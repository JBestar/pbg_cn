<?php
$ch = curl_init('http://127.0.0.1:83/pbg_cn/api/index.php?action=fetch_draw&force=1');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$out = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);
echo "HTTP $code $err\n$out\n";
