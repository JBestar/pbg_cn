<!doctype html>
<html lang="<?= esc(service('request')->getLocale() ?: 'ko') ?>">
<head>
<meta charset="utf-8">
<title><?= lang('Admin.title_store_reg') ?></title>
<link rel="stylesheet" href="/assets/css/main.css?v=2">
<link rel="stylesheet" href="/assets/css/button1.css">
<script src="/assets/js/lib/jquery-1.12.4.min.js"></script>
<script src="/assets/js/lib/sweetalert2.all.min.js"></script>
<style>
body{margin:0;padding:16px 20px;background:#fff;font-size:14px;}
.popup-title{font-size:18px;font-weight:700;margin:0 0 14px;color:#222;}
.popup-table{width:100%;border-collapse:collapse;}
.popup-table td{border:0;padding:6px 4px;vertical-align:middle;}
.popup-table td.lbl{width:120px;}
.popup-table input[type=text],.popup-table input[type=password]{width:140px;padding:4px 6px;}
.hint{color:#666;font-size:12px;}
.actions{text-align:center;padding-top:16px;}
.btn_green{background:#2e7d32;color:#fff;border:none;padding:8px 20px;cursor:pointer;border-radius:4px;}
.btn_red{background:#e53935;color:#fff;border:none;padding:8px 20px;cursor:pointer;border-radius:4px;}
.amt-row button{background:#43a047;color:#fff;border:none;padding:6px 10px;margin:2px;border-radius:4px;cursor:pointer;}
</style>
</head>
<body>
<div class="popup-title"><?= lang('Admin.title_store_reg') ?></div>
<table class="popup-table">
<tr><td class="lbl"><?= lang('Admin.label_id') ?></td><td><input type="text" id="regSubId"></td><td class="hint"><?= lang('Admin.hint_dup_id') ?></td></tr>
<tr><td class="lbl"><?= lang('Admin.label_name') ?></td><td><input type="text" id="regSubName"></td><td class="hint"><?= lang('Admin.hint_dup_name') ?></td></tr>
<tr><td class="lbl"><?= lang('Admin.label_password') ?></td><td><input type="text" id="regSubPwd"></td><td></td></tr>
<tr><td class="lbl"><?= lang('Admin.label_bank_pwd') ?></td><td><input type="text" id="regSubExcPwd"></td><td></td></tr>
<tr><td class="lbl"><?= lang('Admin.label_phone') ?></td><td><input type="text" id="regSubPhone"></td><td></td></tr>
<tr><td class="lbl"><?= lang('Admin.label_bank_info') ?></td><td colspan="2">
<input type="text" id="regSubBank" placeholder="<?= lang('Admin.ph_bank_name') ?>">
<input type="text" id="regSubBankNum" style="width:160px" placeholder="<?= lang('Admin.ph_bank_num') ?>">
<input type="text" id="regSubBankOwner" placeholder="<?= lang('Admin.ph_bank_owner') ?>">
</td></tr>
<tr><td class="lbl"><?= lang('Admin.label_fee') ?></td><td><input type="text" id="regSubSingleDealRate" value="0"> %</td><td></td></tr>
</table>
<div class="actions">
<button type="button" class="btn_green" onclick="reqRegMember();"><?= lang('Admin.btn_register') ?></button>
<button type="button" class="btn_red" onclick="window.close();"><?= lang('Admin.btn_close') ?></button>
</div>
<script src="/assets/js/store_popup.js?v=<?= time() ?>"></script>
<script>initStoreReg();</script>
</body>
</html>
