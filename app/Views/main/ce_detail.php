<!doctype html>
<html lang="<?= esc(service('request')->getLocale() ?: 'ko') ?>">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <title><?= lang('Admin.'.$title_key) ?></title>
    <link rel="stylesheet" href="/assets/css/lib/jquery-ui.css">
    <link rel="stylesheet" href="/assets/css/main.css?v=6">
    <link rel="stylesheet" href="/assets/css/button1.css">
    <script src="/assets/js/lib/jquery-1.12.4.min.js"></script>
    <script src="/assets/js/lib/jquery-ui-1.12.1.min.js"></script>
    <style>
        body { margin: 0; padding: 16px 20px 24px; background: #fff; }
        .detail-title { font-size: 20px; font-weight: 700; color: #1a3a6e; margin: 0 0 14px; }
        .detail-search { margin-bottom: 12px; }
        .detail-search .btn_search { margin-left: 8px; }
        .detail-footer { text-align: right; margin-top: 16px; }
        .default_table { width: 100%; border-collapse: collapse; }
        .default_table th, .default_table td { border: 1px solid #999; padding: 6px 8px; text-align: center; font-size: 13px; }
        .default_table th { background: #eee; }
        .default_table td.tdMoney { text-align: center; }
        .btn-close-detail {
            display: inline-block; padding: 8px 28px; background: #1a73e8; color: #fff;
            border: none; border-radius: 4px; cursor: pointer; font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="detail-title"><?= lang('Admin.'.$title_key) ?></div>
    <div class="detail-search">
        <input type="date" id="inputDateS" value="<?= esc($start) ?>" class="inputDate">
        &nbsp;~&nbsp;
        <input type="date" id="inputDateE" value="<?= esc($end) ?>" class="inputDate">
        <button type="button" class="btn_search btn_icon" onclick="reqDetailSearch();" title="<?= lang('Admin.btn_search') ?>" aria-label="<?= lang('Admin.btn_search') ?>"><i class="fas fa-search"></i></button>
    </div>
    <table class="default_table">
        <thead>
            <tr>
                <th><?= lang('Admin.th_no') ?>.</th>
                <th><?= lang('Admin.th_uid') ?></th>
                <th><?= lang('Admin.th_name') ?></th>
                <th><?= lang('Admin.th_money_before') ?></th>
                <th><?= lang('Admin.th_charge_amount') ?></th>
                <th><?= lang('Admin.th_exchange_recover') ?></th>
                <th><?= lang('Admin.th_point_earn') ?></th>
                <th><?= lang('Admin.th_money_after') ?></th>
                <th><?= lang('Admin.th_change_time') ?></th>
            </tr>
        </thead>
        <tbody id="tbodyDetail"></tbody>
    </table>
    <div class="detail-footer">
        <button type="button" class="btn-close-detail" onclick="window.close();"><?= lang('Admin.btn_close') ?></button>
    </div>
    <script>
    window.DETAIL_UID = <?= json_encode((string)$detail_uid, JSON_UNESCAPED_UNICODE) ?>;
    window.CE_SCOPE = <?= json_encode((string)$scope, JSON_UNESCAPED_UNICODE) ?>;
    </script>
    <script src="/assets/js/ce_detail.js?v=<?= time() ?>"></script>
</body>
</html>
