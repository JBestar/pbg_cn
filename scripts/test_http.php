<?php
$base = 'http://localhost:83/pbg_cn/api/index.php';

function req($url, $post = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if ($post !== null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
    }
    $out = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    echo "HTTP $code $err\n$out\n\n";
    return json_decode($out, true);
}

$status = req($base . '?action=status&mid=m01');
$round = $status['data']['round']['round'];
req($base . '?action=bet', [
    'mid' => 'm01',
    'mode' => 1,
    'amount' => 100,
    'round' => $round,
]);
req($base . '?action=history&mid=m01&limit=2');

// mini view check
$ch = curl_init('http://localhost:83/powerball/public/?view=powerballMiniView&openMini=1');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$html = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "mini view HTTP $code len=" . strlen($html) . "\n";

$ch = curl_init('http://powerball.com:82/?view=powerballMiniView&openMini=1');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$html = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);
echo "powerball.com:82 HTTP $code err=$err len=" . strlen($html) . "\n";
