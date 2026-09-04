$(document).ready(function() {
    reqCount();
});

function showPage(arrInfo, gameId) {
    let tHtml = "";
    let profit = 0;

    tHtml = "<th>게임회차</th><th>일회차</th><th>추첨번호</th><th>숫자합계</th><th>시간</th><th>배팅수</th><th>배팅금액</th><th>당첨금액</th><th>포인트</th><th>정산</th>";
    $("#theadRow").html(tHtml);
    tHtml = "";
    if (arrInfo != null) {
        for (let idx in arrInfo) {
            var row = arrInfo[idx];
            tHtml += "<tr>";
            tHtml += "<td class=\"tdDate\" style=\"height:40px;\">" + row.round_fid + "</td>";
            tHtml += "<td class=\"tdDate\">" + row.round_num + "</td>";
            if (row.round_state == 1 && row.round_normal) {
                tHtml += "<td class=\"tdDate\">" + getSplitString(row.round_normal) + "&nbsp;&nbsp;&nbsp;";
                tHtml += parseInt(row.round_power) + "</td>";
                tHtml += "<td class=\"tdDate\">" + getSplitSum(row.round_normal) + "</td>";
            } else {
                tHtml += "<td class=\"tdDate\"></td><td class=\"tdDate\"></td>";
            }
            if (row.round_time && String(row.round_time).length >= 19) {
                tHtml += "<td class=\"tdDate\">" + String(row.round_time).substr(0, 19) + "</td>";
            } else {
                tHtml += "<td class=\"tdDate\">" + (row.round_time || "") + "</td>";
            }
            var betSum = parseInt(row.bet_sum) || 0;
            var winSum = parseInt(row.win_sum) || 0;
            var pointSum = (parseInt(row.empl_sum) || 0) + (parseInt(row.agen_sum) || 0);
            tHtml += "<td class=\"tdDate\">" + (parseInt(row.bet_count) || 0) + "</td>";
            tHtml += "<td class=\"tdMoney\">" + betSum.toLocaleString() + "</td>";
            tHtml += "<td class=\"tdMoney\">" + winSum.toLocaleString() + "</td>";
            tHtml += "<td class=\"tdMoney\">" + pointSum.toLocaleString() + "</td>";
            // 정산 = 배팅금액 - 당첨금액 - 포인트
            profit = betSum - winSum - pointSum;
            tHtml += "<td class=\"tdMoney\">";
            if (profit >= 0) {
                tHtml += "<font color=\"#0000fe\">";
            } else {
                tHtml += "<font color=\"#fe0000\">";
            }
            tHtml += profit.toLocaleString() + "</font></td>";
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
            if (jResult.status == "success") {
                TotalCount = jResult.data;
                setFirstPage();
                reqPage();
            } else if (jResult.status == "logout") {
                location.reload();
            }
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
            if (jResult.status == "success") {
                showPage(jResult.data, jResult.game);
            } else if (jResult.status == "logout") {
                location.reload();
            }
        }
    });
}
