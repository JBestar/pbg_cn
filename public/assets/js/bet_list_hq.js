/**
 * 본사 배팅내역: 총판별 → 매장별 → 건별(새 창)
 * 총판용 bet_list.js 와 분리
 */
var hqMode = 'agency'; // agency | store
var hqAgencyUid = '';

$(document).ready(function() {
    showAgencyMode();
    reqPage();
});

function i18n(k, fallback) {
    return (window.ADMIN_I18N && window.ADMIN_I18N[k]) ? window.ADMIN_I18N[k] : fallback;
}

function esc(s) {
    return String(s == null ? '' : s)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

function escAttr(s) {
    return String(s == null ? '' : s)
        .replace(/\\/g, '\\\\').replace(/'/g, "\\'");
}

function reqSearch() {
    reqPage();
}

function showAgencyMode() {
    hqMode = 'agency';
    hqAgencyUid = '';
    $('#spanBackWrap').hide();
    $('#spanBetModeLabel').text(i18n('label_agency_bets', '총판별 배팅내역'));
}

function showStoreMode(agencyUid) {
    hqMode = 'store';
    hqAgencyUid = agencyUid || '';
    $('#spanBackWrap').show();
    $('#spanBetModeLabel').text(i18n('label_store_bets', '매장별 배팅내역') + ' — ' + hqAgencyUid);
}

function backToAgencyList() {
    showAgencyMode();
    $('#inputUserID').val('');
    reqPage();
}

function openAgencyStores(uid) {
    showStoreMode(uid);
    $('#inputUserID').val('');
    reqPage();
}

function openBetDetail(uid) {
    var start = $('#inputDateS').val() || '';
    var end = $('#inputDateE').val() || '';
    var url = '/Main/bet_detail?uid=' + encodeURIComponent(uid)
        + '&start=' + encodeURIComponent(start)
        + '&end=' + encodeURIComponent(end);
    window.open(url, 'betDetail_' + uid, 'width=1100,height=720,scrollbars=yes,resizable=yes');
}

function showPage(arrInfo, roleLabel) {
    var tHtml = '';
    var detailLabel = i18n('th_detail_view', '상세보기');
    if (arrInfo != null) {
        for (var idx in arrInfo) {
            var r = arrInfo[idx];
            var bet = parseInt(r.bet_sum, 10) || 0;
            var win = parseInt(r.win_sum, 10) || 0;
            var diff = bet - win;
            var point = parseFloat(r.mb_point) || 0;
            var agenPoint = parseFloat(r.agen_point) || 0;
            tHtml += '<tr>';
            tHtml += '<td class="tdDate">' + esc(roleLabel) + '</td>';
            tHtml += '<td class="tdDate">' + esc(r.mb_uid) + '</td>';
            tHtml += '<td class="tdDate">' + esc(r.mb_nickname) + '</td>';
            tHtml += '<td class="tdMoney">' + (parseInt(r.mb_money, 10) || 0).toLocaleString() + '</td>';
            tHtml += '<td class="tdMoney">' + bet.toLocaleString() + '</td>';
            tHtml += '<td class="tdMoney">' + win.toLocaleString() + '</td>';
            tHtml += '<td class="tdMoney">';
            tHtml += diff >= 0 ? '<font color="#0000fe">' : '<font color="#fe0000">';
            tHtml += diff.toLocaleString() + '</font></td>';
            tHtml += '<td class="tdDate">' + (parseInt(r.win_rounds, 10) || 0) + '</td>';
            tHtml += '<td class="tdMoney">' + fmtPoint(point) + '</td>';
            tHtml += '<td class="tdMoney">' + fmtPoint(agenPoint) + '</td>';
            if (hqMode === 'agency') {
                tHtml += '<td class="tdDate"><button type="button" class="btn-detail-view" onclick="openAgencyStores(\''
                    + escAttr(r.mb_uid) + '\');">' + esc(detailLabel) + '</button></td>';
            } else {
                tHtml += '<td class="tdDate"><button type="button" class="btn-detail-view" onclick="openBetDetail(\''
                    + escAttr(r.mb_uid) + '\');">' + esc(detailLabel) + '</button></td>';
            }
            tHtml += '</tr>';
        }
    }
    $('#tbodyList').html(tHtml);
}

function reqPage() {
    var objData = {
        start: $('#inputDateS').val(),
        end: $('#inputDateE').val(),
        mb_uid: $('#inputUserID').val()
    };
    var url, roleLabel;
    if (hqMode === 'agency') {
        url = '/api/agency_bet_summary';
        roleLabel = i18n('role_agency', '총판');
    } else {
        url = '/api/store_bet_summary';
        objData.agency_uid = hqAgencyUid;
        roleLabel = i18n('role_store', '매장');
    }
    $.ajax({
        url: url,
        data: { json_: JSON.stringify(objData) },
        type: 'post',
        dataType: 'json',
        success: function(jResult) {
            if (jResult.status == 'success') {
                showPage(jResult.data, roleLabel);
            } else if (jResult.status == 'logout') {
                location.reload();
            }
        }
    });
}
