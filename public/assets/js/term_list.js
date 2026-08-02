$(document).ready(function() {
    reqPage();

});

function reqSearch() {
    reqPage();
}


function showPage(arrInfo) {
    let tHtml = "";
    if (arrInfo != null) {
        let bet_profit = 0,
            charge_profit = 0,
            bet_sum = 0,
            win_sum = 0,
            point_sum = 0,
            bet_profit_sum = 0,
            give_sum = 0,
            recovery_sum = 0,
            charge_sum = 0,
            exchange_sum = 0,
            charge_profit_sum = 0;

        Object.keys(arrInfo).reverse()
            .forEach(function(idx) {

                tHtml += "<tr>";
                tHtml += "<td class=\"tdDate\">" + arrInfo[idx].date + "</td>";
                tHtml += "<td class=\"tdMoney\">" + Math.abs(arrInfo[idx].money_bet).toLocaleString() + "</td>";
                tHtml += "<td class=\"tdMoney\">" + Math.abs(arrInfo[idx].money_win).toLocaleString() + "</td>";
                tHtml += "<td class=\"tdMoney\">" + Math.abs(arrInfo[idx].point_empl).toLocaleString() + "</td>";
                bet_profit = Math.abs(arrInfo[idx].money_bet) - Math.abs(arrInfo[idx].money_win) - Math.abs(arrInfo[idx].point_agen) - Math.abs(arrInfo[idx].point_empl);
                tHtml += "<td class=\"tdMoney\">";
                if (bet_profit >= 0) {
                    tHtml += "<font color=\"#0000fe\">";
                } else {
                    tHtml += "<font color=\"#fe0000\">";
                }
                tHtml += bet_profit.toLocaleString() + "</font></td>";
                tHtml += "<td class=\"tdMoney\">" + Math.abs(arrInfo[idx].money_give).toLocaleString() + "</td>";
                tHtml += "<td class=\"tdMoney\">" + Math.abs(arrInfo[idx].money_recovery).toLocaleString() + "</td>";
                tHtml += "<td class=\"tdMoney\">" + Math.abs(arrInfo[idx].money_charge).toLocaleString() + "</td>";
                tHtml += "<td class=\"tdMoney\">" + Math.abs(arrInfo[idx].money_exchange).toLocaleString() + "</td>";
                charge_profit = Math.abs(arrInfo[idx].money_charge) - Math.abs(arrInfo[idx].money_exchange);
                tHtml += "<td class=\"tdMoney\">";
                if (charge_profit >= 0) {
                    tHtml += "<font color=\"#0000fe\">";
                } else {
                    tHtml += "<font color=\"#fe0000\">";
                }
                tHtml += charge_profit.toLocaleString() + "</font></td>";
                tHtml += "</tr>";

                bet_sum += Math.abs(arrInfo[idx].money_bet);
                win_sum += Math.abs(arrInfo[idx].money_win);
                point_sum += Math.abs(arrInfo[idx].point_empl);
                bet_profit_sum += bet_profit;
                give_sum += Math.abs(arrInfo[idx].money_give);
                recovery_sum += Math.abs(arrInfo[idx].money_recovery);
                charge_sum += Math.abs(arrInfo[idx].money_charge);
                exchange_sum += Math.abs(arrInfo[idx].money_exchange);
                charge_profit_sum += charge_profit;
            });


        tHtml += "<tr>";
        tHtml += "<td class=\"tdDate\">합계</td>";
        tHtml += "<td class=\"tdMoney\">" + bet_sum.toLocaleString() + "</td>";
        tHtml += "<td class=\"tdMoney\">" + win_sum.toLocaleString() + "</td>";
        tHtml += "<td class=\"tdMoney\">" + point_sum.toLocaleString() + "</td>";
        tHtml += "<td class=\"tdMoney\">";
        if (bet_profit_sum >= 0) {
            tHtml += "<font color=\"#0000fe\">";
        } else {
            tHtml += "<font color=\"#fe0000\">";
        }
        tHtml += bet_profit_sum.toLocaleString() + "</font></td>";
        tHtml += "<td class=\"tdMoney\">" + give_sum.toLocaleString() + "</td>";
        tHtml += "<td class=\"tdMoney\">" + recovery_sum.toLocaleString() + "</td>";
        tHtml += "<td class=\"tdMoney\">" + charge_sum.toLocaleString() + "</td>";
        tHtml += "<td class=\"tdMoney\">" + exchange_sum.toLocaleString() + "</td>";
        tHtml += "<td class=\"tdMoney\">";
        if (charge_profit_sum >= 0) {
            tHtml += "<font color=\"#0000fe\">";
        } else {
            tHtml += "<font color=\"#fe0000\">";
        }
        tHtml += charge_profit_sum.toLocaleString() + "</font></td>";
        tHtml += "</tr>";


    }
    $('#tbodyList').html(tHtml);
}

function reqPage() {
    var objData = {
        "start": $('#inputDateS').val(),
        "end": $('#inputDateE').val(),
        "mb_uid": $('#selectLevel').val(),

    };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/pbacclist',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);

            if (jResult.status == "success") {
                showPage(jResult.data);
            } else if (jResult.status == "logout") {
                location.reload();
            }
        },
        error: function(request, status, error) {
            // console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }

    });

}