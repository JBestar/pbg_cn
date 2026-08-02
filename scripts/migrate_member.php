<?php
/**
 * Apply member hierarchy migration + seed hashed accounts.
 *   D:\xampp\php\php.exe D:\xampp\htdocs\pbg_cn\scripts\migrate_member.php
 */
require dirname(__DIR__) . '/api/_bootstrap.php';

$db = pbg_db();
$cfg = pbg_config();

function col_exists(mysqli $db, $table, $col)
{
    $t = $db->real_escape_string($table);
    $c = $db->real_escape_string($col);
    $r = $db->query("SHOW COLUMNS FROM `{$t}` LIKE '{$c}'");
    return $r && $r->num_rows > 0;
}

function run_sql(mysqli $db, $sql)
{
    if (!$db->query($sql)) {
        throw new RuntimeException($db->error . ' | ' . substr($sql, 0, 120));
    }
}

echo "Migrating pbg_cn member schema...\n";

run_sql($db, "CREATE TABLE IF NOT EXISTS `member` (
  `mb_fid` INT NOT NULL AUTO_INCREMENT,
  `mb_uid` VARCHAR(50) NOT NULL,
  `mb_pwd` VARCHAR(255) NOT NULL,
  `mb_level` INT NOT NULL,
  `mb_emp_fid` INT NOT NULL DEFAULT 0,
  `mb_nickname` VARCHAR(50) NOT NULL DEFAULT '',
  `mb_money` DECIMAL(14,2) UNSIGNED NOT NULL DEFAULT 0,
  `mb_point` DECIMAL(14,2) UNSIGNED NOT NULL DEFAULT 0,
  `mb_lang` VARCHAR(8) NOT NULL DEFAULT 'ko',
  `mb_time_join` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mb_time_last` DATETIME NULL DEFAULT NULL,
  `mb_ip_last` VARCHAR(60) NOT NULL DEFAULT '',
  `mb_game_pb_ratio` FLOAT NOT NULL DEFAULT 0,
  `mb_game_pb_ratio2` FLOAT NOT NULL DEFAULT 0,
  `mb_game_pb_ratio3` FLOAT NOT NULL DEFAULT 0,
  `mb_game_pb_ratio4` FLOAT NOT NULL DEFAULT 0,
  `mb_game_pb_ratio5` FLOAT NOT NULL DEFAULT 0,
  `mb_state_active` INT NOT NULL DEFAULT 1,
  `mb_state_delete` INT NOT NULL DEFAULT 0,
  `mb_limit_round` INT NOT NULL DEFAULT 0,
  `mb_limit_single` INT NOT NULL DEFAULT 0,
  `mb_limit_mix` INT NOT NULL DEFAULT 0,
  `mb_limit_three` INT NOT NULL DEFAULT 0,
  `mb_limit_digit` INT NOT NULL DEFAULT 0,
  `mb_bank_pwd` VARCHAR(255) NOT NULL DEFAULT '',
  `mb_bank_name` VARCHAR(50) NOT NULL DEFAULT '',
  `mb_bank_owner` VARCHAR(50) NOT NULL DEFAULT '',
  `mb_bank_num` VARCHAR(50) NOT NULL DEFAULT '',
  `mb_phone` VARCHAR(50) NOT NULL DEFAULT '',
  `mb_color` VARCHAR(10) NOT NULL DEFAULT '',
  `mb_game` INT NOT NULL DEFAULT 0,
  `mb_rest` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`mb_fid`),
  UNIQUE KEY `uk_mb_uid` (`mb_uid`),
  KEY `idx_emp` (`mb_emp_fid`),
  KEY `idx_level` (`mb_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

if (!col_exists($db, 'member', 'mb_lang')) {
    run_sql($db, "ALTER TABLE `member` ADD COLUMN `mb_lang` VARCHAR(8) NOT NULL DEFAULT 'ko' AFTER `mb_point`");
}

run_sql($db, "CREATE TABLE IF NOT EXISTS `sess_list` (
  `sess_fid` INT NOT NULL AUTO_INCREMENT,
  `sess_id` VARCHAR(200) NOT NULL,
  `sess_mb_fid` INT NOT NULL,
  `sess_mb_uid` VARCHAR(30) NOT NULL,
  `sess_mb_level` INT NOT NULL,
  `sess_emp_fid` INT NOT NULL DEFAULT 0,
  `sess_ip` VARCHAR(60) NOT NULL DEFAULT '',
  `sess_machine_code` VARCHAR(64) NOT NULL DEFAULT '',
  `sess_update_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sess_fid`),
  UNIQUE KEY `uk_sess_id` (`sess_id`),
  KEY `idx_uid` (`sess_mb_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

run_sql($db, "CREATE TABLE IF NOT EXISTS `sessions` (
  `id` VARCHAR(200) NOT NULL,
  `ip_address` VARCHAR(60) NOT NULL,
  `user_agent` VARCHAR(120) NOT NULL,
  `last_activity` INT(10) NOT NULL,
  `data` TEXT DEFAULT NULL,
  `timestamp` BIGINT(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

run_sql($db, "CREATE TABLE IF NOT EXISTS `sessions_admin` (
  `id` VARCHAR(200) NOT NULL,
  `ip_address` VARCHAR(60) NOT NULL,
  `user_agent` VARCHAR(120) NOT NULL,
  `last_activity` INT(10) NOT NULL,
  `data` TEXT DEFAULT NULL,
  `timestamp` BIGINT(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

run_sql($db, "CREATE TABLE IF NOT EXISTS `money_history` (
  `money_fid` INT NOT NULL AUTO_INCREMENT,
  `money_mb_fid` INT NOT NULL,
  `money_mb_uid` VARCHAR(30) NOT NULL,
  `money_mb_emp_fid` INT NOT NULL DEFAULT 0,
  `money_mb_ech_uid` VARCHAR(30) NOT NULL DEFAULT '',
  `money_amount` DECIMAL(14,2) NOT NULL,
  `money_before` DECIMAL(14,2) NOT NULL,
  `money_after` DECIMAL(14,2) NOT NULL,
  `money_change_type` INT NOT NULL,
  `money_bet_round` INT NOT NULL DEFAULT 0,
  `money_bet_mode` INT NOT NULL DEFAULT 0,
  `money_bet_target` VARCHAR(8) NOT NULL DEFAULT '',
  `money_update_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`money_fid`),
  KEY `idx_mb` (`money_mb_fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

run_sql($db, "CREATE TABLE IF NOT EXISTS `member_charge` (
  `charge_fid` INT NOT NULL AUTO_INCREMENT,
  `charge_emp_fid` INT NOT NULL DEFAULT 0,
  `charge_mb_uid` VARCHAR(30) NOT NULL,
  `charge_mb_name` VARCHAR(50) NOT NULL DEFAULT '',
  `charge_type` INT NOT NULL DEFAULT 0,
  `charge_money` DECIMAL(14,2) NOT NULL,
  `charge_time_require` DATETIME NOT NULL,
  `charge_action_state` INT NOT NULL DEFAULT 0,
  `charge_action_uid` VARCHAR(30) NOT NULL DEFAULT '',
  `charge_time_process` DATETIME NULL DEFAULT NULL,
  `charge_money_before` DECIMAL(14,2) NOT NULL DEFAULT 0,
  `charge_money_after` DECIMAL(14,2) NOT NULL DEFAULT 0,
  `charge_state_delete` INT NOT NULL DEFAULT 0,
  `charge_client_delete` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`charge_fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

run_sql($db, "CREATE TABLE IF NOT EXISTS `member_exchange` (
  `exchange_fid` INT NOT NULL AUTO_INCREMENT,
  `exchange_emp_fid` INT NOT NULL DEFAULT 0,
  `exchange_mb_uid` VARCHAR(30) NOT NULL,
  `exchange_type` INT NOT NULL DEFAULT 0,
  `exchange_money` DECIMAL(14,2) NOT NULL,
  `exchange_time_require` DATETIME NOT NULL,
  `exchange_action_state` INT NOT NULL DEFAULT 0,
  `exchange_action_uid` VARCHAR(30) NOT NULL DEFAULT '',
  `exchange_time_process` DATETIME NULL DEFAULT NULL,
  `exchange_bank_name` VARCHAR(30) NOT NULL DEFAULT '',
  `exchange_bank_owner` VARCHAR(30) NOT NULL DEFAULT '',
  `exchange_bank_number` VARCHAR(30) NOT NULL DEFAULT '',
  `exchange_money_before` DECIMAL(14,2) NOT NULL DEFAULT 0,
  `exchange_money_after` DECIMAL(14,2) NOT NULL DEFAULT 0,
  `exchange_state_delete` INT NOT NULL DEFAULT 0,
  `exchange_client_delete` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`exchange_fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

run_sql($db, "CREATE TABLE IF NOT EXISTS `conf_site` (
  `conf_id` INT NOT NULL,
  `conf_memo` VARCHAR(20) NOT NULL DEFAULT '',
  `conf_content` TEXT NOT NULL,
  `conf_active` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`conf_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

run_sql($db, "CREATE TABLE IF NOT EXISTS `log_captcha` (
  `log_fid` BIGINT NOT NULL AUTO_INCREMENT,
  `log_file` VARCHAR(30) NOT NULL,
  `log_sn` VARCHAR(10) NOT NULL,
  `log_type` INT NOT NULL,
  `log_time` DATETIME NOT NULL,
  PRIMARY KEY (`log_fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

run_sql($db, "CREATE TABLE IF NOT EXISTS `log_history` (
  `log_fid` INT NOT NULL AUTO_INCREMENT,
  `log_mb_uid` VARCHAR(30) NOT NULL,
  `log_emp_fid` INT NOT NULL,
  `log_type` INT NOT NULL,
  `log_ip` VARCHAR(50) NOT NULL,
  `log_time` DATETIME NOT NULL,
  PRIMARY KEY (`log_fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

run_sql($db, "INSERT INTO `conf_site` (`conf_id`, `conf_memo`, `conf_content`, `conf_active`) VALUES
  (1, '사이트명', 'pbg_cn', 0),
  (2, '도메인명', 'localhost', 0)
ON DUPLICATE KEY UPDATE `conf_memo`=VALUES(`conf_memo`)");

if (!col_exists($db, 'machines', 'last_mb_uid')) {
    run_sql($db, "ALTER TABLE `machines` ADD COLUMN `last_mb_uid` VARCHAR(50) NOT NULL DEFAULT '' AFTER `name`");
}
if (!col_exists($db, 'bets', 'mb_fid')) {
    run_sql($db, "ALTER TABLE `bets` ADD COLUMN `mb_fid` INT NOT NULL DEFAULT 0 AFTER `id`");
}
if (!col_exists($db, 'bets', 'mb_uid')) {
    run_sql($db, "ALTER TABLE `bets` ADD COLUMN `mb_uid` VARCHAR(50) NOT NULL DEFAULT '' AFTER `mb_fid`");
}
if (!col_exists($db, 'bets', 'emp_fid')) {
    run_sql($db, "ALTER TABLE `bets` ADD COLUMN `emp_fid` INT NOT NULL DEFAULT 0 AFTER `mb_uid`");
}
if (!col_exists($db, 'money_log', 'mb_fid')) {
    run_sql($db, "ALTER TABLE `money_log` ADD COLUMN `mb_fid` INT NOT NULL DEFAULT 0 AFTER `id`");
}
if (!col_exists($db, 'money_log', 'mb_uid')) {
    run_sql($db, "ALTER TABLE `money_log` ADD COLUMN `mb_uid` VARCHAR(50) NOT NULL DEFAULT '' AFTER `mb_fid`");
}

// Seed: admin(9), agency(8), store(7) — passwords hashed
$seeds = [
    // uid, plain, level, emp_fid, nick, money
    ['admin', 'admin123', 9, 0, '관리자', 0],
    ['agency01', 'agency123', 8, 0, '총판01', 100000],
    ['store01', 'store123', 7, null, '매장01', 10000], // emp_fid filled after agency insert
];

function upsert_member(mysqli $db, $uid, $plain, $level, $empFid, $nick, $money)
{
    $hash = password_hash($plain, PASSWORD_DEFAULT);
    $bankHash = password_hash($plain, PASSWORD_DEFAULT);
    $stmt = $db->prepare('SELECT mb_fid FROM member WHERE mb_uid=? LIMIT 1');
    $stmt->bind_param('s', $uid);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($row) {
        $fid = (int)$row['mb_fid'];
        $u = $db->prepare('UPDATE member SET mb_pwd=?, mb_bank_pwd=?, mb_level=?, mb_emp_fid=?, mb_nickname=?, mb_money=?, mb_lang=\'ko\', mb_state_active=1, mb_state_delete=0 WHERE mb_fid=?');
        $u->bind_param('ssiisdi', $hash, $bankHash, $level, $empFid, $nick, $money, $fid);
        $u->execute();
        $u->close();
        return $fid;
    }
    $ins = $db->prepare('INSERT INTO member (mb_uid, mb_pwd, mb_level, mb_emp_fid, mb_nickname, mb_money, mb_point, mb_lang, mb_state_active, mb_bank_pwd) VALUES (?,?,?,?,?,?,0,\'ko\',1,?)');
    $ins->bind_param('ssiisds', $uid, $hash, $level, $empFid, $nick, $money, $bankHash);
    $ins->execute();
    $fid = (int)$ins->insert_id;
    $ins->close();
    return $fid;
}

$adminFid = upsert_member($db, 'admin', 'admin123', 9, 0, '관리자', 0);
$agencyFid = upsert_member($db, 'agency01', 'agency123', 8, 0, '총판01', 100000);
$storeMoney = 10000.0;
$m = $db->query("SELECT balance FROM machines WHERE code='m01' LIMIT 1");
if ($m && ($mr = $m->fetch_assoc())) {
    $storeMoney = (float)$mr['balance'];
}
$storeFid = upsert_member($db, 'store01', 'store123', 7, $agencyFid, '매장01', $storeMoney);

// Link old bets without mb_fid to store01 if any
$db->query("UPDATE bets SET mb_fid={$storeFid}, mb_uid='store01', emp_fid={$agencyFid} WHERE mb_fid=0 OR mb_fid IS NULL");

echo "OK admin_fid={$adminFid} agency_fid={$agencyFid} store_fid={$storeFid}\n";
echo "Logins (hashed):\n";
echo "  admin / admin123 (level 9)\n";
echo "  agency01 / agency123 (level 8)\n";
echo "  store01 / store123 (level 7, money={$storeMoney})\n";
echo "Done.\n";
