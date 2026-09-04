<!doctype html>
<html lang="<?= esc(service('request')->getLocale() ?: 'ko') ?>">
<head>
<meta charset="utf-8">
<title><?= lang('Admin.title_egg_charge') ?></title>
<link rel="stylesheet" href="/assets/css/main.css?v=2">
<script src="/assets/js/lib/jquery-1.12.4.min.js"></script>
<script src="/assets/js/lib/sweetalert2.all.min.js"></script>
<style>
body{margin:0;padding:16px 20px;background:#fff;font-size:14px;position:relative;min-height:360px;}
.popup-title{font-size:18px;font-weight:700;margin:0 0 16px;}
.row{margin:8px 0;line-height:1.6;}
.lbl{display:inline-block;width:100px;font-weight:700;}
#serviceChargeMoney{width:120px;padding:4px 6px;}
.amt-row{margin:12px 0;}
.amt-row button{background:#43a047;color:#fff;border:none;padding:7px 12px;margin:2px;border-radius:4px;cursor:pointer;}
.actions{margin-top:20px;text-align:center;}
.btn_blue{background:#1a73e8;color:#fff;border:none;padding:8px 22px;cursor:pointer;border-radius:4px;}
.btn_red{background:#e53935;color:#fff;border:none;padding:8px 22px;cursor:pointer;border-radius:4px;margin-left:8px;}
</style>
</head>
<body>
<div class="popup-title"><?= lang('Admin.title_egg_charge') ?></div>
<div class="row"><span class="lbl"><?= lang('Admin.label_sender') ?></span> <span id="serviceChargeSenderId"></span></div>
<div class="row"><span class="lbl"><?= lang('Admin.label_hold_egg') ?></span> <span id="serviceChargeSenderMoney"></span></div>
<div class="row"><span class="lbl"><?= lang('Admin.label_to_user') ?></span> <span id="serviceChargeRecverId"></span></div>
<div class="row"><span class="lbl"><?= lang('Admin.label_hold_egg') ?></span> <span id="serviceChargeRecverMoney"></span></div>
<input type="hidden" id="serviceChargeRecverUid" value="<?= esc($target_uid) ?>">
<div class="row"><span class="lbl"><?= lang('Admin.label_charge_egg') ?></span> <input type="text" id="serviceChargeMoney" value="0"></div>
<div class="amt-row">
<button type="button" onclick="addChargeAmt(10000);"><?= lang('Admin.amt_1w') ?></button>
<button type="button" onclick="addChargeAmt(50000);"><?= lang('Admin.amt_5w') ?></button>
<button type="button" onclick="addChargeAmt(100000);"><?= lang('Admin.amt_10w') ?></button>
<button type="button" onclick="addChargeAmt(500000);"><?= lang('Admin.amt_50w') ?></button>
<button type="button" onclick="addChargeAmt(1000000);"><?= lang('Admin.amt_100w') ?></button>
<button type="button" onclick="addChargeAmt(0);"><?= lang('Admin.btn_reset_amt') ?></button>
</div>
<div class="actions">
<button type="button" class="btn_blue" onclick="reqServiceCharge();"><?= lang('Admin.btn_egg_charge') ?></button>
<button type="button" class="btn_red" onclick="window.close();"><?= lang('Admin.btn_close') ?></button>
</div>
<script>
window.MSG_CHARGE_NEED = <?= json_encode(lang('Admin.label_charge_egg') . '?', JSON_UNESCAPED_UNICODE) ?>;
window.MSG_CHARGE_OK = <?= json_encode('OK', JSON_UNESCAPED_UNICODE) ?>;
</script>
<script src="/assets/js/store_popup.js?v=<?= time() ?>"></script>
<script>initStoreCharge();</script>
</body>
</html>
