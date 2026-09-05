$(document).ready(function() {
    reqDetailSearch();
});

function esc(s) {
    return String(s == null ? '' : s)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

function fmtMoney(n) {
    return (parseInt(n, 10) || 0).toLocaleString();
}

function reqDetailSearch() {
    var objData = {
        mb_uid: window.DETAIL_UID || '',
        start: $('#inputDateS').val(),
        end: $('#inputDateE').val(),
        scope: window.CE_SCOPE || 'store'
    };
    $.ajax({
        url: '/api/ce_detail',
        data: { json_: JSON.stringify(objData) },
        type: 'post',
        dataType: 'json',
        success: function(jResult) {
            if (jResult.status == 'success') {
                showDetail(jResult.data);
            } else if (jResult.status == 'logout') {
                window.close();
            }
        }
    });
}

function showDetail(arrInfo) {
    var tHtml = '';
    if (arrInfo != null) {
        var no = 1;
        for (var idx in arrInfo) {
            var r = arrInfo[idx];
            tHtml += '<tr>';
            tHtml += '<td>' + (no++) + '</td>';
            tHtml += '<td>' + esc(r.mb_uid) + '</td>';
            tHtml += '<td>' + esc(r.mb_nickname) + '</td>';
            tHtml += '<td class="tdMoney">' + fmtMoney(r.money_before) + '</td>';
            tHtml += '<td class="tdMoney">' + fmtMoney(r.charge_amount) + '</td>';
            tHtml += '<td class="tdMoney">' + fmtMoney(r.exchange_amount) + '</td>';
            tHtml += '<td class="tdMoney">' + fmtMoney(r.point_amount) + '</td>';
            tHtml += '<td class="tdMoney">' + fmtMoney(r.money_after) + '</td>';
            tHtml += '<td>' + esc(r.money_update_time) + '</td>';
            tHtml += '</tr>';
        }
    }
    $('#tbodyDetail').html(tHtml);
}
