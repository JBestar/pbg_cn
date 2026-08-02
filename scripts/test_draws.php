<?php
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['action'] = 'draws';
$_GET['limit'] = '3';
require dirname(__DIR__) . '/api/index.php';
