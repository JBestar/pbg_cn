<?php
require __DIR__ . '/_bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    pbg_json(['status' => 'ok']);
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
if ($action === '' && isset($_GET['a'])) {
    $action = $_GET['a'];
}

try {
    switch ($action) {
        case 'login':
            api_login();
            break;
        case 'logout':
            api_logout();
            break;
        case 'me':
            api_me();
            break;
        case 'status':
            api_status();
            break;
        case 'history':
            api_history();
            break;
        case 'draws':
            api_draws();
            break;
        case 'patterns':
            api_patterns();
            break;
        case 'pattern_pb_oddeven':
            api_pattern_pb_oddeven();
            break;
        case 'pattern_pb_underover':
            api_pattern_pb_underover();
            break;
        case 'pattern_sum_oddeven':
            api_pattern_sum_oddeven();
            break;
        case 'pattern_sum_underover':
            api_pattern_sum_underover();
            break;
        case 'bet':
            api_bet();
            break;
        case 'cancel':
            api_cancel();
            break;
        case 'settle':
            api_settle();
            break;
        case 'fetch_draw':
            api_fetch_draw();
            break;
        default:
            pbg_json(['status' => 'fail', 'message' => 'unknown action'], 400);
    }
} catch (Throwable $e) {
    pbg_json(['status' => 'fail', 'message' => $e->getMessage()], 500);
}

function api_login()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        pbg_json(['status' => 'fail', 'message' => 'POST required'], 405);
    }
    $body = pbg_body();
    $uid = isset($body['uid']) ? trim((string)$body['uid']) : '';
    $pwd = isset($body['pwd']) ? (string)$body['pwd'] : '';
    $machineCode = isset($body['machine']) ? trim((string)$body['machine']) : '';
    if ($uid === '' || $pwd === '') {
        pbg_json(['status' => 'fail', 'code' => 'NEED_CREDENTIALS', 'message' => '아이디와 비밀번호를 입력하세요']);
    }
    $member = pbg_get_member_by_uid($uid);
    if (!$member || !pbg_verify_password($pwd, $member['mb_pwd'])) {
        pbg_json(['status' => 'fail', 'code' => 'BAD_CREDENTIALS', 'message' => '아이디 또는 비밀번호가 올바르지 않습니다']);
    }
    if ((int)$member['mb_state_active'] !== 1) {
        pbg_json(['status' => 'fail', 'code' => 'INACTIVE', 'message' => '비활성 계정입니다']);
    }
    if ((int)$member['mb_level'] !== 7) {
        pbg_json(['status' => 'fail', 'code' => 'NOT_STORE', 'message' => '매장 계정으로 로그인하세요']);
    }
    // Rehash legacy plain
    if (strpos($member['mb_pwd'], '$2y$') !== 0 && strpos($member['mb_pwd'], '$argon') !== 0) {
        $db = pbg_db();
        $hash = pbg_hash_password($pwd);
        $u = $db->prepare('UPDATE member SET mb_pwd=? WHERE mb_fid=?');
        $fid = (int)$member['mb_fid'];
        $u->bind_param('si', $hash, $fid);
        $u->execute();
        $u->close();
    }

    $db = pbg_db();
    $fid = (int)$member['mb_fid'];
    $db->query("DELETE FROM sess_list WHERE sess_mb_fid=" . $fid);
    $token = pbg_new_token();
    $level = (int)$member['mb_level'];
    $emp = (int)$member['mb_emp_fid'];
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    $ins = $db->prepare('INSERT INTO sess_list (sess_id, sess_mb_fid, sess_mb_uid, sess_mb_level, sess_emp_fid, sess_ip, sess_machine_code, sess_update_time) VALUES (?,?,?,?,?,?,?,NOW())');
    $ins->bind_param('sisiiis', $token, $fid, $uid, $level, $emp, $ip, $machineCode);
    $ins->execute();
    $ins->close();

    $db->query("UPDATE member SET mb_time_last=NOW(), mb_ip_last='" . $db->real_escape_string($ip) . "' WHERE mb_fid=" . $fid);
    if ($machineCode !== '') {
        $m = $db->prepare('UPDATE machines SET last_mb_uid=? WHERE code=?');
        $m->bind_param('ss', $uid, $machineCode);
        $m->execute();
        $m->close();
    }

    pbg_json([
        'status' => 'success',
        'data' => [
            'token' => $token,
            'member' => [
                'uid' => $member['mb_uid'],
                'name' => $member['mb_nickname'],
                'balance' => (float)$member['mb_money'],
                'level' => (int)$member['mb_level'],
            ],
        ],
    ]);
}

function api_logout()
{
    $token = pbg_request_token();
    if ($token !== '') {
        $db = pbg_db();
        $stmt = $db->prepare('DELETE FROM sess_list WHERE sess_id=?');
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $stmt->close();
    }
    pbg_json(['status' => 'success']);
}

function api_me()
{
    $auth = pbg_auth_member(true);
    $m = $auth['member'];
    pbg_json([
        'status' => 'success',
        'data' => [
            'uid' => $m['mb_uid'],
            'name' => $m['mb_nickname'],
            'balance' => (float)$m['mb_money'],
        ],
    ]);
}

function api_status()
{
    $auth = pbg_auth_member(true);
    $m = $auth['member'];
    $cfg = pbg_config();
    $round = pbg_current_round_info();
    $conf = pbg_get_conf();
    pbg_json([
        'status' => 'success',
        'data' => [
            'machine' => [
                'code' => $m['mb_uid'],
                'name' => $m['mb_nickname'],
                'balance' => (float)$m['mb_money'],
            ],
            'member' => [
                'uid' => $m['mb_uid'],
                'name' => $m['mb_nickname'],
                'balance' => (float)$m['mb_money'],
            ],
            'round' => $round,
            'odds' => [
                1 => (float)$conf['ratio_1'],
                2 => (float)$conf['ratio_1'],
                3 => (float)$conf['ratio_2'],
                4 => (float)$conf['ratio_2'],
                5 => (float)$conf['ratio_5'],
                6 => (float)$conf['ratio_6'],
                7 => (float)$conf['ratio_7'],
                8 => (float)$conf['ratio_8'],
                9 => (float)$conf['ratio_3'],
                10 => (float)$conf['ratio_3'],
                11 => (float)$conf['ratio_4'],
                12 => (float)$conf['ratio_4'],
                13 => (float)$conf['ratio_9'],
                14 => (float)$conf['ratio_10'],
                15 => (float)$conf['ratio_11'],
                16 => (float)$conf['ratio_12'],
            ],
            'powerball_base_url' => $cfg['powerball_base_url'],
            'currency_symbol' => $cfg['currency_symbol'],
        ],
    ]);
}

function api_history()
{
    $auth = pbg_auth_member(true);
    $m = $auth['member'];
    $limit = isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : 30;
    $db = pbg_db();
    $fid = (int)$m['mb_fid'];
    $stmt = pbg_prepare($db, 'SELECT * FROM bets WHERE mb_fid=? ORDER BY id DESC LIMIT ?');
    $stmt->bind_param('ii', $fid, $limit);
    $stmt->execute();
    $raw = pbg_stmt_fetch_all($stmt);
    $stmt->close();
    $rows = [];
    foreach ($raw as $r) {
        $rows[] = format_bet_row($r);
    }
    pbg_json(['status' => 'success', 'data' => $rows]);
}

function format_bet_row($r)
{
    $stateMap = [1 => '等待', 2 => '未中', 3 => '已中', 4 => '取消'];
    $st = (int)$r['state'];
    return [
        'id' => (int)$r['id'],
        'round' => (int)$r['round'],
        'mode' => (int)$r['mode'],
        'label' => pbg_mode_label_cn($r['mode']),
        'amount' => (float)$r['amount'],
        'win_amount' => (float)$r['win_amount'],
        'ratio' => (float)$r['ratio'],
        'state' => $st,
        'state_label' => isset($stateMap[$st]) ? $stateMap[$st] : '',
        'created_at' => $r['created_at'],
        'settled_at' => $r['settled_at'],
    ];
}

function api_draws()
{
    $limit = isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : 30;
    $drawDb = pbg_db('draw_db');
    $stmt = pbg_prepare($drawDb, 'SELECT round, ball1, ball2, ball3, ball4, ball5, powerball, ball_sum, drawn_at FROM draw_results ORDER BY round DESC LIMIT ?');
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $raw = pbg_stmt_fetch_all($stmt);
    $stmt->close();
    $rows = [];
    foreach ($raw as $r) {
        $cls = pbg_classify_draw($r['powerball'], $r['ball_sum']);
        $rows[] = [
            'round' => (int)$r['round'],
            'balls' => [(int)$r['ball1'], (int)$r['ball2'], (int)$r['ball3'], (int)$r['ball4'], (int)$r['ball5']],
            'powerball' => (int)$r['powerball'],
            'ball_sum' => (int)$r['ball_sum'],
            'drawn_at' => $r['drawn_at'],
            'date_label' => pbg_date_label($r['drawn_at']),
            'pick_sprite_key' => pbg_pick_sprite_key($r['powerball'], $r['ball_sum']),
            'pb_oe' => $cls['pb_oe'],
            'pb_uo' => $cls['pb_uo'],
            'sum_oe' => $cls['sum_oe'],
            'sum_uo' => $cls['sum_uo'],
            'sum_size' => $cls['sum_size'],
        ];
    }

    $roundInfo = pbg_current_round_info();
    pbg_json([
        'status' => 'success',
        'data' => $rows,
        'next_round' => (int)$roundInfo['round'],
        'next_date_label' => pbg_date_label(date('Y-m-d H:i:s')),
    ]);
}

function api_patterns()
{
    $limit = isset($_GET['limit']) ? max(10, min(300, (int)$_GET['limit'])) : 100;
    $drawDb = pbg_db('draw_db');
    $stmt = pbg_prepare($drawDb, 'SELECT powerball, ball_sum FROM draw_results ORDER BY round DESC LIMIT ?');
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $raw = pbg_stmt_fetch_all($stmt);
    $stmt->close();
    $stats = [
        'pb_oe' => ['单' => 0, '双' => 0],
        'pb_uo' => ['小' => 0, '大' => 0],
        'sum_oe' => ['单' => 0, '双' => 0],
        'sum_uo' => ['小' => 0, '大' => 0],
        'sum_size' => ['小' => 0, '中' => 0, '大' => 0],
        'streak' => [
            'pb_oe' => '', 'pb_uo' => '', 'sum_oe' => '', 'sum_uo' => '', 'sum_size' => '',
        ],
        'sample' => 0,
    ];
    $seq = ['pb_oe' => [], 'pb_uo' => [], 'sum_oe' => [], 'sum_uo' => [], 'sum_size' => []];
    foreach ($raw as $r) {
        $cls = pbg_classify_draw($r['powerball'], $r['ball_sum']);
        $stats['pb_oe'][$cls['pb_oe']]++;
        $stats['pb_uo'][$cls['pb_uo']]++;
        $stats['sum_oe'][$cls['sum_oe']]++;
        $stats['sum_uo'][$cls['sum_uo']]++;
        $stats['sum_size'][$cls['sum_size']]++;
        $seq['pb_oe'][] = $cls['pb_oe'];
        $seq['pb_uo'][] = $cls['pb_uo'];
        $seq['sum_oe'][] = $cls['sum_oe'];
        $seq['sum_uo'][] = $cls['sum_uo'];
        $seq['sum_size'][] = $cls['sum_size'];
        $stats['sample']++;
    }
    foreach ($seq as $k => $arr) {
        $stats['streak'][$k] = streak_summary($arr);
    }
    pbg_json(['status' => 'success', 'data' => $stats]);
}

function streak_summary($arr)
{
    // arr is newest-first
    if (!$arr) {
        return '';
    }
    $cur = $arr[0];
    $n = 1;
    for ($i = 1; $i < count($arr); $i++) {
        if ($arr[$i] === $cur) {
            $n++;
        } else {
            break;
        }
    }
    return $cur . 'x' . $n;
}

/**
 * Powerball odd/even pattern HTML (always-on under mini view).
 * Query: date=Y-m-d (game day), mode='' (day) | latestLog, roundCnt, lang
 */
function api_pattern_pb_oddeven()
{
    pbg_config(); // Asia/Seoul before date()
    $date = isset($_GET['date']) ? trim($_GET['date']) : date('Y-m-d');
    $mode = isset($_GET['mode']) ? trim($_GET['mode']) : '';
    $roundCnt = isset($_GET['roundCnt']) ? (int)$_GET['roundCnt'] : 300;
    $lang = isset($_GET['lang']) ? trim($_GET['lang']) : 'ko';
    if ($lang !== 'ko' && $lang !== 'zh' && $lang !== 'en') {
        $lang = 'ko';
    }
    $html = pbg_build_powerball_odd_even_pattern_html($date, $mode, $roundCnt, $lang);
    pbg_json([
        'status' => 'success',
        'content' => $html,
        'meta' => [
            'date' => $date,
            'mode' => $mode === '' ? 'day' : $mode,
            'roundCnt' => $roundCnt,
            'lang' => $lang,
            'type' => 'powerball_oddEven',
        ],
    ]);
}

/**
 * Powerball under/over pattern HTML (always-on under odd/even box).
 * Same day window as pattern_pb_oddeven.
 */
function api_pattern_pb_underover()
{
    pbg_config();
    $date = isset($_GET['date']) ? trim($_GET['date']) : date('Y-m-d');
    $mode = isset($_GET['mode']) ? trim($_GET['mode']) : '';
    $roundCnt = isset($_GET['roundCnt']) ? (int)$_GET['roundCnt'] : 300;
    $lang = isset($_GET['lang']) ? trim($_GET['lang']) : 'ko';
    if ($lang !== 'ko' && $lang !== 'zh' && $lang !== 'en') {
        $lang = 'ko';
    }
    $html = pbg_build_powerball_under_over_pattern_html($date, $mode, $roundCnt, $lang);
    pbg_json([
        'status' => 'success',
        'content' => $html,
        'meta' => [
            'date' => $date,
            'mode' => $mode === '' ? 'day' : $mode,
            'roundCnt' => $roundCnt,
            'lang' => $lang,
            'type' => 'powerball_underOver',
        ],
    ]);
}

/**
 * Number-sum odd/even pattern (일반볼 홀/짝).
 */
function api_pattern_sum_oddeven()
{
    pbg_config();
    $date = isset($_GET['date']) ? trim($_GET['date']) : date('Y-m-d');
    $mode = isset($_GET['mode']) ? trim($_GET['mode']) : '';
    $roundCnt = isset($_GET['roundCnt']) ? (int)$_GET['roundCnt'] : 300;
    $lang = isset($_GET['lang']) ? trim($_GET['lang']) : 'ko';
    if ($lang !== 'ko' && $lang !== 'zh' && $lang !== 'en') {
        $lang = 'ko';
    }
    $html = pbg_build_number_sum_odd_even_pattern_html($date, $mode, $roundCnt, $lang);
    pbg_json([
        'status' => 'success',
        'content' => $html,
        'meta' => [
            'date' => $date,
            'mode' => $mode === '' ? 'day' : $mode,
            'roundCnt' => $roundCnt,
            'lang' => $lang,
            'type' => 'number_oddEven',
        ],
    ]);
}

/**
 * Number-sum under/over pattern (일반볼 언더/오버, 기준 72.5).
 */
function api_pattern_sum_underover()
{
    pbg_config();
    $date = isset($_GET['date']) ? trim($_GET['date']) : date('Y-m-d');
    $mode = isset($_GET['mode']) ? trim($_GET['mode']) : '';
    $roundCnt = isset($_GET['roundCnt']) ? (int)$_GET['roundCnt'] : 300;
    $lang = isset($_GET['lang']) ? trim($_GET['lang']) : 'ko';
    if ($lang !== 'ko' && $lang !== 'zh' && $lang !== 'en') {
        $lang = 'ko';
    }
    $html = pbg_build_number_sum_under_over_pattern_html($date, $mode, $roundCnt, $lang);
    pbg_json([
        'status' => 'success',
        'content' => $html,
        'meta' => [
            'date' => $date,
            'mode' => $mode === '' ? 'day' : $mode,
            'roundCnt' => $roundCnt,
            'lang' => $lang,
            'type' => 'number_underOver',
        ],
    ]);
}

function api_bet()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        pbg_json(['status' => 'fail', 'message' => 'POST required'], 405);
    }
    $auth = pbg_auth_member(true);
    $member = $auth['member'];
    $body = pbg_body();
    $mode = isset($body['mode']) ? (int)$body['mode'] : 0;
    $amount = isset($body['amount']) ? (float)$body['amount'] : 0;
    $clientRound = isset($body['round']) ? (int)$body['round'] : 0;
    $machineCode = isset($body['machine']) ? trim((string)$body['machine']) : (isset($auth['sess']['sess_machine_code']) ? $auth['sess']['sess_machine_code'] : '');

    if ($amount <= 0) {
        pbg_json(['status' => 'fail', 'code' => 'NO_AMOUNT', 'message' => '请选择金额']);
    }

    $conf = pbg_get_conf();
    $meta = pbg_mode_meta($mode, $conf);
    if (!$meta) {
        pbg_json(['status' => 'fail', 'code' => 'INVALID_MODE', 'message' => '无效投注项目']);
    }
    if ($amount < (float)$conf['min_bet']) {
        pbg_json(['status' => 'fail', 'code' => 'MIN_BET', 'message' => '低于最小投注']);
    }
    if ($amount > (float)$conf['max_bet']) {
        pbg_json(['status' => 'fail', 'code' => 'MAX_BET', 'message' => '超过最大投注']);
    }

    $roundInfo = pbg_current_round_info();
    if (!$roundInfo['can_bet']) {
        pbg_json(['status' => 'fail', 'code' => 'CLOSED', 'message' => '当前期已封盘']);
    }
    if ($clientRound > 0 && $clientRound !== (int)$roundInfo['round']) {
        pbg_json(['status' => 'fail', 'code' => 'ROUND', 'message' => '期号已变更，请重试']);
    }

    $machineId = 0;
    if ($machineCode !== '') {
        $mc = pbg_get_machine($machineCode);
        if ($mc) {
            $machineId = (int)$mc['id'];
        }
    }

    $db = pbg_db();
    $db->begin_transaction();
    try {
        $fid = (int)$member['mb_fid'];
        $stmt = pbg_prepare($db, 'SELECT mb_money, mb_emp_fid, mb_uid FROM member WHERE mb_fid=? FOR UPDATE');
        $stmt->bind_param('i', $fid);
        $stmt->execute();
        $row = pbg_stmt_fetch_one($stmt);
        $stmt->close();
        if (!$row) {
            throw new RuntimeException('member lock failed');
        }
        $bal = (float)$row['mb_money'];
        $empFid = (int)$row['mb_emp_fid'];
        $mbUid = $row['mb_uid'];

        if ($bal < $amount) {
            $db->rollback();
            pbg_json(['status' => 'fail', 'code' => 'BALANCE', 'message' => '余额不足']);
        }

        $after = $bal - $amount;
        $upd = $db->prepare('UPDATE member SET mb_money=? WHERE mb_fid=?');
        $upd->bind_param('di', $after, $fid);
        $upd->execute();
        $upd->close();

        $round = (int)$roundInfo['round'];
        $target = $meta['target'];
        $ratio = (float)$meta['ratio'];
        $winAmt = 0.0;
        $stateWait = 1;
        $emptyResult = '';
        $mcode = $machineCode !== '' ? $machineCode : $mbUid;

        $ins = $db->prepare('INSERT INTO bets (mb_fid, mb_uid, emp_fid, machine_id, machine_code, round, mode, target, ratio, amount, win_amount, state, result, before_money, after_money) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $ins->bind_param(
            'isiisiisdddisdd',
            $fid,
            $mbUid,
            $empFid,
            $machineId,
            $mcode,
            $round,
            $mode,
            $target,
            $ratio,
            $amount,
            $winAmt,
            $stateWait,
            $emptyResult,
            $bal,
            $after
        );
        if (!$ins->execute()) {
            throw new RuntimeException($ins->error);
        }
        $betId = (int)$ins->insert_id;
        $ins->close();

        pbg_money_log($machineId, $betId, 1, -$amount, $bal, $after, 'bet', $fid, $mbUid);
        $db->commit();

        pbg_settle_pending();

        $fresh = pbg_get_member_by_fid($fid);
        pbg_json([
            'status' => 'success',
            'data' => [
                'bet_id' => $betId,
                'balance' => (float)$fresh['mb_money'],
                'round' => $round,
                'label' => pbg_mode_label_cn($mode),
            ],
        ]);
    } catch (Throwable $e) {
        $db->rollback();
        throw $e;
    }
}

function api_cancel()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        pbg_json(['status' => 'fail', 'message' => 'POST required'], 405);
    }
    $auth = pbg_auth_member(true);
    $member = $auth['member'];

    pbg_settle_pending();

    $db = pbg_db();
    $drawDb = pbg_db('draw_db');
    $db->begin_transaction();
    try {
        $fid = (int)$member['mb_fid'];
        $mbUid = $member['mb_uid'];
        $stmt = pbg_prepare($db, 'SELECT * FROM bets WHERE mb_fid=? AND state=1 ORDER BY id DESC');
        $stmt->bind_param('i', $fid);
        $stmt->execute();
        $pending = pbg_stmt_fetch_all($stmt);
        $stmt->close();
        $bets = [];
        foreach ($pending as $r) {
            $rnd = (int)$r['round'];
            $chk = $drawDb->prepare('SELECT round FROM draw_results WHERE round=? LIMIT 1');
            if (!($chk instanceof mysqli_stmt)) {
                continue;
            }
            $chk->bind_param('i', $rnd);
            $chk->execute();
            $hasDraw = (bool)pbg_stmt_fetch_one($chk);
            $chk->close();
            if (!$hasDraw) {
                $bets[] = $r;
            }
        }

        if (!$bets) {
            $db->rollback();
            pbg_json(['status' => 'fail', 'code' => 'NO_CANCEL', 'message' => '没有可取消的投注']);
        }

        $lock = pbg_prepare($db, 'SELECT mb_money FROM member WHERE mb_fid=? FOR UPDATE');
        $lock->bind_param('i', $fid);
        $lock->execute();
        $lockRow = pbg_stmt_fetch_one($lock);
        $lock->close();
        if (!$lockRow) {
            throw new RuntimeException('member lock failed');
        }
        $bal = (float)$lockRow['mb_money'];

        $refunded = 0;
        foreach ($bets as $bet) {
            $amt = (float)$bet['amount'];
            $before = $bal;
            $bal += $amt;
            $bid = (int)$bet['id'];
            $updBet = $db->prepare('UPDATE bets SET state=4, settled_at=NOW() WHERE id=? AND state=1');
            $updBet->bind_param('i', $bid);
            $updBet->execute();
            $updBet->close();
            pbg_money_log((int)$bet['machine_id'], $bid, 3, $amt, $before, $bal, 'cancel', $fid, $mbUid);
            $refunded += $amt;
        }

        $upd = $db->prepare('UPDATE member SET mb_money=? WHERE mb_fid=?');
        $upd->bind_param('di', $bal, $fid);
        $upd->execute();
        $upd->close();
        $db->commit();

        pbg_json([
            'status' => 'success',
            'data' => [
                'cancelled' => count($bets),
                'refunded' => $refunded,
                'balance' => $bal,
            ],
        ]);
    } catch (Throwable $e) {
        $db->rollback();
        throw $e;
    }
}

function api_settle()
{
    $n = pbg_settle_pending();
    pbg_json(['status' => 'success', 'data' => ['settled' => $n]]);
}

/**
 * Pull current/next draw from local powerball HTTP, then settle waits.
 * ?force=1 — ignore 5-min window (manual/debug)
 */
function api_fetch_draw()
{
    $force = isset($_GET['force']) && ($_GET['force'] === '1' || $_GET['force'] === 'true');
    $result = pbg_fetch_powerball_draw($force);
    $settled = 0;
    if (!empty($result['ok']) && !empty($result['fetched'])) {
        $settled = pbg_settle_pending();
    } elseif (!empty($result['ok'])) {
        // outside window: still try settle in case DB already has rows
        $settled = pbg_settle_pending();
    }

    if (empty($result['ok'])) {
        pbg_json([
            'status' => 'fail',
            'message' => $result['error'] ?: 'fetch failed',
            'data' => [
                'http_code' => $result['http_code'],
                'settled' => $settled,
            ],
        ], 502);
    }

    pbg_json([
        'status' => 'success',
        'data' => [
            'fetched' => !empty($result['fetched']),
            'skipped' => isset($result['skipped']) ? $result['skipped'] : '',
            'draw' => $result['draw'],
            'last_round' => isset($result['last_round']) ? $result['last_round'] : null,
            'settled' => $settled,
            'round_info' => pbg_current_round_info(),
        ],
    ]);
}

/**
 * Settle all wait bets whose draw_results row exists.
 */
function pbg_settle_pending()
{
    $db = pbg_db();
    $drawDb = pbg_db('draw_db');
    $roundRows = pbg_query_fetch_all($db, 'SELECT DISTINCT round FROM bets WHERE state=1 ORDER BY round ASC');
    $settled = 0;
    foreach ($roundRows as $rr) {
        $round = (int)$rr['round'];
        $stmt = $drawDb->prepare('SELECT * FROM draw_results WHERE round=? LIMIT 1');
        if (!($stmt instanceof mysqli_stmt)) {
            continue;
        }
        $stmt->bind_param('i', $round);
        if (!$stmt->execute()) {
            $stmt->close();
            continue;
        }
        $draw = pbg_stmt_fetch_one($stmt);
        $stmt->close();
        if (!$draw) {
            continue;
        }
        $cls = pbg_classify_draw($draw['powerball'], $draw['ball_sum']);
        $settled += pbg_settle_round($round, $cls);
    }
    return $settled;
}

function pbg_settle_round($round, $cls)
{
    $db = pbg_db();
    $stmt = $db->prepare('SELECT * FROM bets WHERE round=? AND state=1');
    if (!($stmt instanceof mysqli_stmt)) {
        return 0;
    }
    $stmt->bind_param('i', $round);
    if (!$stmt->execute()) {
        $stmt->close();
        return 0;
    }
    $bets = pbg_stmt_fetch_all($stmt);
    $stmt->close();
    $count = 0;
    foreach ($bets as $bet) {
        $db->begin_transaction();
        try {
            $bid = (int)$bet['id'];
            $chk = pbg_prepare($db, 'SELECT * FROM bets WHERE id=? AND state=1 FOR UPDATE');
            $chk->bind_param('i', $bid);
            $chk->execute();
            $row = pbg_stmt_fetch_one($chk);
            $chk->close();
            if (!$row) {
                $db->rollback();
                continue;
            }

            $win = pbg_is_win($row['mode'], $row['target'], $cls);
            $resultStr = $cls['result_1'] . $cls['result_2'] . $cls['result_3'] . $cls['result_4'];
            if ($win) {
                $winMoney = (int)((float)$row['amount'] * (float)$row['ratio']);
                $state = 3;
            } else {
                $winMoney = 0;
                $state = 2;
            }

            $upd = pbg_prepare($db, 'UPDATE bets SET state=?, win_amount=?, result=?, settled_at=NOW() WHERE id=?');
            $upd->bind_param('idsi', $state, $winMoney, $resultStr, $bid);
            $upd->execute();
            $upd->close();

            if ($win && $winMoney > 0) {
                $mbFid = (int)$row['mb_fid'];
                $mbUid = (string)$row['mb_uid'];
                if ($mbFid > 0) {
                    $lock = pbg_prepare($db, 'SELECT mb_money FROM member WHERE mb_fid=? FOR UPDATE');
                    $lock->bind_param('i', $mbFid);
                    $lock->execute();
                    $lockRow = pbg_stmt_fetch_one($lock);
                    $lock->close();
                    if (!$lockRow) {
                        throw new RuntimeException('member lock failed');
                    }
                    $bal = (float)$lockRow['mb_money'];
                    $after = $bal + $winMoney;
                    $um = pbg_prepare($db, 'UPDATE member SET mb_money=? WHERE mb_fid=?');
                    $um->bind_param('di', $after, $mbFid);
                    $um->execute();
                    $um->close();
                    pbg_money_log((int)$row['machine_id'], $bid, 2, $winMoney, $bal, $after, 'win', $mbFid, $mbUid);
                } else {
                    $mid = (int)$row['machine_id'];
                    $lock = pbg_prepare($db, 'SELECT balance FROM machines WHERE id=? FOR UPDATE');
                    $lock->bind_param('i', $mid);
                    $lock->execute();
                    $lockRow = pbg_stmt_fetch_one($lock);
                    $lock->close();
                    if (!$lockRow) {
                        throw new RuntimeException('machine lock failed');
                    }
                    $bal = (float)$lockRow['balance'];
                    $after = $bal + $winMoney;
                    $um = pbg_prepare($db, 'UPDATE machines SET balance=? WHERE id=?');
                    $um->bind_param('di', $after, $mid);
                    $um->execute();
                    $um->close();
                    pbg_money_log($mid, $bid, 2, $winMoney, $bal, $after, 'win');
                }
            }

            $db->commit();
            $count++;
        } catch (Throwable $e) {
            $db->rollback();
        }
    }
    return $count;
}
