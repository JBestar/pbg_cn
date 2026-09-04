<?php 
namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

/**
 * Admin bet list — reads terminal `bets` table (+ powerball.draw_results).
 * Returns tiger-compatible field names for existing bet_list.js.
 */
class PbBet_Model extends Model {

    private $mDb;
    private $mBuilder;
    private $mTbName = "bets";
    private $mTbColumn;
    private $drawDbName = 'powerball';

    function __construct()
    {
        $this->mDb      = Database::connect();
        $this->mBuilder = $this->mDb->table($this->mTbName);
        $this->mTbColumn = [];
    }

    /** Terminal mode (1–16) → admin display mode for getBetTypeTextOrg */
    /** Terminal state 1 wait / 2 lose / 3 win / 4 cancel → admin 0/1/2/3 */
    private function mapStateToAdmin($state)
    {
        $state = (int)$state;
        $map = [1 => BET_WAIT, 2 => BET_LOSS, 3 => BET_WIN, 4 => BET_CANCEL];
        return isset($map[$state]) ? $map[$state] : BET_WAIT;
    }

    private function buildWhere($arrRqData)
    {
        $where = " b.id > 0 ";
        // Powerball-only admin filter (game=0). Ignore coin/bogle.
        if (isset($arrRqData['game']) && intval($arrRqData['game']) >= 0 && intval($arrRqData['game']) != GAME_POWER_BALL) {
            $where .= " AND 1=0 ";
            return $where;
        }
        if (!empty($arrRqData['start'])) {
            $where .= " AND b.created_at >= '" . $this->mDb->escapeString($arrRqData['start']) . "' ";
        }
        if (!empty($arrRqData['end'])) {
            $where .= " AND b.created_at <= '" . $this->mDb->escapeString($arrRqData['end']) . " 23:59:59' ";
        }
        if (isset($arrRqData['round_id']) && strlen((string)$arrRqData['round_id']) > 0) {
            $where .= " AND b.round = '" . $this->mDb->escapeString($arrRqData['round_id']) . "' ";
        }
        if (!empty($arrRqData['mb_uid'])) {
            $where .= " AND b.mb_uid = '" . $this->mDb->escapeString($arrRqData['mb_uid']) . "' ";
        }
        if (array_key_exists('mb_emp_fid', $arrRqData)) {
            $where .= " AND b.emp_fid = '" . intval($arrRqData['mb_emp_fid']) . "' ";
        }
        return $where;
    }

    private function classifyDraw($pb, $sum)
    {
        $pb = (int)$pb;
        $sum = (int)$sum;
        $r1 = ($pb % 2) ? 'P' : 'B';
        $r2 = ($pb < 5) ? 'P' : 'B';
        $r3 = ($sum % 2) ? 'P' : 'B';
        $r4 = ($sum <= 72) ? 'P' : 'B';
        if ($sum <= 64) {
            $r5 = 'S';
        } elseif ($sum <= 80) {
            $r5 = 'M';
        } else {
            $r5 = 'L';
        }
        return [$r1, $r2, $r3, $r4, $r5];
    }

    private function mapRow($row)
    {
        $o = new \stdClass();
        $o->bet_fid = (int)$row['id'];
        $o->bet_state = $this->mapStateToAdmin($row['state']);
        $o->bet_emp_fid = (int)$row['emp_fid'];
        $o->bet_mb_uid = $row['mb_uid'];
        $o->bet_mb_name = '';
        $o->bet_round_fid = (int)$row['round'];
        $o->bet_round_no = (int)$row['round'];
        $o->bet_round_date = substr((string)$row['created_at'], 0, 10);
        $o->bet_time = $row['created_at'];
        $o->bet_game = GAME_POWER_BALL;
        $o->bet_mode = (int)$row['mode'];
        $o->bet_target = $row['target'];
        $o->bet_ratio = $row['ratio'];
        $o->bet_money = (float)$row['amount'];
        $o->bet_win_money = (float)$row['win_amount'];
        $o->bet_before_money = (float)$row['before_money'];
        $o->bet_after_money = (float)$row['after_money'];
        $o->mb_uid = $row['mb_uid'];
        $o->mb_nickname = isset($row['mb_nickname']) ? $row['mb_nickname'] : '';
        $o->mb_point = isset($row['mb_point']) ? (float)$row['mb_point'] : 0;
        $o->mb_ip_last = isset($row['mb_ip_last']) ? $row['mb_ip_last'] : '';
        $o->mb_emp_nickname = isset($row['mb_emp_nickname']) ? $row['mb_emp_nickname'] : '';
        $o->mb_emp_fid = (int)$row['emp_fid'];

        if (!empty($row['draw_round'])) {
            $o->round_fid = (int)$row['draw_round'];
            $balls = [
                (int)$row['ball1'], (int)$row['ball2'], (int)$row['ball3'],
                (int)$row['ball4'], (int)$row['ball5'],
            ];
            $o->round_normal = implode(',', $balls);
            $o->round_power = (int)$row['powerball'];
            $cls = $this->classifyDraw($row['powerball'], $row['ball_sum']);
            $o->round_result_1 = $cls[0];
            $o->round_result_2 = $cls[1];
            $o->round_result_3 = $cls[2];
            $o->round_result_4 = $cls[3];
            $o->round_result_5 = $cls[4];
        } else {
            $o->round_fid = null;
            $o->round_normal = null;
            $o->round_power = null;
            $o->round_result_1 = null;
            $o->round_result_2 = null;
            $o->round_result_3 = null;
            $o->round_result_4 = null;
            $o->round_result_5 = null;
        }
        return $o;
    }

    public function gets($count)
    {
        try {
            $sql = "SELECT b.*, m.mb_nickname, m.mb_ip_last, emp.mb_nickname AS mb_emp_nickname,
                    d.round AS draw_round, d.ball1, d.ball2, d.ball3, d.ball4, d.ball5, d.powerball, d.ball_sum
                    FROM bets b
                    LEFT JOIN member m ON m.mb_uid = b.mb_uid
                    LEFT JOIN member emp ON emp.mb_fid = b.emp_fid
                    LEFT JOIN `{$this->drawDbName}`.draw_results d ON d.round = b.round
                    ORDER BY b.id DESC LIMIT " . (int)$count;
            $q = $this->mDb->query($sql);
            $out = [];
            foreach ($q->getResultArray() as $row) {
                $out[] = $this->mapRow($row);
            }
            return $out;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getByFid($bet_fid)
    {
        try {
            $sql = "SELECT b.*, m.mb_nickname, m.mb_ip_last, emp.mb_nickname AS mb_emp_nickname,
                    d.round AS draw_round, d.ball1, d.ball2, d.ball3, d.ball4, d.ball5, d.powerball, d.ball_sum
                    FROM bets b
                    LEFT JOIN member m ON m.mb_uid = b.mb_uid
                    LEFT JOIN member emp ON emp.mb_fid = b.emp_fid
                    LEFT JOIN `{$this->drawDbName}`.draw_results d ON d.round = b.round
                    WHERE b.id = " . (int)$bet_fid . " LIMIT 1";
            $row = $this->mDb->query($sql)->getRowArray();
            return $row ? $this->mapRow($row) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getByRoundId($round_id, $mb_uid = "")
    {
        try {
            $where = " b.round = '" . $this->mDb->escapeString($round_id) . "' ";
            if (strlen($mb_uid) > 0) {
                $where .= " AND b.mb_uid = '" . $this->mDb->escapeString($mb_uid) . "' ";
            }
            $sql = "SELECT b.*, m.mb_nickname, m.mb_ip_last, emp.mb_nickname AS mb_emp_nickname,
                    d.round AS draw_round, d.ball1, d.ball2, d.ball3, d.ball4, d.ball5, d.powerball, d.ball_sum
                    FROM bets b
                    LEFT JOIN member m ON m.mb_uid = b.mb_uid
                    LEFT JOIN member emp ON emp.mb_fid = b.emp_fid
                    LEFT JOIN `{$this->drawDbName}`.draw_results d ON d.round = b.round
                    WHERE {$where} ORDER BY b.id ASC";
            $out = [];
            foreach ($this->mDb->query($sql)->getResultArray() as $row) {
                $out[] = $this->mapRow($row);
            }
            return $out;
        } catch (\Exception $e) {
            return null;
        }
    }

    function registerBet($arrBetData, $objMember, $arrEmpRatio)
    {
        return 0;
    }

    function cancelBet($objBet, $objMember)
    {
        return false;
    }

    function updateBet($arrRqData)
    {
        return false;
    }

    function updateBetObj($objBetInfo)
    {
        return false;
    }

    function searchCount($arrRqData)
    {
        try {
            $where = $this->buildWhere($arrRqData);
            $sql = "SELECT COUNT(*) AS cnt FROM bets b WHERE " . $where;
            $row = $this->mDb->query($sql)->getRow();
            return $row ? (int)$row->cnt : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    function searchList($arrRqData, $page, $cntPer = 20)
    {
        try {
            if ($page < 1 || $cntPer < 1) {
                return null;
            }
            $where = $this->buildWhere($arrRqData);
            $nStartRow = ($page - 1) * $cntPer;
            $sql = "SELECT b.*, m.mb_nickname, m.mb_point, m.mb_ip_last, emp.mb_nickname AS mb_emp_nickname,
                    d.round AS draw_round, d.ball1, d.ball2, d.ball3, d.ball4, d.ball5, d.powerball, d.ball_sum
                    FROM bets b
                    LEFT JOIN member m ON m.mb_uid = b.mb_uid
                    LEFT JOIN member emp ON emp.mb_fid = b.emp_fid
                    LEFT JOIN `{$this->drawDbName}`.draw_results d ON d.round = b.round
                    WHERE {$where}
                    ORDER BY b.id DESC
                    LIMIT {$nStartRow}, " . (int)$cntPer;
            $out = [];
            foreach ($this->mDb->query($sql)->getResultArray() as $row) {
                $out[] = $this->mapRow($row);
            }
            return $out;
        } catch (\Exception $e) {
            return null;
        }
    }

    function getAccByRound($arrRqData)
    {
        try {
            $where = $this->buildWhere($arrRqData);
            $sql = "SELECT CAST(b.created_at AS DATE) AS bet_date,
                    b.round AS bet_round_fid,
                    b.round AS bet_round_no,
                    COUNT(b.id) AS bet_count,
                    SUM(b.amount) AS bet_sum,
                    SUM(b.win_amount) AS win_sum,
                    0 AS point_sum
                    FROM bets b
                    WHERE {$where}
                    GROUP BY CAST(b.created_at AS DATE), b.round
                    ORDER BY b.round DESC";
            return $this->mDb->query($sql)->getResult();
        } catch (\Exception $e) {
            return [];
        }
    }

    function getMemberByRound($arrRqData)
    {
        try {
            $where = " b.created_at >= '" . $this->mDb->escapeString($arrRqData['round_start']) . "' ";
            $where .= " AND b.created_at <= '" . $this->mDb->escapeString($arrRqData['round_end']) . "' ";
            if (array_key_exists('mb_emp_fid', $arrRqData)) {
                $where .= " AND b.emp_fid = '" . intval($arrRqData['mb_emp_fid']) . "' ";
            }
            $sql = "SELECT m.mb_fid, b.mb_uid AS bet_mb_uid
                    FROM bets b
                    JOIN member m ON m.mb_uid = b.mb_uid
                    WHERE {$where}
                    GROUP BY b.mb_uid
                    ORDER BY MAX(b.id) DESC";
            return $this->mDb->query($sql)->getResult();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * 배팅내역 매장 단위 합계. 포인트 = member.mb_point, 총판포인트 = 상위 총판 mb_point.
     */
    function getStoreBetSummary($arrRqData)
    {
        try {
            $whereMember = " m.mb_level = " . LEVEL_EMPLOYEE . " AND m.mb_state_delete = 0 ";
            if (array_key_exists('mb_emp_fid', $arrRqData)) {
                $whereMember .= " AND m.mb_emp_fid = '" . intval($arrRqData['mb_emp_fid']) . "' ";
            }
            if (!empty($arrRqData['mb_uid'])) {
                $whereMember .= " AND m.mb_uid = '" . $this->mDb->escapeString($arrRqData['mb_uid']) . "' ";
            }

            $whereBet = " b.state IN (2, 3) ";
            if (!empty($arrRqData['start'])) {
                $whereBet .= " AND b.created_at >= '" . $this->mDb->escapeString($arrRqData['start']) . "' ";
            }
            if (!empty($arrRqData['end'])) {
                $whereBet .= " AND b.created_at <= '" . $this->mDb->escapeString($arrRqData['end']) . " 23:59:59' ";
            }
            if (isset($arrRqData['round_id']) && strlen((string)$arrRqData['round_id']) > 0) {
                $whereBet .= " AND b.round = '" . $this->mDb->escapeString($arrRqData['round_id']) . "' ";
            }

            $sql = "SELECT m.mb_fid, m.mb_uid, m.mb_nickname, m.mb_money, m.mb_point,
                    IFNULL(agen.mb_point, 0) AS agen_point,
                    IFNULL(bt.bet_sum, 0) AS bet_sum,
                    IFNULL(bt.win_sum, 0) AS win_sum,
                    IFNULL(bt.win_rounds, 0) AS win_rounds
                    FROM member m
                    LEFT JOIN member agen ON agen.mb_fid = m.mb_emp_fid
                    LEFT JOIN (
                        SELECT mb_uid,
                               SUM(amount) AS bet_sum,
                               SUM(win_amount) AS win_sum,
                               COUNT(DISTINCT CASE WHEN state = 3 THEN round END) AS win_rounds
                        FROM bets b
                        WHERE {$whereBet}
                        GROUP BY mb_uid
                    ) bt ON bt.mb_uid = m.mb_uid
                    WHERE {$whereMember}
                    ORDER BY m.mb_uid ASC";
            return $this->mDb->query($sql)->getResult();
        } catch (\Exception $e) {
            return [];
        }
    }
}
