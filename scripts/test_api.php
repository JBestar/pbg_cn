<?php
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['action'] = 'status';
$_GET['mid'] = 'm01';
ob_start();
require dirname(__DIR__) . '/api/index.php';
