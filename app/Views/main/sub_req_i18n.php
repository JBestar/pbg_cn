<?php
$subReqKind = (isset($sub_req_kind) && $sub_req_kind === 'exchange') ? 'exchange' : 'charge';
$subReqKeys = array(
    'btn_confirm', 'btn_cancel', 'btn_delete',
    'state_unconfirmed', 'state_permitted', 'state_refused',
    'type_charge_req', 'type_egg_charge', 'type_exchange_req', 'type_egg_recover',
    'msg_confirm_permit', 'msg_confirm_refuse', 'msg_confirm_delete',
    'msg_over_money', 'msg_over_money_store', 'msg_proc_fail',
);
$subReqI18n = array();
foreach ($subReqKeys as $k) {
    $subReqI18n[$k] = lang('Admin.' . $k);
}
?>
<script>
window.SUB_REQ_CONF = { kind: <?= json_encode($subReqKind) ?> };
window.ADMIN_I18N = window.ADMIN_I18N || {};
(function (src) {
    for (var k in src) {
        if (Object.prototype.hasOwnProperty.call(src, k)) window.ADMIN_I18N[k] = src[k];
    }
})(<?= json_encode($subReqI18n, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>);
</script>
