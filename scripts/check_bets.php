<?php
require dirname(__DIR__) . '/api/_bootstrap.php';
$db = pbg_db();
$r = $db->query('SELECT id,round,state,amount,machine_id FROM bets ORDER BY id DESC LIMIT 5');
while ($row = $r->fetch_assoc()) {
    print_r($row);
}
$info = pbg_current_round_info();
echo 'current round=' . $info['round'] . ' can_bet=' . ($info['can_bet'] ? '1' : '0') . "\n";
