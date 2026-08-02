<?php
require dirname(__DIR__) . '/api/_bootstrap.php';
$m = pbg_get_machine('m01');
$b = pbg_db()->query('SELECT id,state,win_amount FROM bets WHERE id=1')->fetch_assoc();
echo 'balance=' . $m['balance'] . ' bet=' . json_encode($b) . "\n";
