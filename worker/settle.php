<?php
/**
 * Settlement + draw-fetch worker — run via Task Scheduler or:
 *   D:\xampp\php\php.exe D:\xampp\htdocs\pbg_cn\worker\settle.php --loop
 */
require dirname(__DIR__) . '/api/_bootstrap.php';
require_once dirname(__DIR__) . '/api/settle_lib.php';

$loop = in_array('--loop', $argv, true);
$interval = 2;

do {
    $ts = date('Y-m-d H:i:s');
    $info = pbg_current_round_info();
    $remain = (int)$info['remain_seconds'];

    $fetchNote = '';
    if (pbg_in_draw_fetch_window($remain)) {
        $result = pbg_fetch_powerball_draw(false);
        if (!empty($result['fetched'])) {
            $r = isset($result['draw']['round']) ? $result['draw']['round'] : '-';
            $fetchNote = " fetch_round={$r}";
            if ($result['error'] !== '') {
                $fetchNote .= ' fetch_err=' . $result['error'];
            }
        } elseif (!empty($result['skipped'])) {
            $fetchNote = ' fetch_skip=' . $result['skipped'];
        } elseif ($result['error'] !== '') {
            $fetchNote = ' fetch_err=' . $result['error'];
        }
    }

    $n = pbg_settle_pending_lib();
    if ($n > 0 || $fetchNote !== '' || !$loop) {
        echo "[{$ts}] remain={$remain} settled={$n}{$fetchNote}\n";
    }
    if ($loop) {
        sleep($interval);
    }
} while ($loop);
