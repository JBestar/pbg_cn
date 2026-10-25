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

function escHtml(s) {
    return String(s == null ? '' : s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

/** Flatten rows into tree order with depth (mb_parent_fid). */
function orderStoreTree(arrInfo) {
    if (!arrInfo || !arrInfo.length) return [];
    var map = {};
    var byParent = {};
    var i, r, fid, pf;
    for (i = 0; i < arrInfo.length; i++) {
        r = arrInfo[i];
        fid = parseInt(r.mb_fid, 10) || 0;
        pf = parseInt(r.mb_parent_fid, 10) || 0;
        map[fid] = r;
        if (!byParent[pf]) byParent[pf] = [];
        byParent[pf].push(r);
    }
    Object.keys(byParent).forEach(function (k) {
        byParent[k].sort(function (a, b) {
            return String(a.mb_uid).localeCompare(String(b.mb_uid));
        });
    });

    var out = [];
    var visited = {};
    function walk(parentFid, depth) {
        var list = byParent[parentFid] || [];
        for (var n = 0; n < list.length; n++) {
            var child = list[n];
            var cf = parseInt(child.mb_fid, 10) || 0;
            if (visited[cf]) continue;
            visited[cf] = true;
            out.push({ row: child, depth: depth });
            walk(cf, depth + 1);
        }
    }
    walk(0, 0);
    for (i = 0; i < arrInfo.length; i++) {
        r = arrInfo[i];
        fid = parseInt(r.mb_fid, 10) || 0;
        if (visited[fid]) continue;
        pf = parseInt(r.mb_parent_fid, 10) || 0;
        if (pf > 0 && !map[pf]) {
            visited[fid] = true;
            out.push({ row: r, depth: 0 });
            walk(fid, 1);
        }
    }
    // Any leftover (cycle safety)
    for (i = 0; i < arrInfo.length; i++) {
        r = arrInfo[i];
        fid = parseInt(r.mb_fid, 10) || 0;
        if (!visited[fid]) {
            visited[fid] = true;
            out.push({ row: r, depth: 0 });
        }
    }
    return out;
}

function uidTreeHtml(uid, depth) {
    depth = parseInt(depth, 10) || 0;
    if (depth <= 0) return escHtml(uid);
    var pad = depth * 14;
    return '<span class="store-tree-uid" style="padding-left:' + pad + 'px;">'
        + '<span class="store-tree-branch">└ </span>' + escHtml(uid) + '</span>';
}

function showPage(arrInfo) {
    var tHtml = '';
    var ordered = orderStoreTree(arrInfo);
    for (var i = 0; i < ordered.length; i++) {
        var r = ordered[i].row;
        var depth = ordered[i].depth;
        var bet = parseInt(r.bet_sum, 10) || 0;
        var win = parseInt(r.bet_win_sum, 10) || 0;
        var empl = parseFloat(r.bet_empl_sum) || 0;
        var agen = parseFloat(r.bet_agen_sum) || 0;
        var profit = bet - win - empl - agen;
        var eggIn = parseInt(r.charge_present_sum, 10) || 0;
        var eggOut = parseInt(r.exchange_present_sum, 10) || 0;

        tHtml += '<tr id="trFid-' + r.mb_fid + '"' + (depth > 0 ? ' class="store-tree-child"' : '') + '>';
        tHtml += '<td class="tdDate">' + (i + 1) + '</td>';
        tHtml += '<td class="tdDate">' + getMemberLevelText(r.mb_level) + ' / ' + getMemberChannelText(r.mb_channel) + '</td>';
        tHtml += '<td class="tdDate">' + uidTreeHtml(r.mb_uid, depth) + '</td>';
        tHtml += '<td class="tdDate">' + escHtml(r.mb_nickname) + '</td>';
        tHtml += '<td class="tdDate">' + displayPwd(r.mb_pwd) + '</td>';
        tHtml += '<td class="tdMoney">' + (parseInt(r.mb_money, 10) || 0).toLocaleString() + '</td>';
        tHtml += '<td class="tdMoney">' + fmtPoint(r.mb_point) + '</td>';
        tHtml += '<td class="tdMoney">' + bet.toLocaleString() + '</td>';
        tHtml += '<td class="tdMoney">';
        if (profit >= 0) tHtml += '<font color="#0000fe">';
        else tHtml += '<font color="#fe0000">';
        tHtml += fmtPoint(profit) + '</font></td>';
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
        tHtml += '<button type="button" class="btn-store-action btn-store-blue btn_icon" title="' + t('btn_edit', '수정') + '" aria-label="' + t('btn_edit', '수정') + '" onclick="openStoreEdit('
            + r.mb_fid + ')"><i class="fas fa-pencil-alt"></i></button> ';
        tHtml += '<button type="button" class="btn-store-action btn-store-red btn_icon" title="' + t('btn_delete', '삭제') + '" aria-label="' + t('btn_delete', '삭제') + '" onclick="reqDeleteMember('
            + r.mb_fid + ', \'' + escAttr(r.mb_uid) + '\');"><i class="fas fa-trash-alt"></i></button>';
        tHtml += '</td>';
        tHtml += '<td class="tdDate">' + (r.mb_time_join || '') + '</td>';
        tHtml += '</tr>';
    }
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

function reqDeleteMember(mbFid, mbUid) {
    var msg = t('msg_delete_store', '이 매장을 삭제할까요? 하부매장이 있으면 함께 삭제됩니다.');
    if (mbUid) msg = '[' + mbUid + '] ' + msg;
    if (!confirm(msg)) return;

    $.ajax({
        url: '/api/member_delete',
        data: { json_: JSON.stringify({ mb_fid: mbFid }) },
        type: 'post',
        dataType: 'json',
        success: function (jResult) {
            if (jResult.status === 'success') {
                reqPage();
                if (typeof reqAssets === 'function') reqAssets();
            } else if (jResult.status === 'logout') {
                location.reload();
            } else {
                alert(t('btn_delete', '삭제') + ' fail');
            }
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
