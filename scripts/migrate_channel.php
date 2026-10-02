<?php
/**
 * member.mb_channel (0=게임기, 1=모바일) 추가. 여러 번 실행해도 안전, 회원 데이터는 변경하지 않음.
 *   D:\xampp\php\php.exe D:\xampp\htdocs\pbg_cn\scripts\migrate_channel.php
 */
require dirname(__DIR__) . '/api/_bootstrap.php';

$db = pbg_db();

function mc_col_exists(mysqli $db, $table, $col)
{
    $t = $db->real_escape_string($table);
    $c = $db->real_escape_string($col);
    $r = $db->query("SHOW COLUMNS FROM `{$t}` LIKE '{$c}'");
    return $r && $r->num_rows > 0;
}

function mc_index_exists(mysqli $db, $table, $index)
{
    $t = $db->real_escape_string($table);
    $i = $db->real_escape_string($index);
    $r = $db->query("SHOW INDEX FROM `{$t}` WHERE Key_name = '{$i}'");
    return $r && $r->num_rows > 0;
}

function mc_run(mysqli $db, $sql)
{
    if (!$db->query($sql)) {
        throw new RuntimeException($db->error . ' | ' . substr($sql, 0, 120));
    }
}

echo "Migrating member.mb_channel...\n";

if (!mc_col_exists($db, 'member', 'mb_channel')) {
    mc_run($db, "ALTER TABLE `member` ADD COLUMN `mb_channel` TINYINT NOT NULL DEFAULT 0 AFTER `mb_level`");
    echo "  + column mb_channel added (existing members = 0 / cabinet)\n";
} else {
    echo "  = column mb_channel already exists\n";
}

if (!mc_index_exists($db, 'member', 'idx_level_channel')) {
    mc_run($db, "ALTER TABLE `member` ADD KEY `idx_level_channel` (`mb_level`, `mb_channel`)");
    echo "  + index idx_level_channel added\n";
} else {
    echo "  = index idx_level_channel already exists\n";
}

$res = $db->query("SELECT mb_level, mb_channel, COUNT(*) AS cnt FROM `member` WHERE mb_state_delete = 0 GROUP BY mb_level, mb_channel ORDER BY mb_level, mb_channel");
if ($res instanceof mysqli_result) {
    while ($row = $res->fetch_assoc()) {
        echo "  level=" . $row['mb_level'] . " channel=" . $row['mb_channel'] . " count=" . $row['cnt'] . "\n";
    }
}

echo "Done.\n";
