<?php
require dirname(__DIR__) . '/api/_bootstrap.php';
require_once dirname(__DIR__) . '/api/settle_lib.php';

echo "fetch_url=" . pbg_config()['powerball_fetch_url'] . "\n";
$info = pbg_current_round_info();
echo "before last_round={$info['last_round']} remain={$info['remain_seconds']}\n";

$r = pbg_fetch_powerball_draw(true);
echo json_encode($r, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";

$info2 = pbg_current_round_info();
echo "after last_round={$info2['last_round']}\n";
