<?php
/**
 * Fetch draw from local powerball + settle — run via Task Scheduler or:
 *   D:\xampp\php\php.exe D:\xampp\htdocs\pbg_cn\worker\fetch_draw.php --loop
 *
 * Timing mirrors lion reground: every 5-min boundary (sec 0–50) and near countdown end.
 */
require dirname(__DIR__) . '/api/_bootstrap.php';
require_once dirname(__DIR__) . '/api/settle_lib.php';

$loop = in_array('--loop', $argv, true);
$forceOnce = in_array('--force', $argv, true);
$interval = 2;

do {
    $ts = date('Y-m-d H:i:s');
    $info = pbg_current_round_info();
    $remain = (int)$info['remain_seconds'];
    $inWindow = $forceOnce || pbg_in_draw_fetch_window($remain);

    if ($inWindow || $forceOnce) {
        $result = pbg_fetch_powerball_draw($forceOnce);
        $settled = pbg_settle_pending_lib();
        $forceOnce = false;

        $round = isset($result['draw']['round']) ? $result['draw']['round'] : '-';
        $fetched = !empty($result['fetched']) ? 'yes' : 'no';
        $skip = isset($result['skipped']) ? $result['skipped'] : '';
        $err = $result['error'] !== '' ? (' err=' . $result['error']) : '';
        echo "[{$ts}] remain={$remain} fetched={$fetched} skip={$skip} round={$round} settled={$settled}{$err}\n";
    } elseif (!$loop) {
        echo "[{$ts}] outside draw window (remain={$remain}), use --force or --loop\n";
    }

    if ($loop) {
        sleep($interval);
    }
} while ($loop);
