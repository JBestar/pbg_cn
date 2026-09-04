$(document).ready(function () {
    reqDetailSearch();
});

function reqDetailSearch() {
    var objData = {
        game: 0,
        start: $('#inputDateS').val(),
        end: $('#inputDateE').val(),
        mb_uid: window.DETAIL_UID || '',
        page: 1,
        cntper: 500
    };
    $.ajax({
        url: '/api/pbbetlist_page',
        data: { json_: JSON.stringify(objData) },
        type: 'post',
        dataType: 'json',
        success: function (jResult) {
            if (jResult.status === 'success') {
                showDetail(jResult.data);
            } else if (jResult.status === 'logout') {
                window.close();
            }
        }
    });
}

function showDetail(arrInfo) {
    var tHtml = '';
    if (arrInfo != null) {
        for (var idx in arrInfo) {
            var r = arrInfo[idx];
            var afterMoney = (parseInt(r.bet_after_money, 10) || 0);
            if (!afterMoney && r.bet_before_money != null) {
                afterMoney = (parseInt(r.bet_before_money, 10) || 0) - (parseInt(r.bet_money, 10) || 0);
            }
            tHtml += '<tr>';
            tHtml += '<td>' + (r.bet_round_no || r.bet_round_fid || '') + '</td>';
            tHtml += '<td>' + esc(r.mb_uid) + '</td>';
            tHtml += '<td>' + esc(r.mb_nickname) + '</td>';
            tHtml += '<td>' + getBetTypeHtml(r.bet_mode) + '</td>';
            tHtml += '<td>' + (parseInt(r.bet_money, 10) || 0).toLocaleString() + '</td>';
            tHtml += '<td>' + afterMoney.toLocaleString() + '</td>';
            tHtml += '<td>' + (parseInt(r.bet_win_money, 10) || 0).toLocaleString() + '</td>';
            tHtml += '<td>' + (parseInt(r.mb_point, 10) || 0).toLocaleString() + '</td>';
            tHtml += '<td>' + esc(r.bet_time) + '</td>';
            tHtml += '</tr>';
        }
    }
    if (!tHtml) {
        tHtml = '<tr><td colspan="9">-</td></tr>';
    }
    $('#tbodyDetail').html(tHtml);
}

function esc(s) {
    return String(s == null ? '' : s)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}
