<?php
require dirname(__DIR__) . '/api/_bootstrap.php';

// Simulate POST bet
$_SERVER['REQUEST_METHOD'] = 'POST';
$payload = json_encode(['mid' => 'm01', 'mode' => 1, 'amount' => 100, 'round' => 0]);
file_put_contents('php://memory', ''); // noop

// Call functions directly
$cfg = pbg_config();
$machine = pbg_get_machine('m01');
$conf = pbg_get_conf();
$meta = pbg_mode_meta(1, $conf);
$roundInfo = pbg_current_round_info();
echo "Can bet: " . ($roundInfo['can_bet'] ? 'yes' : 'no') . " round=" . $roundInfo['round'] . "\n";
echo "Meta: " . json_encode($meta) . "\n";
echo "Balance before: " . $machine['balance'] . "\n";

// Use HTTP-less bet via including with mocked input is hard; use mysqli directly for smoke or curl
$url = 'http://localhost/pbg_cn/api/index.php?action=bet';
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS => json_encode([
        'mid' => 'm01',
        'mode' => 1,
        'amount' => 100,
        'round' => $roundInfo['round'],
    ]),
    CURLOPT_RETURNTRANSFER => true,
]);
$out = curl_exec($ch);
$err = curl_error($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "HTTP $code err=$err\n$out\n";

$ch = curl_init('http://localhost/pbg_cn/api/index.php?action=history&mid=m01&limit=3');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
echo "history: " . curl_exec($ch) . "\n";
curl_close($ch);
