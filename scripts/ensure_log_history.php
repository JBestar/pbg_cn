<?php
require dirname(__DIR__) . '/api/_bootstrap.php';
$db = pbg_db();
$sql = "CREATE TABLE IF NOT EXISTS `log_history` (
  `log_fid` int(11) NOT NULL AUTO_INCREMENT,
  `log_mb_uid` varchar(30) NOT NULL,
  `log_emp_fid` int(11) NOT NULL,
  `log_type` int(11) NOT NULL,
  `log_ip` varchar(50) NOT NULL,
  `log_time` datetime NOT NULL,
  PRIMARY KEY (`log_fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
if (!$db->query($sql)) {
    fwrite(STDERR, $db->error . "\n");
    exit(1);
}
$r = $db->query("SHOW TABLES LIKE 'log_history'");
echo ($r && $r->num_rows) ? "log_history OK\n" : "log_history MISSING\n";
