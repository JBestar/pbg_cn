$(document).ready(function() {

    reqCount();

});

function showPage(arrInfo, gameId) {
    let tHtml = "";
    let profit = 0;
    
    if(gameId == GAME_BOGLE_BALL){
        tHtml = "<th>게임회차</th><th>추첨번호</th><th>숫자합계</th><th>결과</th><th>마감시간</th><th>배팅수</th><th>배팅금액</th><th>당첨금액</th><th>포인트</th><th>정산</th>";
    } else {
        tHtml = "<th>게임회차</th><th>일회차</th><th>추첨번호</th><th>숫자합계</th><th>결과</th><th>마감시간</th><th>배팅수</th><th>배팅금액</th><th>당첨금액</th><th>포인트</th><th>정산</th>";
    }
    $("#theadRow").html(tHtml);
    tHtml = "";
    if (arrInfo != null) {
        for (let idx in arrInfo) {
            tHtml += "<tr>";
            if (gameId == GAME_COIN5_BALL) {
                tHtml += "<td class=\"tdDate\" style=\"height:40px;\">" + (arrInfo[idx].round_hash != null ? arrInfo[idx].round_hash : "") + "</td>";
            } else if (gameId == GAME_POWER_BALL){
                tHtml += "<td class=\"tdDate\" style=\"height:40px;\">" + arrInfo[idx].round_fid + "</td>";
            }
            tHtml += "<td class=\"tdDate\" >" + arrInfo[idx].round_num + "</td>";
            if(arrInfo[idx].round_state == 1){
                tHtml += "<td class=\"tdDate\">" + getSplitString(arrInfo[idx].round_normal) + "&nbsp;&nbsp;&nbsp;";
                tHtml += parseInt(arrInfo[idx].round_power) + "</td>";
                tHtml += "<td class=\"tdDate\">" + getSplitSum(arrInfo[idx].round_normal) + "</td>";
            } else tHtml += "<td class=\"tdDate\"></td><td class=\"tdDate\"></td>";
            tHtml += "<td class=\"tdDate\">";
            tHtml += getRoundResultHtml(arrInfo[idx].round_result_1, 1);
            tHtml += getRoundResultHtml(arrInfo[idx].round_result_2, 2);
            tHtml += getRoundResultHtml(arrInfo[idx].round_result_3, 3);
            tHtml += getRoundResultHtml(arrInfo[idx].round_result_4, 4);
            tHtml += getRoundResultHtml(arrInfo[idx].round_result_5, 5);
            tHtml += "</td>";
            if(arrInfo[idx].round_time.length >= 19)
                tHtml += "<td class=\"tdDate\">" + arrInfo[idx].round_time.substr(0, 17) + "00</td>";
            else tHtml += "<td class=\"tdDate\">" + arrInfo[idx].round_time + "</td>";
            if (arrInfo[idx].bet_round_fid != null) {
                tHtml += "<td class=\"tdDate\">" + arrInfo[idx].bet_count + "</td>";
                tHtml += "<td class=\"tdMoney\">" + parseInt(arrInfo[idx].bet_sum).toLocaleString() + "</td>";
                tHtml += "<td class=\"tdMoney\">" + parseInt(arrInfo[idx].win_sum).toLocaleString() + "</td>";
                tHtml += "<td class=\"tdMoney\">" + (parseInt(arrInfo[idx].empl_sum) + parseInt(arrInfo[idx].agen_sum)).toLocaleString() + "</td>";
                profit = parseInt(arrInfo[idx].bet_sum) - parseInt(arrInfo[idx].win_sum) - parseInt(arrInfo[idx].empl_sum) - parseInt(arrInfo[idx].agen_sum);
                tHtml += "<td class=\"tdMoney\">";
                if (profit >= 0) {
                    tHtml += "<font color=\"#0000fe\">";
                } else {
                    tHtml += "<font color=\"#fe0000\">";
                }
                tHtml += profit.toLocaleString() + "</font></td>";

            } else {
                tHtml += "<td class=\"tdDate\">0</td> <td class=\"tdMoney\">0</td> <td class=\"tdMoney\">0</td>";
                tHtml += "<td class=\"tdMoney\">0</td> <td class=\"tdMoney\"><font color=\"#0000fe\">0</font></td>";
            }



            tHtml += "</tr>";

        }
    }
    $('#tbodyList').html(tHtml);
}

function changeGame() {
    $('#inputGameNo').val('');
    var roundLabel = (window.ADMIN_I18N && window.ADMIN_I18N.label_game_round) ? window.ADMIN_I18N.label_game_round : '게임회차';
    $("#spanGameRound").text(roundLabel);
    reqCount();
}

function reqSearch() {
    reqCount();
}



function reqCount() {

    var objData = {
        "game": $('#selectGameType').val(),
        "start": $('#inputDateS').val(),
        "end": $('#inputDateE').val(),
        "round_id": $('#inputGameNo').val()
    };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/pbround_count',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            console.log(jResult);

            if (jResult.status == "success") {
                TotalCount = jResult.data;
                setFirstPage();
                reqPage();
            } else if (jResult.status == "logout") {
                location.reload();
            }
        },
        error: function(request, status, error) {
            // console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }

    });

}

function reqPage() {
    var objData = {
        "game": $('#selectGameType').val(),
        "start": $('#inputDateS').val(),
        "end": $('#inputDateE').val(),
        "round_id": $('#inputGameNo').val(),
        "page": getActivePage(),
        "cntper": CountPerPage
    };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/pbround_page',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            console.log(jResult);

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