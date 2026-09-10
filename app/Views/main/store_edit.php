<!doctype html>
<html lang="<?= esc(service('request')->getLocale() ?: 'ko') ?>">
<head>
<meta charset="utf-8">
<title><?= lang('Admin.title_store_edit') ?></title>
<link rel="stylesheet" href="/assets/css/main.css?v=2">
<link rel="stylesheet" href="/assets/css/button1.css">
<script src="/assets/js/lib/jquery-1.12.4.min.js"></script>
<script src="/assets/js/lib/sweetalert2.all.min.js"></script>
<style>
body{margin:0;padding:16px 20px;background:#fff;font-size:14px;}
.popup-title{font-size:18px;font-weight:700;margin:0 0 14px;color:#222;}
.popup-table{width:100%;border-collapse:collapse;}
.popup-table td{border:0;padding:6px 4px;vertical-align:middle;}
.popup-table td.lbl{width:130px;}
.popup-table input[type=text],.popup-table input[type=password]{width:140px;padding:4px 6px;}
.hint{color:#666;font-size:12px;margin-left:6px;}
.actions{text-align:center;padding-top:16px;}
.btn_green{background:#2e7d32;color:#fff;border:none;padding:8px 20px;cursor:pointer;border-radius:4px;}
.btn_red{background:#e53935;color:#fff;border:none;padding:8px 20px;cursor:pointer;border-radius:4px;}
</style>
</head>
<body>
<div class="popup-title"><?= lang('Admin.title_store_edit') ?></div>
<input type="hidden" id="editSubNo">
<table class="popup-table">
<tr><td class="lbl"><?= lang('Admin.label_id') ?></td><td><span id="editSubId" style="font-weight:700"></span></td></tr>
<tr><td class="lbl"><?= lang('Admin.label_name') ?></td><td><span id="editSubName" style="font-weight:700"></span></td></tr>
<tr><td class="lbl"><?= lang('Admin.label_password') ?></td><td><input type="text" id="editSubPwd"></td></tr>
<tr><td class="lbl"><?= lang('Admin.label_bank_pwd') ?></td><td><input type="text" id="editSubExcPwd"></td></tr>
<tr><td class="lbl"><?= lang('Admin.label_phone') ?></td><td><input type="text" id="editSubPhone"></td></tr>
<tr><td class="lbl"><?= lang('Admin.label_bank_info') ?></td><td>
<input type="text" id="editSubBank" placeholder="<?= lang('Admin.ph_bank_name') ?>">
<input type="text" id="editSubBankNum" style="width:160px" placeholder="<?= lang('Admin.ph_bank_num') ?>">
<input type="text" id="editSubBankOwner" placeholder="<?= lang('Admin.ph_bank_owner') ?>">
</td></tr>
<tr><td class="lbl"><?= lang('Admin.th_round_limit') ?></td><td><input type="text" id="editSubLimitRound"></td></tr>
<tr><td class="lbl"><?= lang('Admin.th_single_limit_short') ?></td><td><input type="text" id="editSubLimitSingle"></td></tr>
<tr><td class="lbl"><?= lang('Admin.th_mix_limit_short') ?></td><td><input type="text" id="editSubLimitMix"></td></tr>
<tr><td class="lbl"><?= lang('Admin.th_mix_limit') ?>3</td><td><input type="text" id="editSubLimitThree"></td></tr>
<tr><td class="lbl"><?= lang('Admin.th_digit_limit') ?></td><td><input type="text" id="editSubLimitDigit"></td></tr>
<tr><td class="lbl"><?= lang('Admin.label_fee') ?></td><td><input type="text" id="editSubSingleDealRate"> % <span class="hint" id="hintFeeMax"></span></td></tr>
</table>
<div class="actions">
<button type="button" class="btn_green" onclick="reqEditMember();"><?= lang('Admin.btn_change') ?></button>
<button type="button" class="btn_red" onclick="window.close();"><?= lang('Admin.btn_close') ?></button>
</div>
<script>
window.STORE_EDIT_FID = <?= json_encode((int)$edit_fid) ?>;
window.ADMIN_I18N = window.ADMIN_I18N || {};
window.ADMIN_I18N.btn_ok = <?= json_encode(lang('Admin.btn_ok'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.hint_fee_max = <?= json_encode(lang('Admin.hint_fee_max'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.msg_fee_over = <?= json_encode(lang('Admin.msg_fee_over'), JSON_UNESCAPED_UNICODE) ?>;
</script>
<script src="/assets/js/store_popup.js?v=<?= time() ?>"></script>
<script>initStoreEdit();</script>
</body>
</html>
