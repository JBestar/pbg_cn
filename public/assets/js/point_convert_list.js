$(document).ready(function () {
    reqCount();
});

function reqSearch() {
    reqCount();
}

function esc(s) {
    return String(s == null ? '' : s)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

function showPage(arrInfo) {
    var tHtml = '';
    var nPage = (typeof getActivePage === 'function') ? getActivePage() : 1;
    var per = (typeof CountPerPage !== 'undefined') ? CountPerPage : 20;
    var rowNo = (nPage - 1) * per;
    if (arrInfo != null) {
        for (var idx in arrInfo) {
            var r = arrInfo[idx];
            rowNo++;
            tHtml += '<tr>';
            tHtml += '<td class="tdDate">' + rowNo + '</td>';
            tHtml += '<td class="tdDate">' + esc(r.money_mb_uid) + '</td>';
            tHtml += '<td class="tdDate">' + esc(r.mb_nickname) + '</td>';
            tHtml += '<td class="tdMoney">' + (parseInt(r.money_amount, 10) || 0).toLocaleString() + '</td>';
            tHtml += '<td class="tdMoney">' + (parseInt(r.money_before, 10) || 0).toLocaleString() + '</td>';
            tHtml += '<td class="tdMoney">' + (parseInt(r.money_after, 10) || 0).toLocaleString() + '</td>';
            tHtml += '<td class="tdDate">' + esc(r.money_update_time) + '</td>';
            tHtml += '</tr>';
        }
    }
    if (!tHtml) {
        tHtml = '<tr><td colspan="7">-</td></tr>';
    }
    $('#tbodyList').html(tHtml);
}

function reqCount() {
    var objData = {
        start: $('#inputDateS').val(),
        end: $('#inputDateE').val(),
        mb_uid: $('#inputUserID').val()
    };
    $.ajax({
        url: '/api/point_convert_count',
        data: { json_: JSON.stringify(objData) },
        type: 'post',
        dataType: 'json',
        success: function (jResult) {
            if (jResult.status === 'success') {
                if (typeof TotalCount !== 'undefined') {
                    TotalCount = jResult.data;
                }
                if (typeof setFirstPage === 'function') {
                    setFirstPage();
                }
                reqPage();
            } else if (jResult.status === 'logout') {
                location.reload();
            }
        }
    });
}

function reqPage() {
    var page = (typeof getActivePage === 'function') ? getActivePage() : 1;
    var per = (typeof CountPerPage !== 'undefined') ? CountPerPage : 20;
    var objData = {
        start: $('#inputDateS').val(),
        end: $('#inputDateE').val(),
        mb_uid: $('#inputUserID').val(),
        page: page,
        cntper: per
    };
    $.ajax({
        url: '/api/point_convert_page',
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
