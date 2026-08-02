<?php
$ch = curl_init('http://localhost:83/pbg_cn/api/index.php?action=cancel');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS => json_encode(['mid' => 'm01']),
    CURLOPT_RETURNTRANSFER => true,
]);
echo curl_exec($ch), "\n";
