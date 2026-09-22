<!doctype html>
<html lang="<?= esc(service('request')->getLocale() ?: 'ko') ?>">
<head>
<meta charset="utf-8">
<title><?= lang('Admin.title_egg_recover') ?></title>
<link rel="stylesheet" href="/assets/css/main.css?v=6">
<script src="/assets/js/lib/jquery-1.12.4.min.js"></script>
<script src="/assets/js/lib/sweetalert2.all.min.js"></script>
<style>
body{margin:0;padding:16px 20px;background:#fff;font-size:14px;min-height:360px;}
.popup-title{font-size:18px;font-weight:700;margin:0 0 16px;}
.row{margin:8px 0;line-height:1.6;}
.lbl{display:inline-block;width:100px;font-weight:700;}
#serviceExchangeMoney{width:120px;padding:4px 6px;}
.amt-row{margin:12px 0;}
.amt-row button{background:#43a047;color:#fff;border:none;padding:7px 12px;margin:2px;border-radius:4px;cursor:pointer;}
.actions{margin-top:20px;text-align:center;}
.btn_blue{background:#1a73e8;color:#fff;border:none;padding:8px 22px;cursor:pointer;border-radius:4px;}
.btn_red{background:#e53935;color:#fff;border:none;padding:8px 22px;cursor:pointer;border-radius:4px;margin-left:8px;}
</style>
</head>
<body>
<div class="popup-title"><?= lang('Admin.title_egg_recover') ?></div>
<div class="row"><span class="lbl"><?= lang('Admin.label_to_user') ?></span> <span id="serviceExchangeRecverId"></span></div>
<div class="row"><span class="lbl"><?= lang('Admin.label_hold_egg') ?></span> <span id="serviceExchangeRecverMoney"></span></div>
<div class="row"><span class="lbl"><?= lang('Admin.label_sender') ?></span> <span id="serviceExchangeSenderId"></span></div>
<div class="row"><span class="lbl"><?= lang('Admin.label_hold_egg') ?></span> <span id="serviceExchangeSenderMoney"></span></div>
<input type="hidden" id="serviceExchangeSenderUid" value="<?= esc($target_uid) ?>">
<div class="row"><span class="lbl"><?= lang('Admin.label_recover_egg') ?></span> <input type="text" id="serviceExchangeMoney" value="0"></div>
<div class="amt-row">
<button type="button" onclick="addRecoverAmt(10000);"><?= lang('Admin.amt_1w') ?></button>
<button type="button" onclick="addRecoverAmt(50000);"><?= lang('Admin.amt_5w') ?></button>
<button type="button" onclick="addRecoverAmt(100000);"><?= lang('Admin.amt_10w') ?></button>
<button type="button" onclick="addRecoverAmt(500000);"><?= lang('Admin.amt_50w') ?></button>
<button type="button" onclick="addRecoverAmt(1000000);"><?= lang('Admin.amt_100w') ?></button>
<button type="button" onclick="addRecoverAmt(0);"><?= lang('Admin.btn_reset_amt') ?></button>
</div>
<div class="actions">
<button type="button" class="btn_blue" onclick="reqServiceExchange();"><?= lang('Admin.btn_egg_recover') ?></button>
<button type="button" class="btn_red" onclick="window.close();"><?= lang('Admin.btn_close') ?></button>
</div>
<script>
window.ADMIN_I18N = window.ADMIN_I18N || {};
window.ADMIN_I18N.btn_ok = <?= json_encode(lang('Admin.btn_ok'), JSON_UNESCAPED_UNICODE) ?>;
</script>
<script src="/assets/js/store_popup.js?v=<?= time() ?>"></script>
<script>initStoreRecover();</script>
</body>
</html>
