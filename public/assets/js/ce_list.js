$(document).ready(function() {
    reqPage();
});

function reqSearch() {
    reqPage();
}

function esc(s) {
    return String(s == null ? '' : s)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

function escAttr(s) {
    return String(s == null ? '' : s)
        .replace(/\\/g, '\\\\').replace(/'/g, "\\'");
}

function fmtMoney(n) {
    return (parseInt(n, 10) || 0).toLocaleString();
}

function levelText(lv) {
    if (typeof getMemberLevelText === 'function') {
        return getMemberLevelText(lv);
    }
    return (window.ADMIN_I18N && window.ADMIN_I18N.role_agency) ? window.ADMIN_I18N.role_agency : '총판';
}

function showPage(arrInfo) {
    var tHtml = '';
    var sumCharge = 0, sumEx = 0, sumPt = 0, sumDiff = 0;
    var detailLabel = (window.ADMIN_I18N && window.ADMIN_I18N.th_detail_view)
        ? window.ADMIN_I18N.th_detail_view : '상세보기';
    var totalLabel = (window.ADMIN_I18N && window.ADMIN_I18N.th_total)
        ? window.ADMIN_I18N.th_total : '합계';
    var showGrade = !!window.CE_SHOW_GRADE;
    var showAgency = !!window.CE_SHOW_AGENCY;
    var colSpanId = 2;
    if (showGrade) colSpanId += 1;
    if (showAgency) colSpanId += 1;

    if (arrInfo != null) {
        for (var idx in arrInfo) {
            var r = arrInfo[idx];
            var charge = parseInt(r.charge_sum, 10) || 0;
            var exchange = parseInt(r.exchange_sum, 10) || 0;
            var point = parseFloat(r.point_sum) || 0;
            var diff = parseFloat(r.diff_sum);
            if (isNaN(diff)) diff = charge - exchange - point;
            sumCharge += charge;
            sumEx += exchange;
            sumPt += point;
            sumDiff += diff;

            var diffCls = diff >= 0 ? 'td-diff-pos' : 'td-diff-neg';
            tHtml += '<tr>';
            if (showGrade) {
                tHtml += '<td class="tdDate">' + esc(levelText(r.mb_level)) + '</td>';
            }
            if (showAgency) {
                tHtml += '<td class="tdDate">' + esc(r.agency_uid || '') + '</td>';
            }
            tHtml += '<td class="tdDate">' + esc(r.mb_uid) + '</td>';
            tHtml += '<td class="tdDate">' + esc(r.mb_nickname) + '</td>';
            tHtml += '<td class="tdMoney">' + fmtMoney(charge) + '</td>';
            tHtml += '<td class="tdMoney">' + fmtMoney(exchange) + '</td>';
            tHtml += '<td class="tdMoney">' + fmtPoint(point) + '</td>';
            tHtml += '<td class="tdMoney ' + diffCls + '">' + fmtPoint(diff) + '</td>';
            tHtml += '<td class="tdDate"><button type="button" class="btn-detail-view" onclick="openCeDetail(\''
                + escAttr(r.mb_uid) + '\');">' + esc(detailLabel) + '</button></td>';
            tHtml += '</tr>';
        }
    }

    var sumDiffCls = sumDiff >= 0 ? 'td-diff-pos' : 'td-diff-neg';
    tHtml += '<tr style="background:#f5f5f5;font-weight:bold;">';
    tHtml += '<td class="tdDate" colspan="' + colSpanId + '">' + esc(totalLabel) + '</td>';
    tHtml += '<td class="tdMoney">' + fmtMoney(sumCharge) + '</td>';
    tHtml += '<td class="tdMoney">' + fmtMoney(sumEx) + '</td>';
    tHtml += '<td class="tdMoney">' + fmtPoint(sumPt) + '</td>';
    tHtml += '<td class="tdMoney ' + sumDiffCls + '">' + fmtPoint(sumDiff) + '</td>';
    tHtml += '<td class="tdDate"></td>';
    tHtml += '</tr>';

    $('#tbodyList').html(tHtml);
}

function openCeDetail(uid) {
    var start = $('#inputDateS').val() || '';
    var end = $('#inputDateE').val() || '';
    var path = window.CE_DETAIL_PATH || '/Main/store_ce_detail';
    var url = path + '?uid=' + encodeURIComponent(uid)
        + '&start=' + encodeURIComponent(start)
        + '&end=' + encodeURIComponent(end);
    window.open(url, 'ceDetail_' + uid, 'width=1100,height=720,scrollbars=yes,resizable=yes');
}

function reqPage() {
    var objData = {
        start: $('#inputDateS').val(),
        end: $('#inputDateE').val(),
        mb_uid: $('#inputUserID').val()
    };
    var api = window.CE_API || '/api/store_ce_summary';
    $.ajax({
        url: api,
        data: { json_: JSON.stringify(objData) },
        type: 'post',
        dataType: 'json',
        success: function(jResult) {
            if (jResult.status == 'success') {
                showPage(jResult.data);
            } else if (jResult.status == 'logout') {
                location.reload();
            }
        }
    });
}
