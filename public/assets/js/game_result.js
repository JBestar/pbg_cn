$(document).ready(function() {
    reqCount();
});

function showPage(arrInfo) {
    var tHtml = '';
    if (arrInfo != null) {
        for (var idx in arrInfo) {
            var row = arrInfo[idx];
            tHtml += '<tr>';
            tHtml += '<td class="tdDate" style="height:40px;">' + row.round_fid + '</td>';
            tHtml += '<td class="tdDate">' + row.round_num + '</td>';
            if (row.round_state == 1 && row.round_normal) {
                tHtml += '<td class="tdDate">' + getSplitString(row.round_normal) + '&nbsp;&nbsp;&nbsp;';
                tHtml += parseInt(row.round_power, 10) + '</td>';
                tHtml += '<td class="tdDate">' + getSplitSum(row.round_normal) + '</td>';
            } else {
                tHtml += '<td class="tdDate"></td><td class="tdDate"></td>';
            }
            if (row.round_time && String(row.round_time).length >= 19) {
                tHtml += '<td class="tdDate">' + String(row.round_time).substr(0, 19) + '</td>';
            } else {
                tHtml += '<td class="tdDate">' + (row.round_time || '') + '</td>';
            }
            var betSum = parseInt(row.bet_sum, 10) || 0;
            var winSum = parseInt(row.win_sum, 10) || 0;
            var pointSum = (parseInt(row.empl_sum, 10) || 0) + (parseInt(row.agen_sum, 10) || 0);
            tHtml += '<td class="tdDate">' + (parseInt(row.bet_count, 10) || 0) + '</td>';
            tHtml += '<td class="tdMoney">' + betSum.toLocaleString() + '</td>';
            tHtml += '<td class="tdMoney">' + winSum.toLocaleString() + '</td>';
            tHtml += '<td class="tdMoney">' + pointSum.toLocaleString() + '</td>';
            var profit = betSum - winSum - pointSum;
            tHtml += '<td class="tdMoney">';
            if (profit >= 0) {
                tHtml += '<font color="#0000fe">';
            } else {
                tHtml += '<font color="#fe0000">';
            }
            tHtml += profit.toLocaleString() + '</font></td>';
            tHtml += '</tr>';
        }
    }
    $('#tbodyList').html(tHtml);
}

function reqSearch() {
    reqCount();
}

function buildFilter() {
    return {
        game: 0,
        start: $('#inputDateS').val(),
        end: $('#inputDateE').val(),
        round_id: $('#inputGameNo').val(),
        mb_uid: $('#selectLevel').val() || ''
    };
}

function reqCount() {
    var objData = buildFilter();
    $.ajax({
        url: '/api/pbround_count',
        data: { json_: JSON.stringify(objData) },
        type: 'post',
        dataType: 'json',
        success: function(jResult) {
            if (jResult.status == 'success') {
                TotalCount = jResult.data;
                setFirstPage();
                reqPage();
            } else if (jResult.status == 'logout') {
                location.reload();
            }
        }
    });
}

function reqPage() {
    var objData = buildFilter();
    objData.page = getActivePage();
    objData.cntper = CountPerPage;
    $.ajax({
        url: '/api/pbround_page',
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
