$(document).ready(function() {
    reqPage();
});

function reqSearch() {
    reqPage();
}

function isHq() {
    return (window.TERM_ADMIN_LEVEL || 0) > 8;
}

function totalLabel() {
    return (window.ADMIN_I18N && window.ADMIN_I18N.th_total) ? window.ADMIN_I18N.th_total : '합계';
}

function moneyCell(n, colorize) {
    var v = parseInt(n, 10) || 0;
    if (!colorize) {
        return '<td class="tdMoney">' + Math.abs(v).toLocaleString() + '</td>';
    }
    var html = '<td class="tdMoney">';
    if (v >= 0) html += '<font color="#0000fe">';
    else html += '<font color="#fe0000">';
    html += v.toLocaleString() + '</font></td>';
    return html;
}

function showPage(arrInfo) {
    var tHtml = '';
    var hq = isHq();
    var bet_sum = 0, win_sum = 0, point_sum = 0, bet_profit_sum = 0;
    var give_sum = 0, recovery_sum = 0, charge_sum = 0, exchange_sum = 0, ce_profit_sum = 0;

    if (arrInfo != null) {
        Object.keys(arrInfo).reverse().forEach(function(idx) {
            var row = arrInfo[idx];
            var bet = Math.abs(row.money_bet || 0);
            var win = Math.abs(row.money_win || 0);
            var point = Math.abs(row.point_empl || 0) + Math.abs(row.point_agen || 0);
            var bet_profit = bet - win - point;
            var give = Math.abs(row.money_give || 0);
            var recovery = Math.abs(row.money_recovery || 0);
            var charge = Math.abs(row.money_charge || 0);
            var exchange = Math.abs(row.money_exchange || 0);
            // 총판: 충환손익 = 알충전 - 알회수 / 본사: 충전 - 환전
            var ce_profit = hq ? (charge - exchange) : (give - recovery);

            tHtml += '<tr>';
            tHtml += '<td class="tdDate">' + (row.date || '') + '</td>';
            tHtml += '<td class="tdMoney">' + bet.toLocaleString() + '</td>';
            tHtml += '<td class="tdMoney">' + win.toLocaleString() + '</td>';
            tHtml += '<td class="tdMoney">' + point.toLocaleString() + '</td>';
            tHtml += moneyCell(bet_profit, true);
            tHtml += '<td class="tdMoney">' + give.toLocaleString() + '</td>';
            tHtml += '<td class="tdMoney">' + recovery.toLocaleString() + '</td>';
            if (hq) {
                tHtml += '<td class="tdMoney">' + charge.toLocaleString() + '</td>';
                tHtml += '<td class="tdMoney">' + exchange.toLocaleString() + '</td>';
            }
            tHtml += moneyCell(ce_profit, true);
            tHtml += '</tr>';

            bet_sum += bet;
            win_sum += win;
            point_sum += point;
            bet_profit_sum += bet_profit;
            give_sum += give;
            recovery_sum += recovery;
            charge_sum += charge;
            exchange_sum += exchange;
            ce_profit_sum += ce_profit;
        });

        tHtml += '<tr style="background:#f5f5f5;font-weight:bold;">';
        tHtml += '<td class="tdDate">' + totalLabel() + '</td>';
        tHtml += '<td class="tdMoney">' + bet_sum.toLocaleString() + '</td>';
        tHtml += '<td class="tdMoney">' + win_sum.toLocaleString() + '</td>';
        tHtml += '<td class="tdMoney">' + point_sum.toLocaleString() + '</td>';
        tHtml += moneyCell(bet_profit_sum, true);
        tHtml += '<td class="tdMoney">' + give_sum.toLocaleString() + '</td>';
        tHtml += '<td class="tdMoney">' + recovery_sum.toLocaleString() + '</td>';
        if (hq) {
            tHtml += '<td class="tdMoney">' + charge_sum.toLocaleString() + '</td>';
            tHtml += '<td class="tdMoney">' + exchange_sum.toLocaleString() + '</td>';
        }
        tHtml += moneyCell(ce_profit_sum, true);
        tHtml += '</tr>';
    }
    $('#tbodyList').html(tHtml);
}

function reqPage() {
    var objData = {
        start: $('#inputDateS').val(),
        end: $('#inputDateE').val(),
        mb_uid: $('#selectLevel').val()
    };
    $.ajax({
        url: '/api/pbacclist',
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
