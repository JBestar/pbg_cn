<?php
$f = dirname(__DIR__) . '/public/assets/vendor/powerball/css/chat_owner_pick.css';
$c = file_get_contents($f);
$c = str_replace('url(/chat/images/', 'url(../chat/images/', $c);
file_put_contents($f, $c);
echo 'rewrote urls: ' . substr_count($c, 'url(../chat/images/') . "\n";
