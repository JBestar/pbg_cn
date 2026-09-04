$(document).ready(function() {
    reqPage();
});

function reqSearch() {
    reqPage();
}

function showPage(arrInfo) {
    var tHtml = "";
    if (arrInfo != null) {
        for (var idx in arrInfo) {
            var r = arrInfo[idx];
            var bet = parseInt(r.bet_sum, 10) || 0;
            var win = parseInt(r.win_sum, 10) || 0;
            var diff = bet - win;
            var point = parseFloat(r.mb_point) || 0;
            var agenPoint = parseFloat(r.agen_point) || 0;
            tHtml += "<tr>";
            tHtml += "<td class=\"tdDate\">" + esc(r.mb_uid) + "</td>";
            tHtml += "<td class=\"tdDate\">" + esc(r.mb_nickname) + "</td>";
            tHtml += "<td class=\"tdMoney\">" + (parseInt(r.mb_money, 10) || 0).toLocaleString() + "</td>";
            tHtml += "<td class=\"tdMoney\">" + bet.toLocaleString() + "</td>";
            tHtml += "<td class=\"tdMoney\">" + win.toLocaleString() + "</td>";
            tHtml += "<td class=\"tdMoney\">";
            if (diff >= 0) tHtml += "<font color=\"#0000fe\">";
            else tHtml += "<font color=\"#fe0000\">";
            tHtml += diff.toLocaleString() + "</font></td>";
            tHtml += "<td class=\"tdDate\">" + (parseInt(r.win_rounds, 10) || 0) + "</td>";
            tHtml += "<td class=\"tdMoney\">" + Math.round(point).toLocaleString() + "</td>";
            tHtml += "<td class=\"tdMoney\">" + Math.round(agenPoint).toLocaleString() + "</td>";
            var detailLabel = (window.ADMIN_I18N && window.ADMIN_I18N.th_detail_view)
                ? window.ADMIN_I18N.th_detail_view : '상세보기';
            tHtml += "<td class=\"tdDate\"><button type=\"button\" class=\"btn-detail-view\" onclick=\"openBetDetail('"
                + escAttr(r.mb_uid) + "');\">" + esc(detailLabel) + "</button></td>";
            tHtml += "</tr>";
        }
    }
    $('#tbodyList').html(tHtml);
}

function esc(s) {
    return String(s == null ? '' : s)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

function escAttr(s) {
    return String(s == null ? '' : s)
        .replace(/\\/g, '\\\\').replace(/'/g, "\\'");
}

function openBetDetail(uid) {
    var start = $('#inputDateS').val() || '';
    var end = $('#inputDateE').val() || '';
    var url = '/Main/bet_detail?uid=' + encodeURIComponent(uid)
        + '&start=' + encodeURIComponent(start)
        + '&end=' + encodeURIComponent(end);
    window.open(url, 'betDetail_' + uid, 'width=1100,height=720,scrollbars=yes,resizable=yes');
}

function reqPage() {
    var objData = {
        "start": $('#inputDateS').val(),
        "end": $('#inputDateE').val(),
        "mb_uid": $('#inputUserID').val()
    };
    var jsonData = JSON.stringify(objData);
    $.ajax({
        url: '/api/store_bet_summary',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            if (jResult.status == "success") {
                showPage(jResult.data);
            } else if (jResult.status == "logout") {
                location.reload();
            }
        }
    });
}
