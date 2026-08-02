<?php
$ctx = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n",
        'content' => json_encode(['uid' => 'store01', 'pwd' => 'store123', 'machine' => 'm01']),
    ],
]);
echo file_get_contents('http://127.0.0.1:83/pbg_cn/api/index.php?action=login', false, $ctx);
echo "\n";
