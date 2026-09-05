$(document).ready(function () {
    reqPage();
});

function reqSearch() {
    reqPage();
}

function esc(s) {
    return String(s == null ? '' : s)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

function reqPage() {
    var objData = {
        start: $('#inputDateS').val(),
        end: $('#inputDateE').val(),
        page: 1,
        cntper: 500
    };
    $.ajax({
        url: '/api/cancel_list_page',
        data: { json_: JSON.stringify(objData) },
        type: 'post',
        dataType: 'json',
        success: function (jResult) {
            if (jResult.status === 'success') {
                showPage(jResult.data);
            } else if (jResult.status === 'logout') {
                location.reload();
            }
        }
    });
}

function showPage(arrInfo) {
    var tHtml = '';
    if (arrInfo != null) {
        var no = 1;
        for (var idx in arrInfo) {
            var r = arrInfo[idx];
            var betHtml = (typeof getBetTypeHtml === 'function')
                ? getBetTypeHtml(r.bet_mode)
                : esc(r.bet_mode);
            tHtml += '<tr>';
            tHtml += '<td class="tdDate">' + (no++) + '</td>';
            tHtml += '<td class="tdDate">' + esc(r.bet_round_no || r.bet_round_fid || '') + '</td>';
            tHtml += '<td class="tdDate">' + esc(r.mb_uid) + '</td>';
            tHtml += '<td class="tdDate">' + esc(r.mb_nickname) + '</td>';
            tHtml += '<td class="tdDate">' + betHtml + '</td>';
            tHtml += '<td class="tdMoney">' + (parseInt(r.bet_money, 10) || 0).toLocaleString() + '</td>';
            tHtml += '<td class="tdDate">' + esc(r.bet_time) + '</td>';
            tHtml += '<td class="tdDate">' + esc(r.bet_cancel_time || '') + '</td>';
            tHtml += '</tr>';
        }
    }
    if (!tHtml) {
        tHtml = '<tr><td colspan="8">-</td></tr>';
    }
    $('#tbodyList').html(tHtml);
}
