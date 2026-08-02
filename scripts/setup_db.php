<?php
/**
 * Create / apply schema using credentials from project .env
 *   D:\xampp\php\php.exe D:\xampp\htdocs\pbg_cn\scripts\setup_db.php
 */
$cfg = require dirname(__DIR__) . '/config/app.php';
$db = $cfg['db'];

$m = new mysqli($db['host'], $db['user'], $db['pass']);
if ($m->connect_error) {
    fwrite(STDERR, $m->connect_error . PHP_EOL);
    exit(1);
}
$m->set_charset($db['charset']);

$sqlFile = dirname(__DIR__) . '/sql/schema.sql';
$sql = file_get_contents($sqlFile);
if ($sql === false) {
    fwrite(STDERR, "Cannot read schema.sql\n");
    exit(1);
}

if (!$m->multi_query($sql)) {
    fwrite(STDERR, $m->error . PHP_EOL);
    exit(1);
}

do {
    if ($res = $m->store_result()) {
        $res->free();
    }
} while ($m->more_results() && $m->next_result());

if ($m->errno) {
    fwrite(STDERR, $m->error . PHP_EOL);
    exit(1);
}

$drawName = $cfg['draw_db']['name'];
$chk = $m->query('SELECT round, powerball, ball_sum FROM `' . $m->real_escape_string($drawName) . '`.draw_results ORDER BY round DESC LIMIT 1');
$row = $chk ? $chk->fetch_assoc() : null;
echo "DB setup OK\n";
echo "Latest draw: " . json_encode($row, JSON_UNESCAPED_UNICODE) . "\n";
