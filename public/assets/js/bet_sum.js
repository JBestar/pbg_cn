$(document).ready(function() {

    reqPage();

});

function showPage(arrInfo, gameId) {
    let tHtml = "";
    let profit = 0,
        totalCnt = 0,
        totalBet = 0,
        totalWin = 0,
        totalPoint = 0,
        totalProfit = 0;
    if (arrInfo != null) {
        for (let idx in arrInfo) {

            tHtml += "<tr>";
            tHtml += "<td class=\"tdDate\" style=\"height:40px;\">" + arrInfo[idx].bet_date + "</td>";
            if (parseInt(gameId) == 1) {
                tHtml += "<td class=\"tdDate\">" + (arrInfo[idx].round_hash != null ? arrInfo[idx].round_hash : "") + "</td>";
            } else {
                tHtml += "<td class=\"tdDate\">" + arrInfo[idx].bet_round_fid + "</td>";
            }
            tHtml += "<td class=\"tdDate\">" + arrInfo[idx].bet_round_no + "</td>";
            tHtml += "<td class=\"tdDate\">" + arrInfo[idx].bet_count + "</td>";
            tHtml += "<td class=\"tdMoney\">" + parseInt(arrInfo[idx].bet_sum).toLocaleString() + "</td>";
            tHtml += "<td class=\"tdMoney\">" + parseInt(arrInfo[idx].win_sum).toLocaleString() + "</td>";
            tHtml += "<td class=\"tdMoney\">" + parseInt(arrInfo[idx].point_sum).toLocaleString() + "</td>";
            profit = parseInt(arrInfo[idx].bet_sum) - parseInt(arrInfo[idx].win_sum) - parseInt(arrInfo[idx].point_sum);
            tHtml += "<td class=\"tdMoney\">";
            if (profit >= 0) {
                tHtml += "<font color=\"#0000fe\">";
            } else {
                tHtml += "<font color=\"#fe0000\">";
            }
            tHtml += profit.toLocaleString() + "</font></td>";

            tHtml += "</tr>";
            totalCnt += parseInt(arrInfo[idx].bet_count);
            totalBet += parseInt(arrInfo[idx].bet_sum);
            totalWin += parseInt(arrInfo[idx].win_sum);
            totalPoint += parseInt(arrInfo[idx].point_sum);
            totalProfit += profit;
        }

    }
    $("#thBetCount").html(totalCnt.toLocaleString());
    $("#thBetSum").html(totalBet.toLocaleString());
    $("#thWinSum").html(totalWin.toLocaleString());
    $("#thPointSum").html(totalPoint.toLocaleString());

    if (totalProfit >= 0)
        $("#thProfitSum").css("color", "#0000fe");
    else $("#thProfitSum").css("color", "#fe0000");
    $("#thProfitSum").html(totalProfit.toLocaleString());

    $('#tbodyList').html(tHtml);
}

function changeGame() {
    $('#inputGameNo').val('');
    var roundLabel = (window.ADMIN_I18N && window.ADMIN_I18N.label_game_round) ? window.ADMIN_I18N.label_game_round : '게임회차';
    $("#spanGameRound").text(roundLabel);
    reqPage();
}

function reqSearch() {
    reqPage();
}

function reqLoopRound() {

    reqPage();
}

function reqPage() {
    var objData = {
        "game": $('#selectGameType').val(),
        "start": $('#inputDateS').val(),
        "end": $('#inputDateE').val(),
        "round_id": $('#inputGameNo').val()
    };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/pbacc_round',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);

            if (jResult.status == "success") {
                showPage(jResult.data, jResult.game);
            } else if (jResult.status == "logout") {
                location.reload();
            }
        },
        error: function(request, status, error) {
            // console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }

    });
}