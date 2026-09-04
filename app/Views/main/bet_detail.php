<!doctype html>
<html lang="<?= esc(service('request')->getLocale() ?: 'ko') ?>">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <title><?= lang('Admin.title_bet_detail') ?></title>
    <link rel="stylesheet" href="/assets/css/lib/jquery-ui.css">
    <link rel="stylesheet" href="/assets/css/main.css?v=2">
    <link rel="stylesheet" href="/assets/css/button1.css">
    <script src="/assets/js/lib/jquery-1.12.4.min.js"></script>
    <script src="/assets/js/lib/jquery-ui-1.12.1.min.js"></script>
    <script src="/assets/js/util.js?v=5"></script>
    <script>
    window.ADMIN_I18N = {
        mark_odd: <?= json_encode(lang('Admin.mark_odd'), JSON_UNESCAPED_UNICODE) ?>,
        mark_even: <?= json_encode(lang('Admin.mark_even'), JSON_UNESCAPED_UNICODE) ?>,
        mark_under: <?= json_encode(lang('Admin.mark_under'), JSON_UNESCAPED_UNICODE) ?>,
        mark_over: <?= json_encode(lang('Admin.mark_over'), JSON_UNESCAPED_UNICODE) ?>,
        mark_p: <?= json_encode(lang('Admin.mark_p'), JSON_UNESCAPED_UNICODE) ?>,
        mark_b: <?= json_encode(lang('Admin.mark_b'), JSON_UNESCAPED_UNICODE) ?>,
        mark_pb: <?= json_encode(lang('Admin.mark_pb'), JSON_UNESCAPED_UNICODE) ?>,
        mark_power: <?= json_encode(lang('Admin.mark_power'), JSON_UNESCAPED_UNICODE) ?>,
        room1: <?= json_encode(lang('Admin.room1'), JSON_UNESCAPED_UNICODE) ?>,
        room2: <?= json_encode(lang('Admin.room2'), JSON_UNESCAPED_UNICODE) ?>,
        room3: <?= json_encode(lang('Admin.room3'), JSON_UNESCAPED_UNICODE) ?>,
        room4: <?= json_encode(lang('Admin.room4'), JSON_UNESCAPED_UNICODE) ?>,
        bet_waiting: <?= json_encode(lang('Admin.bet_waiting'), JSON_UNESCAPED_UNICODE) ?>,
        bet_lose: <?= json_encode(lang('Admin.bet_lose'), JSON_UNESCAPED_UNICODE) ?>,
        bet_win: <?= json_encode(lang('Admin.bet_win'), JSON_UNESCAPED_UNICODE) ?>,
        bet_void: <?= json_encode(lang('Admin.bet_void'), JSON_UNESCAPED_UNICODE) ?>
    };
    window.DETAIL_UID = <?= json_encode((string)$detail_uid, JSON_UNESCAPED_UNICODE) ?>;
    </script>
    <style>
        body { margin: 0; padding: 16px 20px 24px; background: #fff; }
        .detail-title { font-size: 20px; font-weight: 700; color: #1a3a6e; margin: 0 0 14px; }
        .detail-search { margin-bottom: 12px; }
        .detail-search .btn_search { margin-left: 8px; }
        .detail-footer { text-align: right; margin-top: 16px; }
        .default_table { width: 100%; border-collapse: collapse; }
        .default_table th, .default_table td { border: 1px solid #999; padding: 6px 8px; text-align: center; font-size: 13px; }
        .default_table th { background: #eee; }
        .btn-close-detail {
            display: inline-block; padding: 8px 28px; background: #1a73e8; color: #fff;
            border: none; border-radius: 4px; cursor: pointer; font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="detail-title"><?= lang('Admin.title_bet_detail') ?></div>
    <div class="detail-search">
        <input type="date" id="inputDateS" value="<?= esc($start) ?>" class="inputDate">
        &nbsp;~&nbsp;
        <input type="date" id="inputDateE" value="<?= esc($end) ?>" class="inputDate">
        <input type="button" class="btn_search" onclick="reqDetailSearch();" value="<?= lang('Admin.btn_search') ?>">
    </div>
    <table class="default_table">
        <thead>
            <tr>
                <th><?= lang('Admin.th_day_round') ?></th>
                <th><?= lang('Admin.th_uid') ?></th>
                <th><?= lang('Admin.th_buyer_name') ?></th>
                <th><?= lang('Admin.th_bet_history') ?></th>
                <th><?= lang('Admin.th_purchase_amount') ?></th>
                <th><?= lang('Admin.th_after_purchase') ?></th>
                <th><?= lang('Admin.th_hit_amount') ?></th>
                <th><?= lang('Admin.th_point') ?></th>
                <th><?= lang('Admin.th_purchase_time') ?></th>
            </tr>
        </thead>
        <tbody id="tbodyDetail"></tbody>
    </table>
    <div class="detail-footer">
        <button type="button" class="btn-close-detail" onclick="window.close();"><?= lang('Admin.btn_close') ?></button>
    </div>
    <script src="/assets/js/bet_detail.js?v=<?= time() ?>"></script>
</body>
</html>
