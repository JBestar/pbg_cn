<?php 
namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

/**
 * Game results — powerball.draw_results + pbg_cn.bets (subtree via emp_fid).
 */
class PbRound_Model extends Model {

    protected $returnType = 'object';
    private $mDb;
    private $drawDbName = 'powerball';
    private $gameId = GAME_POWER_BALL;

    public function __construct()
    {
        $this->mDb = Database::connect();
    }

    public function setType($gameId)
    {
        $this->gameId = (int)$gameId;
    }

    private function dateWhere($arrRqData, $alias = 'd')
    {
        $where = " {$alias}.round > 0 ";
        if (!empty($arrRqData['start'])) {
            $where .= " AND DATE({$alias}.drawn_at) >= '" . $this->mDb->escapeString($arrRqData['start']) . "' ";
        }
        if (!empty($arrRqData['end'])) {
            $where .= " AND DATE({$alias}.drawn_at) <= '" . $this->mDb->escapeString($arrRqData['end']) . "' ";
        }
        if (isset($arrRqData['round_id']) && strlen((string)$arrRqData['round_id']) > 0) {
            $rid = $this->mDb->escapeString($arrRqData['round_id']);
            $where .= " AND ({$alias}.round = '{$rid}' OR {$alias}.daily_round = '{$rid}') ";
        }
        return $where;
    }

    private function betJoinSql($arrRqData)
    {
        $bw = " state IN (2, 3) ";
        if (array_key_exists('mb_emp_fid', $arrRqData)) {
            $bw .= " AND emp_fid = '" . intval($arrRqData['mb_emp_fid']) . "' ";
        }
        if (!empty($arrRqData['store_uid'])) {
            $bw .= " AND mb_uid = '" . $this->mDb->escapeString($arrRqData['store_uid']) . "' ";
        }
        return " LEFT JOIN (
            SELECT round AS bet_round_fid,
                   COUNT(id) AS bet_count,
                   SUM(amount) AS bet_sum,
                   SUM(win_amount) AS win_sum,
                   0 AS empl_sum,
                   0 AS agen_sum
            FROM bets
            WHERE {$bw}
            GROUP BY round
        ) AS bet_acc ON bet_acc.bet_round_fid = d.round ";
    }

    private function classify($pb, $sum)
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
        $o->round_fid = (int)$row['round'];
        $o->round_num = (int)$row['daily_round'];
        $o->round_hash = null;
        $o->round_state = 1;
        $balls = [
            (int)$row['ball1'], (int)$row['ball2'], (int)$row['ball3'],
            (int)$row['ball4'], (int)$row['ball5'],
        ];
        $o->round_normal = implode(',', $balls);
        $o->round_power = (int)$row['powerball'];
        $o->round_time = $row['drawn_at'];
        $cls = $this->classify($row['powerball'], $row['ball_sum']);
        $o->round_result_1 = $cls[0];
        $o->round_result_2 = $cls[1];
        $o->round_result_3 = $cls[2];
        $o->round_result_4 = $cls[3];
        $o->round_result_5 = $cls[4];
        if (!empty($row['bet_round_fid'])) {
            $o->bet_round_fid = (int)$row['bet_round_fid'];
            $o->bet_count = (int)$row['bet_count'];
            $o->bet_sum = (float)$row['bet_sum'];
            $o->win_sum = (float)$row['win_sum'];
            $o->empl_sum = (float)$row['empl_sum'];
            $o->agen_sum = (float)$row['agen_sum'];
        } else {
            $o->bet_round_fid = null;
            $o->bet_count = 0;
            $o->bet_sum = 0;
            $o->win_sum = 0;
            $o->empl_sum = 0;
            $o->agen_sum = 0;
        }
        return $o;
    }

    public function searchCount($arrRqData)
    {
        if ((int)$this->gameId !== GAME_POWER_BALL) {
            return 0;
        }
        try {
            $where = $this->dateWhere($arrRqData, 'd');
            $sql = "SELECT COUNT(*) AS cnt FROM `{$this->drawDbName}`.draw_results d WHERE {$where}";
            $row = $this->mDb->query($sql)->getRow();
            return $row ? (int)$row->cnt : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function searchList($arrRqData, $page, $cntPer = 20)
    {
        if ((int)$this->gameId !== GAME_POWER_BALL) {
            return [];
        }
        if ($page < 1 || $cntPer < 1) {
            return null;
        }
        try {
            $where = $this->dateWhere($arrRqData, 'd');
            $nStart = ($page - 1) * $cntPer;
            $sql = "SELECT d.*, bet_acc.bet_round_fid, bet_acc.bet_count, bet_acc.bet_sum, bet_acc.win_sum, bet_acc.empl_sum, bet_acc.agen_sum
                    FROM `{$this->drawDbName}`.draw_results d "
                . $this->betJoinSql($arrRqData)
                . " WHERE {$where}
                    ORDER BY d.round DESC
                    LIMIT {$nStart}, " . (int)$cntPer;
            $out = [];
            foreach ($this->mDb->query($sql)->getResultArray() as $row) {
                $out[] = $this->mapRow($row);
            }
            return $out;
        } catch (\Exception $e) {
            return [];
        }
    }
}
