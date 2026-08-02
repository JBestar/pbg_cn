<?php
/**
 * Shared settle helpers for CLI workers (avoid loading full api/index.php router).
 */
function pbg_settle_pending_lib()
{
    $db = pbg_db();
    $drawDb = pbg_db('draw_db');
    $res = $db->query('SELECT DISTINCT round FROM bets WHERE state=1 ORDER BY round ASC');
    $rounds = [];
    while ($r = $res->fetch_assoc()) {
        $rounds[] = (int)$r['round'];
    }
    $settled = 0;
    foreach ($rounds as $round) {
        $stmt = $drawDb->prepare('SELECT * FROM draw_results WHERE round=? LIMIT 1');
        $stmt->bind_param('i', $round);
        $stmt->execute();
        $draw = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$draw) {
            continue;
        }
        $cls = pbg_classify_draw($draw['powerball'], $draw['ball_sum']);
        $settled += pbg_settle_round_lib($round, $cls);
    }
    return $settled;
}

function pbg_settle_round_lib($round, $cls)
{
    $db = pbg_db();
    $stmt = $db->prepare('SELECT * FROM bets WHERE round=? AND state=1');
    $stmt->bind_param('i', $round);
    $stmt->execute();
    $res = $stmt->get_result();
    $bets = [];
    while ($r = $res->fetch_assoc()) {
        $bets[] = $r;
    }
    $stmt->close();
    $count = 0;
    foreach ($bets as $bet) {
        $db->begin_transaction();
        try {
            $bid = (int)$bet['id'];
            $chk = $db->prepare('SELECT * FROM bets WHERE id=? AND state=1 FOR UPDATE');
            $chk->bind_param('i', $bid);
            $chk->execute();
            $row = $chk->get_result()->fetch_assoc();
            $chk->close();
            if (!$row) {
                $db->rollback();
                continue;
            }

            $win = pbg_is_win($row['mode'], $row['target'], $cls);
            $resultStr = $cls['result_1'] . $cls['result_2'] . $cls['result_3'] . $cls['result_4'];
            if ($win) {
                $winMoney = (int)((float)$row['amount'] * (float)$row['ratio']);
                $state = 3;
            } else {
                $winMoney = 0;
                $state = 2;
            }

            $upd = $db->prepare('UPDATE bets SET state=?, win_amount=?, result=?, settled_at=NOW() WHERE id=?');
            $upd->bind_param('idsi', $state, $winMoney, $resultStr, $bid);
            $upd->execute();
            $upd->close();

            if ($win && $winMoney > 0) {
                $mbFid = (int)$row['mb_fid'];
                $mbUid = (string)$row['mb_uid'];
                if ($mbFid > 0) {
                    $lock = $db->prepare('SELECT mb_money FROM member WHERE mb_fid=? FOR UPDATE');
                    $lock->bind_param('i', $mbFid);
                    $lock->execute();
                    $bal = (float)$lock->get_result()->fetch_assoc()['mb_money'];
                    $lock->close();
                    $after = $bal + $winMoney;
                    $um = $db->prepare('UPDATE member SET mb_money=? WHERE mb_fid=?');
                    $um->bind_param('di', $after, $mbFid);
                    $um->execute();
                    $um->close();
                    pbg_money_log((int)$row['machine_id'], $bid, 2, $winMoney, $bal, $after, 'win', $mbFid, $mbUid);
                } else {
                    $mid = (int)$row['machine_id'];
                    $lock = $db->prepare('SELECT balance FROM machines WHERE id=? FOR UPDATE');
                    $lock->bind_param('i', $mid);
                    $lock->execute();
                    $bal = (float)$lock->get_result()->fetch_assoc()['balance'];
                    $lock->close();
                    $after = $bal + $winMoney;
                    $um = $db->prepare('UPDATE machines SET balance=? WHERE id=?');
                    $um->bind_param('di', $after, $mid);
                    $um->execute();
                    $um->close();
                    pbg_money_log($mid, $bid, 2, $winMoney, $bal, $after, 'win');
                }
            }

            $db->commit();
            $count++;
        } catch (Throwable $e) {
            $db->rollback();
            fwrite(STDERR, $e->getMessage() . PHP_EOL);
        }
    }
    return $count;
}
