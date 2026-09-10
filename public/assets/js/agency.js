$(document).ready(function() {
    $('#inputSubId').val('');
    reqPage();
});

function reqSearch() {
    reqPage();
}

function t(key, fallback) {
    return (window.ADMIN_I18N && window.ADMIN_I18N[key]) ? window.ADMIN_I18N[key] : fallback;
}

function displayPwd(p) {
    p = String(p == null ? '' : p);
    if (!p) return '';
    if (p.indexOf('$2y$') === 0 || p.indexOf('$2a$') === 0 || p.indexOf('$argon') === 0) return '—';
    return p;
}

function escAttr(s) {
    return String(s == null ? '' : s).replace(/\\/g, '\\\\').replace(/'/g, "\\'");
}

function showPage(arrInfo) {
    var tHtml = '';
    var totalMember = 0;
    if (arrInfo != null) {
        totalMember = arrInfo.length;
        for (var idx in arrInfo) {
            var r = arrInfo[idx];
            // skip deleted-looking rows only if needed
            var bet = parseInt(r.bet_sum, 10) || 0;
            var win = parseInt(r.bet_win_sum, 10) || 0;
            var empl = parseInt(r.bet_empl_sum, 10) || 0;
            var agen = parseInt(r.bet_agen_sum, 10) || 0;
            var profit = bet - win - empl - agen;
            var eggIn = parseInt(r.charge_present_sum, 10) || 0;
            var eggOut = parseInt(r.exchange_present_sum, 10) || 0;

            tHtml += '<tr id="trFid-' + r.mb_fid + '">';
            tHtml += '<td class="tdDate">' + (parseInt(idx, 10) + 1) + '</td>';
            tHtml += '<td class="tdDate">' + getMemberLevelText(r.mb_level) + '</td>';
            tHtml += '<td class="tdDate">' + r.mb_uid + '</td>';
            tHtml += '<td class="tdDate">' + r.mb_nickname + '</td>';
            tHtml += '<td class="tdDate">' + displayPwd(r.mb_pwd) + '</td>';
            tHtml += '<td class="tdMoney">' + (parseInt(r.mb_money, 10) || 0).toLocaleString() + '</td>';
            tHtml += '<td class="tdMoney">' + (parseInt(r.mb_point, 10) || 0).toLocaleString() + '</td>';
            tHtml += '<td class="tdMoney">' + bet.toLocaleString() + '</td>';
            tHtml += '<td class="tdMoney">';
            if (profit >= 0) tHtml += '<font color="#0000fe">';
            else tHtml += '<font color="#fe0000">';
            tHtml += profit.toLocaleString() + '</font></td>';
            tHtml += '<td class="tdMoney">' + (parseInt(r.mb_limit_round, 10) || 0).toLocaleString() + '</td>';
            tHtml += '<td class="tdMoney">' + (parseInt(r.mb_limit_single, 10) || 0).toLocaleString() + '</td>';
            tHtml += '<td class="tdMoney">' + (parseInt(r.mb_limit_mix, 10) || 0).toLocaleString() + '</td>';
            tHtml += '<td class="tdMoney">' + eggIn.toLocaleString() + '</td>';
            tHtml += '<td class="tdMoney">' + eggOut.toLocaleString() + '</td>';
            tHtml += '<td class="tdDate">';
            tHtml += '<button type="button" class="btn-store-action btn-store-blue" onclick="openStoreCharge(\''
                + escAttr(r.mb_uid) + '\')">' + t('btn_egg_charge', '충전') + '</button> ';
            tHtml += '<button type="button" class="btn-store-action btn-store-red" onclick="openStoreRecover(\''
                + escAttr(r.mb_uid) + '\')">' + t('btn_egg_recover', '회수') + '</button>';
            tHtml += '</td><td class="tdDate">';
            tHtml += '<button type="button" class="btn-store-action btn-store-blue" onclick="openStoreEdit('
                + r.mb_fid + ')">' + t('btn_edit', '수정') + '</button> ';
            if (parseInt(r.mb_state_active, 10) == 1) {
                tHtml += '<button type="button" class="btn-store-action btn-store-blue" onclick="reqPermitMember('
                    + r.mb_fid + ', 0);">' + t('btn_approve', '승인') + '</button>';
            } else {
                tHtml += '<button type="button" class="btn-store-action btn-store-red" onclick="reqPermitMember('
                    + r.mb_fid + ', 1);">' + t('btn_block', '차단') + '</button>';
            }
            tHtml += '</td>';
            tHtml += '<td class="tdDate">' + (r.mb_time_join || '') + '</td>';
            tHtml += '</tr>';
        }
    }
    $('#divSearchResult').html('Total ' + totalMember);
    $('#tbodyList').html(tHtml);
}

function openStoreReg() {
    window.open('/Main/store_reg', 'storeReg', 'width=640,height=720,scrollbars=yes,resizable=yes');
}

function openStoreEdit(fid) {
    window.open('/Main/store_edit?fid=' + encodeURIComponent(fid), 'storeEdit_' + fid,
        'width=640,height=780,scrollbars=yes,resizable=yes');
}

function openStoreCharge(uid) {
    window.open('/Main/store_charge?uid=' + encodeURIComponent(uid), 'storeCharge_' + uid,
        'width=560,height=420,scrollbars=yes,resizable=yes');
}

function openStoreRecover(uid) {
    window.open('/Main/store_recover?uid=' + encodeURIComponent(uid), 'storeRecover_' + uid,
        'width=560,height=420,scrollbars=yes,resizable=yes');
}

function reqPermitMember(fid, active) {
    var objData = { fid: fid, active: active };
    $.ajax({
        url: '/api/member_permit',
        data: { json_: JSON.stringify(objData) },
        type: 'post',
        dataType: 'json',
        success: function (jResult) {
            if (jResult.status === 'success') reqPage();
            else if (jResult.status === 'logout') location.reload();
        }
    });
}

function reqPage() {
    var objData = {
        start: $('#inputDateS').val(),
        end: $('#inputDateE').val(),
        mb_uid: $('#inputSubId').val()
    };
    $.ajax({
        url: '/api/member_list',
        data: { json_: JSON.stringify(objData) },
        type: 'post',
        dataType: 'json',
        success: function (jResult) {
            if (jResult.status === 'success') {
                if (jResult.info) window.mUser = jResult.info;
                showPage(jResult.data);
            } else if (jResult.status === 'logout') {
                location.reload();
            }
        }
    });
}

// opener refresh hook for popups
window.refreshStoreList = function () {
    reqPage();
    if (typeof reqAssets === 'function') reqAssets();
};
