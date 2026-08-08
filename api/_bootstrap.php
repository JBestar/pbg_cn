<?php
/**
 * Shared bootstrap for API / worker
 */

function pbg_config()
{
    static $cfg;
    if (!$cfg) {
        $cfg = require dirname(__DIR__) . '/config/app.php';
        date_default_timezone_set($cfg['timezone']);
    }
    return $cfg;
}

function pbg_db($which = 'db')
{
    static $pool = [];
    if (isset($pool[$which])) {
        return $pool[$which];
    }
    $c = pbg_config()[$which];
    $mysqli = new mysqli($c['host'], $c['user'], $c['pass'], $c['name']);
    if ($mysqli->connect_error) {
        throw new RuntimeException('DB connect failed: ' . $mysqli->connect_error);
    }
    $mysqli->set_charset($c['charset']);
    $pool[$which] = $mysqli;
    return $mysqli;
}

/**
 * Safe mysqli_stmt::get_result — returns null instead of false.
 * @return mysqli_result|null
 */
function pbg_stmt_result($stmt)
{
    if (!($stmt instanceof mysqli_stmt)) {
        return null;
    }
    $res = $stmt->get_result();
    return ($res instanceof mysqli_result) ? $res : null;
}

/**
 * @return array<string,mixed>|null
 */
function pbg_stmt_fetch_one($stmt)
{
    $res = pbg_stmt_result($stmt);
    if (!$res) {
        return null;
    }
    $row = $res->fetch_assoc();
    return is_array($row) ? $row : null;
}

/**
 * @return list<array<string,mixed>>
 */
function pbg_stmt_fetch_all($stmt)
{
    $res = pbg_stmt_result($stmt);
    if (!$res) {
        return [];
    }
    $rows = [];
    while ($row = $res->fetch_assoc()) {
        if (is_array($row)) {
            $rows[] = $row;
        }
    }
    return $rows;
}

/**
 * @return array<string,mixed>|null
 */
function pbg_query_fetch_one(mysqli $db, $sql)
{
    $res = $db->query($sql);
    if (!($res instanceof mysqli_result)) {
        return null;
    }
    $row = $res->fetch_assoc();
    return is_array($row) ? $row : null;
}

/**
 * @return list<array<string,mixed>>
 */
function pbg_query_fetch_all(mysqli $db, $sql)
{
    $res = $db->query($sql);
    if (!($res instanceof mysqli_result)) {
        return [];
    }
    $rows = [];
    while ($row = $res->fetch_assoc()) {
        if (is_array($row)) {
            $rows[] = $row;
        }
    }
    return $rows;
}

/**
 * @return mysqli_stmt
 */
function pbg_prepare(mysqli $db, $sql)
{
    $stmt = $db->prepare($sql);
    if (!($stmt instanceof mysqli_stmt)) {
        throw new RuntimeException('prepare failed: ' . $db->error);
    }
    return $stmt;
}

function pbg_json($data, $code = 200)
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type, X-PBG-Token');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function pbg_body()
{
    $raw = file_get_contents('php://input');
    $json = json_decode($raw, true);
    if (is_array($json)) {
        return $json;
    }
    return $_POST ?: [];
}

/** lion-compatible mode → target + odds column */
function pbg_mode_meta($mode, $conf)
{
    $mode = (int)$mode;
    $map = [
        1  => ['target' => 'P',  'ratio' => 'ratio_1'],
        2  => ['target' => 'B',  'ratio' => 'ratio_1'],
        3  => ['target' => 'P',  'ratio' => 'ratio_2'],
        4  => ['target' => 'B',  'ratio' => 'ratio_2'],
        5  => ['target' => 'PP', 'ratio' => 'ratio_5'],
        6  => ['target' => 'BP', 'ratio' => 'ratio_6'],
        7  => ['target' => 'PB', 'ratio' => 'ratio_7'],
        8  => ['target' => 'BB', 'ratio' => 'ratio_8'],
        9  => ['target' => 'P',  'ratio' => 'ratio_3'],
        10 => ['target' => 'B',  'ratio' => 'ratio_3'],
        11 => ['target' => 'P',  'ratio' => 'ratio_4'],
        12 => ['target' => 'B',  'ratio' => 'ratio_4'],
        13 => ['target' => 'PP', 'ratio' => 'ratio_9'],
        14 => ['target' => 'BP', 'ratio' => 'ratio_10'],
        15 => ['target' => 'PB', 'ratio' => 'ratio_11'],
        16 => ['target' => 'BB', 'ratio' => 'ratio_12'],
    ];
    if (!isset($map[$mode])) {
        return null;
    }
    $m = $map[$mode];
    $col = $m['ratio'];
    return [
        'target' => $m['target'],
        'ratio' => (float)$conf[$col],
    ];
}

function pbg_mode_label_cn($mode)
{
    $labels = [
        1 => '功率球 单',
        2 => '功率球 双',
        3 => '功率球 小',
        4 => '功率球 大',
        5 => '功率球 单+小',
        6 => '功率球 双+小',
        7 => '功率球 单+大',
        8 => '功率球 双+大',
        9 => '普通球 单',
        10 => '普通球 双',
        11 => '普通球 小',
        12 => '普通球 大',
        13 => '普通球 单+小',
        14 => '普通球 双+小',
        15 => '普通球 单+大',
        16 => '普通球 双+大',
    ];
    $mode = (int)$mode;
    return isset($labels[$mode]) ? $labels[$mode] : ('模式' . $mode);
}

function pbg_classify_draw($pb, $sum)
{
    $pb = (int)$pb;
    $sum = (int)$sum;
    $r1 = ($pb % 2) ? 'P' : 'B';
    $r2 = ($pb < 5) ? 'P' : 'B';
    $r3 = ($sum % 2) ? 'P' : 'B';
    $r4 = ($sum <= 72) ? 'P' : 'B';
    if ($sum <= 64) {
        $r5 = 'S';
    } elseif ($sum <= 80) {
        $r5 = 'M';
    } else {
        $r5 = 'L';
    }
    return [
        'result_1' => $r1,
        'result_2' => $r2,
        'result_3' => $r3,
        'result_4' => $r4,
        'result_5' => $r5,
        'pb_oe' => $r1 === 'P' ? '单' : '双',
        'pb_uo' => $r2 === 'P' ? '小' : '大',
        'sum_oe' => $r3 === 'P' ? '单' : '双',
        'sum_uo' => $r4 === 'P' ? '小' : '大',
        'sum_size' => $r5 === 'S' ? '小' : ($r5 === 'M' ? '中' : '大'),
    ];
}

/**
 * powerball Home::buildChatPickSpriteKey 와 동일
 * 홀=o, 짝=e, 언더=u, 오버=o, 소=s, 중=m, 대=b
 */
function pbg_pick_sprite_key($pb, $sum)
{
    $pb = (int)$pb;
    $sum = (int)$sum;
    $c0 = ($pb % 2 === 1) ? 'o' : 'e';
    $c1 = ($sum % 2 === 1) ? 'o' : 'e';
    $c2 = ($pb <= 4) ? 'u' : 'o';
    $c3 = ($sum <= 72) ? 'u' : 'o';
    if ($sum <= 64) {
        $c4 = 's';
    } elseif ($sum <= 80) {
        $c4 = 'm';
    } else {
        $c4 = 'b';
    }
    return $c0 . $c1 . $c2 . $c3 . $c4;
}

/** 중국어 추첨내역 날짜 형식: 07月16日 */
function pbg_date_label($drawnAt)
{
    $ts = $drawnAt ? strtotime($drawnAt) : time();
    if ($ts === false) {
        $ts = time();
    }
    return sprintf('%02d月%02d日', (int)date('n', $ts), (int)date('j', $ts));
}

/** Evaluate win like lion PballBet_Model::updateBetRound modes 1-16 */
function pbg_is_win($mode, $target, $cls)
{
    $mode = (int)$mode;
    switch ($mode) {
        case 1:
        case 2:
            return $target === $cls['result_1'];
        case 3:
        case 4:
            return $target === $cls['result_2'];
        case 5:
            return $cls['result_1'] === 'P' && $cls['result_2'] === 'P';
        case 6:
            return $cls['result_1'] === 'B' && $cls['result_2'] === 'P';
        case 7:
            return $cls['result_1'] === 'P' && $cls['result_2'] === 'B';
        case 8:
            return $cls['result_1'] === 'B' && $cls['result_2'] === 'B';
        case 9:
        case 10:
            return $target === $cls['result_3'];
        case 11:
        case 12:
            return $target === $cls['result_4'];
        case 13:
            return $cls['result_3'] === 'P' && $cls['result_4'] === 'P';
        case 14:
            return $cls['result_3'] === 'B' && $cls['result_4'] === 'P';
        case 15:
            return $cls['result_3'] === 'P' && $cls['result_4'] === 'B';
        case 16:
            return $cls['result_3'] === 'B' && $cls['result_4'] === 'B';
        default:
            return false;
    }
}

function pbg_get_machine($code)
{
    $db = pbg_db();
    $stmt = pbg_prepare($db, 'SELECT * FROM machines WHERE code=? AND status=1 LIMIT 1');
    $stmt->bind_param('s', $code);
    $stmt->execute();
    $row = pbg_stmt_fetch_one($stmt);
    $stmt->close();
    return $row;
}

function pbg_hash_password($plain)
{
    return password_hash((string)$plain, PASSWORD_DEFAULT);
}

function pbg_verify_password($plain, $hash)
{
    $hash = (string)$hash;
    $plain = (string)$plain;
    if ($hash === '') {
        return false;
    }
    if (strpos($hash, '$2y$') === 0 || strpos($hash, '$2a$') === 0 || strpos($hash, '$argon') === 0) {
        return password_verify($plain, $hash);
    }
    return hash_equals($hash, $plain);
}

function pbg_get_member_by_uid($uid)
{
    $db = pbg_db();
    $stmt = pbg_prepare($db, 'SELECT * FROM member WHERE mb_uid=? AND mb_state_delete=0 LIMIT 1');
    $stmt->bind_param('s', $uid);
    $stmt->execute();
    $row = pbg_stmt_fetch_one($stmt);
    $stmt->close();
    return $row;
}

function pbg_get_member_by_fid($fid)
{
    $db = pbg_db();
    $fid = (int)$fid;
    $stmt = pbg_prepare($db, 'SELECT * FROM member WHERE mb_fid=? AND mb_state_delete=0 LIMIT 1');
    $stmt->bind_param('i', $fid);
    $stmt->execute();
    $row = pbg_stmt_fetch_one($stmt);
    $stmt->close();
    return $row;
}

function pbg_request_token()
{
    $hdr = isset($_SERVER['HTTP_X_PBG_TOKEN']) ? trim($_SERVER['HTTP_X_PBG_TOKEN']) : '';
    if ($hdr !== '') {
        return $hdr;
    }
    $body = pbg_body();
    if (!empty($body['token'])) {
        return trim((string)$body['token']);
    }
    if (isset($_GET['token'])) {
        return trim((string)$_GET['token']);
    }
    return '';
}

function pbg_auth_member($requireStore = true)
{
    $token = pbg_request_token();
    if ($token === '') {
        pbg_json(['status' => 'fail', 'code' => 'AUTH', 'message' => '로그인이 필요합니다'], 401);
    }
    $db = pbg_db();
    $stmt = pbg_prepare($db, 'SELECT * FROM sess_list WHERE sess_id=? LIMIT 1');
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $sess = pbg_stmt_fetch_one($stmt);
    $stmt->close();
    if (!$sess) {
        pbg_json(['status' => 'fail', 'code' => 'AUTH', 'message' => '세션이 만료되었습니다'], 401);
    }
    $member = pbg_get_member_by_fid((int)$sess['sess_mb_fid']);
    if (!$member || (int)$member['mb_state_active'] !== 1) {
        pbg_json(['status' => 'fail', 'code' => 'AUTH', 'message' => '계정 상태를 확인하세요'], 401);
    }
    if ($requireStore && (int)$member['mb_level'] !== 7) {
        pbg_json(['status' => 'fail', 'code' => 'AUTH', 'message' => '매장 계정으로 로그인하세요'], 403);
    }
    $db->query("UPDATE sess_list SET sess_update_time=NOW() WHERE sess_fid=" . (int)$sess['sess_fid']);
    return ['member' => $member, 'sess' => $sess, 'token' => $token];
}

function pbg_new_token()
{
    return bin2hex(random_bytes(24));
}

function pbg_get_conf()
{
    $db = pbg_db();
    $row = pbg_query_fetch_one($db, 'SELECT * FROM conf_game WHERE id=1 LIMIT 1');
    if (!$row) {
        throw new RuntimeException('conf_game id=1 missing (run sql/schema.sql)');
    }
    return $row;
}

/**
 * Current betting round based on latest draw + wall clock (5-min slots, KST).
 */
function pbg_current_round_info()
{
    $drawDb = pbg_db('draw_db');
    $conf = pbg_get_conf();
    $cutoff = (int)$conf['bet_cutoff_sec'];
    if ($cutoff < 20) {
        $cutoff = 20;
    }

    $res = $drawDb->query('SELECT * FROM draw_results ORDER BY round DESC LIMIT 1');
    $last = $res ? $res->fetch_assoc() : null;
    $lastRound = $last ? (int)$last['round'] : 0;
    $nextRound = $lastRound + 1;

    $now = time();
    // Next draw boundary: every 5 minutes
    $slot = 300;
    $remain = $slot - ($now % $slot);
    if ($remain === $slot) {
        $remain = 0;
    }
    $betRemain = max(0, $remain - $cutoff);
    $canBet = $betRemain > 0;

    return [
        'last_round' => $lastRound,
        'round' => $nextRound,
        'remain_seconds' => $remain,
        'bet_remain_seconds' => $betRemain,
        'bet_cutoff_sec' => $cutoff,
        'can_bet' => $canBet,
        'server_time' => date('Y-m-d H:i:s', $now),
        'last_draw' => $last ? [
            'round' => (int)$last['round'],
            'ball1' => (int)$last['ball1'],
            'ball2' => (int)$last['ball2'],
            'ball3' => (int)$last['ball3'],
            'ball4' => (int)$last['ball4'],
            'ball5' => (int)$last['ball5'],
            'powerball' => (int)$last['powerball'],
            'ball_sum' => (int)$last['ball_sum'],
            'drawn_at' => $last['drawn_at'],
            'class' => pbg_classify_draw($last['powerball'], $last['ball_sum']),
        ] : null,
    ];
}

function pbg_money_log($machineId, $betId, $type, $amount, $before, $after, $memo = '', $mbFid = 0, $mbUid = '')
{
    $db = pbg_db();
    $machineId = (int)$machineId;
    $betId = (int)$betId;
    $type = (int)$type;
    $mbFid = (int)$mbFid;
    $mbUid = (string)$mbUid;
    $stmt = $db->prepare('INSERT INTO money_log (mb_fid, mb_uid, machine_id, bet_id, change_type, amount, before_money, after_money, memo) VALUES (?,?,?,?,?,?,?,?,?)');
    $stmt->bind_param('isiiiddds', $mbFid, $mbUid, $machineId, $betId, $type, $amount, $before, $after, $memo);
    $stmt->execute();
    $stmt->close();

    if ($mbFid > 0) {
        $emp = 0;
        $h2 = $db->prepare('INSERT INTO money_history (money_mb_fid, money_mb_uid, money_mb_emp_fid, money_amount, money_before, money_after, money_change_type, money_update_time) VALUES (?,?,?,?,?,?,?,NOW())');
        $h2->bind_param('isidddi', $mbFid, $mbUid, $emp, $amount, $before, $after, $type);
        $h2->execute();
        $h2->close();
    }
}

/**
 * lion reground 타이밍과 유사: 5분 정각 직후(초 0~50) 또는 카운트다운 ≤5초
 */
function pbg_in_draw_fetch_window($remainSeconds = null)
{
    $now = time();
    $min = (int)date('i', $now);
    $sec = (int)date('s', $now);
    if ($min % 5 === 0 && $sec >= 0 && $sec <= 50) {
        return true;
    }
    if ($remainSeconds === null) {
        $info = pbg_current_round_info();
        $remainSeconds = (int)$info['remain_seconds'];
    }
    return $remainSeconds <= 5;
}

/**
 * HTTP GET powerball getDrawResult via 127.0.0.1 (same server).
 * Triggers getOrGenerate on powerball so draw_results is filled.
 *
 * @return array{ok:bool,draw:?array,http_code:int,error:string,raw:?string,fetched:bool}
 */
function pbg_fetch_powerball_draw($force = false)
{
    static $lastOkRound = 0;
    static $lastAttemptAt = 0;

    $cfg = pbg_config();
    $url = isset($cfg['powerball_fetch_url']) ? trim($cfg['powerball_fetch_url']) : '';
    if ($url === '') {
        return ['ok' => false, 'draw' => null, 'http_code' => 0, 'error' => 'powerball_fetch_url empty', 'raw' => null, 'fetched' => false];
    }

    $info = pbg_current_round_info();
    $remain = (int)$info['remain_seconds'];
    $lastRound = (int)$info['last_round'];

    if (!$force && !pbg_in_draw_fetch_window($remain)) {
        return [
            'ok' => true,
            'draw' => $info['last_draw'],
            'http_code' => 0,
            'error' => '',
            'raw' => null,
            'fetched' => false,
            'skipped' => 'outside_window',
            'last_round' => $lastRound,
        ];
    }

    // Avoid hammering: same round already fetched within 8s (unless force + new needed)
    $now = time();
    if (!$force && $lastOkRound === $lastRound && ($now - $lastAttemptAt) < 8 && $remain > 5) {
        return [
            'ok' => true,
            'draw' => $info['last_draw'],
            'http_code' => 0,
            'error' => '',
            'raw' => null,
            'fetched' => false,
            'skipped' => 'cooldown',
            'last_round' => $lastRound,
        ];
    }

    $lastAttemptAt = $now;
    $sep = (strpos($url, '?') === false) ? '?' : '&';
    $fetchUrl = $url . $sep . '_=' . (int)floor(microtime(true) * 1000);
    // Near slot end: allow powerball preNext generation (same as mini-view)
    if ($remain <= 5 && $remain > 0) {
        $fetchUrl .= '&preNext=1';
    }

    $headers = ['Accept: application/json'];
    $hostHdr = isset($cfg['powerball_fetch_host']) ? trim($cfg['powerball_fetch_host']) : '';
    if ($hostHdr !== '') {
        $headers[] = 'Host: ' . $hostHdr;
    }

    $ch = curl_init($fetchUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_TIMEOUT => 8,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_FOLLOWLOCATION => true,
    ]);
    $raw = curl_exec($ch);
    $errno = curl_errno($ch);
    $err = curl_error($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($errno || $raw === false) {
        return ['ok' => false, 'draw' => null, 'http_code' => $code, 'error' => $err ?: ('curl_' . $errno), 'raw' => null, 'fetched' => true];
    }
    if ($code < 200 || $code >= 300) {
        return ['ok' => false, 'draw' => null, 'http_code' => $code, 'error' => 'http_' . $code, 'raw' => $raw, 'fetched' => true];
    }

    $json = json_decode($raw, true);
    if (!is_array($json) || !isset($json['round'])) {
        return ['ok' => false, 'draw' => null, 'http_code' => $code, 'error' => 'bad_json', 'raw' => $raw, 'fetched' => true];
    }

    $draw = [
        'round' => (int)$json['round'],
        'ball1' => (int)(isset($json['ball1']) ? $json['ball1'] : (isset($json['n1']) ? $json['n1'] : 0)),
        'ball2' => (int)(isset($json['ball2']) ? $json['ball2'] : (isset($json['n2']) ? $json['n2'] : 0)),
        'ball3' => (int)(isset($json['ball3']) ? $json['ball3'] : (isset($json['n3']) ? $json['n3'] : 0)),
        'ball4' => (int)(isset($json['ball4']) ? $json['ball4'] : (isset($json['n4']) ? $json['n4'] : 0)),
        'ball5' => (int)(isset($json['ball5']) ? $json['ball5'] : (isset($json['n5']) ? $json['n5'] : 0)),
        'powerball' => (int)(isset($json['powerball']) ? $json['powerball'] : (isset($json['p1']) ? $json['p1'] : 0)),
        'ball_sum' => (int)(isset($json['ball_sum']) ? $json['ball_sum'] : (isset($json['sum']) ? $json['sum'] : 0)),
        'drawn_at' => isset($json['drawn_at']) ? $json['drawn_at'] : '',
        'pick_sprite_key' => pbg_pick_sprite_key(
            isset($json['powerball']) ? $json['powerball'] : (isset($json['p1']) ? $json['p1'] : 0),
            isset($json['ball_sum']) ? $json['ball_sum'] : (isset($json['sum']) ? $json['sum'] : 0)
        ),
    ];
    $lastOkRound = $draw['round'];

    return [
        'ok' => true,
        'draw' => $draw,
        'http_code' => $code,
        'error' => '',
        'raw' => null,
        'fetched' => true,
        'last_round' => $draw['round'],
    ];
}

/**
 * Game-day window for picker date Y-m-d (same as powerball PowerballDraw_Model).
 * @return array{0:string,1:string}
 */
function pbg_game_day_window($ymd)
{
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $ymd)) {
        return ['', ''];
    }
    $from = $ymd . ' 00:05:00';
    $to = (new DateTimeImmutable($ymd))->modify('+1 day')->format('Y-m-d') . ' 00:00:00';
    return [$from, $to];
}

/**
 * Baccarat big-road pattern: fixed 7 rows, bend right on collision / overflow.
 * Newest columns kept when clipping to $maxCols (no horizontal scroll).
 *
 * @param array $groups [[type, [rounds...]], ...] chronological
 * @param string $emptyLabel
 * @param int $maxRows
 * @param int $maxCols
 */
function pbg_build_baccarat_road_table(array $groups, $emptyLabel = '', $maxRows = 7, $maxCols = 30)
{
    $maxRows = max(1, (int)$maxRows);
    $maxCols = max(1, (int)$maxCols);
    $cols = [];
    $nextStartCol = 0;

    foreach ($groups as $group) {
        list($type, $rounds) = $group;
        if (!$rounds) {
            continue;
        }
        $col = $nextStartCol;
        while (true) {
            if (!isset($cols[$col])) {
                $cols[$col] = array_fill(0, $maxRows, null);
            }
            if ($cols[$col][0] === null) {
                break;
            }
            $col++;
        }
        $startCol = $col;
        $row = 0;
        $dragon = false;
        $typeEsc = htmlspecialchars((string)$type, ENT_QUOTES, 'UTF-8');

        foreach ($rounds as $i => $r) {
            $cell = [
                'class' => $typeEsc,
                'text' => ((int)$r % 1000),
            ];
            if ($i === 0) {
                if (!isset($cols[$col])) {
                    $cols[$col] = array_fill(0, $maxRows, null);
                }
                $cols[$col][$row] = $cell;
                continue;
            }
            if (!$dragon) {
                $nextRow = $row + 1;
                if ($nextRow < $maxRows) {
                    if (!isset($cols[$col])) {
                        $cols[$col] = array_fill(0, $maxRows, null);
                    }
                    if ($cols[$col][$nextRow] === null) {
                        $row = $nextRow;
                        $cols[$col][$row] = $cell;
                        continue;
                    }
                }
                $dragon = true;
            }
            $col++;
            while (true) {
                if (!isset($cols[$col])) {
                    $cols[$col] = array_fill(0, $maxRows, null);
                }
                if ($cols[$col][$row] === null) {
                    break;
                }
                $col++;
            }
            $cols[$col][$row] = $cell;
        }
        $nextStartCol = $startCol + 1;
    }

    if (!$cols) {
        return '<div style="padding:12px;color:#969696;text-align:center;">'
            . htmlspecialchars((string)$emptyLabel, ENT_QUOTES, 'UTF-8') . '</div>';
    }

    ksort($cols);
    $cols = array_values($cols);
    if (count($cols) > $maxCols) {
        $cols = array_slice($cols, -$maxCols);
    }

    $html = '<table class="patternTable roadTable"><tbody>';
    for ($r = 0; $r < $maxRows; $r++) {
        $html .= '<tr>';
        foreach ($cols as $colCells) {
            $c = isset($colCells[$r]) ? $colCells[$r] : null;
            if ($c === null) {
                $html .= '<td></td>';
            } else {
                $html .= '<td><div class="' . $c['class'] . '">' . $c['text'] . '</div></td>';
            }
        }
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';
    return $html;
}

/**
 * Powerball odd/even pattern HTML (baccarat 7-row road).
 * @param array $groups
 * @param string $lang ko|zh|en
 */
function pbg_build_odd_even_pattern_table(array $groups, $lang = 'ko')
{
    $oddEven = [
        'ko' => ['empty' => '패턴 데이터 없음'],
        'zh' => ['empty' => '暂无图案数据'],
        'en' => ['empty' => 'No pattern data'],
    ];
    if (!isset($oddEven[$lang])) {
        $lang = 'ko';
    }
    return pbg_build_baccarat_road_table($groups, $oddEven[$lang]['empty'], 7, 30);
}

/**
 * Build powerball odd/even pattern from draw_results.
 * @param string $date Y-m-d
 * @param string $mode '' | 'latestLog'
 * @param int $roundCnt
 * @param string $lang ko|zh|en
 */
function pbg_build_powerball_odd_even_pattern_html($date = '', $mode = '', $roundCnt = 300, $lang = 'ko')
{
    if (!$date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        $date = date('Y-m-d');
    }
    if ($roundCnt < 50 || $roundCnt > 2000) {
        $roundCnt = 300;
    } else {
        $roundCnt = (int)(round($roundCnt / 50) * 50);
    }

    $drawDb = pbg_db('draw_db');
    $rows = [];
    if ($mode === 'latestLog') {
        $stmt = pbg_prepare($drawDb, 'SELECT round, powerball FROM draw_results ORDER BY round DESC LIMIT ?');
        $stmt->bind_param('i', $roundCnt);
        $stmt->execute();
        $rows = pbg_stmt_fetch_all($stmt);
        $stmt->close();
        usort($rows, static function ($a, $b) {
            return ((int)$a['round']) <=> ((int)$b['round']);
        });
    } else {
        list($dateFrom, $dateTo) = pbg_game_day_window($date);
        $stmt = pbg_prepare($drawDb, 'SELECT round, powerball FROM draw_results WHERE drawn_at >= ? AND drawn_at <= ? ORDER BY round ASC');
        $stmt->bind_param('ss', $dateFrom, $dateTo);
        $stmt->execute();
        $rows = pbg_stmt_fetch_all($stmt);
        $stmt->close();
    }

    $groups = [];
    foreach ($rows as $draw) {
        $round = (int)$draw['round'];
        $pb = (int)$draw['powerball'];
        $type = ($pb % 2 === 1) ? 'odd' : 'even';
        if (!empty($groups) && $groups[count($groups) - 1][0] === $type) {
            $groups[count($groups) - 1][1][] = $round;
        } else {
            $groups[] = [$type, [$round]];
        }
    }
    return pbg_build_odd_even_pattern_table($groups, $lang);
}

/**
 * Under/over pattern table (baccarat 7-row road).
 * @param array $groups
 * @param string $lang ko|zh|en
 */
function pbg_build_under_over_pattern_table(array $groups, $lang = 'ko')
{
    $uo = [
        'ko' => ['empty' => '패턴 데이터 없음'],
        'zh' => ['empty' => '暂无图案数据'],
        'en' => ['empty' => 'No pattern data'],
    ];
    if (!isset($uo[$lang])) {
        $lang = 'ko';
    }
    return pbg_build_baccarat_road_table($groups, $uo[$lang]['empty'], 7, 30);
}

/**
 * Build powerball under/over pattern from draw_results (pb <= 4 under, else over).
 */
function pbg_build_powerball_under_over_pattern_html($date = '', $mode = '', $roundCnt = 300, $lang = 'ko')
{
    if (!$date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        $date = date('Y-m-d');
    }
    if ($roundCnt < 50 || $roundCnt > 2000) {
        $roundCnt = 300;
    } else {
        $roundCnt = (int)(round($roundCnt / 50) * 50);
    }

    $drawDb = pbg_db('draw_db');
    $rows = [];
    if ($mode === 'latestLog') {
        $stmt = pbg_prepare($drawDb, 'SELECT round, powerball FROM draw_results ORDER BY round DESC LIMIT ?');
        $stmt->bind_param('i', $roundCnt);
        $stmt->execute();
        $rows = pbg_stmt_fetch_all($stmt);
        $stmt->close();
        usort($rows, static function ($a, $b) {
            return ((int)$a['round']) <=> ((int)$b['round']);
        });
    } else {
        list($dateFrom, $dateTo) = pbg_game_day_window($date);
        $stmt = pbg_prepare($drawDb, 'SELECT round, powerball FROM draw_results WHERE drawn_at >= ? AND drawn_at <= ? ORDER BY round ASC');
        $stmt->bind_param('ss', $dateFrom, $dateTo);
        $stmt->execute();
        $rows = pbg_stmt_fetch_all($stmt);
        $stmt->close();
    }

    $groups = [];
    foreach ($rows as $draw) {
        $round = (int)$draw['round'];
        $pb = (int)$draw['powerball'];
        $type = ($pb <= 4) ? 'under' : 'over';
        if (!empty($groups) && $groups[count($groups) - 1][0] === $type) {
            $groups[count($groups) - 1][1][] = $round;
        } else {
            $groups[] = [$type, [$round]];
        }
    }
    return pbg_build_under_over_pattern_table($groups, $lang);
}

/**
 * Fetch draw rows for pattern builders (round, powerball, ball_sum).
 */
function pbg_pattern_fetch_draws($date = '', $mode = '', $roundCnt = 300)
{
    if (!$date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        $date = date('Y-m-d');
    }
    if ($roundCnt < 50 || $roundCnt > 2000) {
        $roundCnt = 300;
    } else {
        $roundCnt = (int)(round($roundCnt / 50) * 50);
    }

    $drawDb = pbg_db('draw_db');
    $rows = [];
    if ($mode === 'latestLog') {
        $stmt = pbg_prepare($drawDb, 'SELECT round, powerball, ball_sum FROM draw_results ORDER BY round DESC LIMIT ?');
        $stmt->bind_param('i', $roundCnt);
        $stmt->execute();
        $rows = pbg_stmt_fetch_all($stmt);
        $stmt->close();
        usort($rows, static function ($a, $b) {
            return ((int)$a['round']) <=> ((int)$b['round']);
        });
    } else {
        list($dateFrom, $dateTo) = pbg_game_day_window($date);
        $stmt = pbg_prepare($drawDb, 'SELECT round, powerball, ball_sum FROM draw_results WHERE drawn_at >= ? AND drawn_at <= ? ORDER BY round ASC');
        $stmt->bind_param('ss', $dateFrom, $dateTo);
        $stmt->execute();
        $rows = pbg_stmt_fetch_all($stmt);
        $stmt->close();
    }
    return $rows;
}

/**
 * Number-sum (일반볼) odd/even pattern — ball_sum % 2.
 */
function pbg_build_number_sum_odd_even_pattern_html($date = '', $mode = '', $roundCnt = 300, $lang = 'ko')
{
    $rows = pbg_pattern_fetch_draws($date, $mode, $roundCnt);
    $groups = [];
    foreach ($rows as $draw) {
        $round = (int)$draw['round'];
        $sum = (int)$draw['ball_sum'];
        $type = ($sum % 2 === 1) ? 'odd' : 'even';
        if (!empty($groups) && $groups[count($groups) - 1][0] === $type) {
            $groups[count($groups) - 1][1][] = $round;
        } else {
            $groups[] = [$type, [$round]];
        }
    }
    return pbg_build_odd_even_pattern_table($groups, $lang);
}

/**
 * Number-sum (일반볼) under/over pattern — sum <= 72 under, else over.
 */
function pbg_build_number_sum_under_over_pattern_html($date = '', $mode = '', $roundCnt = 300, $lang = 'ko')
{
    $rows = pbg_pattern_fetch_draws($date, $mode, $roundCnt);
    $groups = [];
    foreach ($rows as $draw) {
        $round = (int)$draw['round'];
        $sum = (int)$draw['ball_sum'];
        $type = ($sum <= 72) ? 'under' : 'over';
        if (!empty($groups) && $groups[count($groups) - 1][0] === $type) {
            $groups[count($groups) - 1][1][] = $round;
        } else {
            $groups[] = [$type, [$round]];
        }
    }
    return pbg_build_under_over_pattern_table($groups, $lang);
}
