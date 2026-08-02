$(document).ready(function() {

    reqCount();
    // Realtime: refresh bet list every 5s while this page is open
    setInterval(function () {
        reqCount();
    }, 5000);
});

function reqLoopRound() {
    reqCount();
}

function showPage(arrInfo, lv) {
    let tHtml = "";

    if (arrInfo != null) {
        let lastRoundId = 0;
        let trStyle1 = "<tr style=\"background-color:#f7f4ac;\" ondblclick='showEditBet(this);'";
        let trStyle2 = "<tr style=\"background-color:#dddddd;\" ondblclick='showEditBet(this);'";
        let trStyleNo = 0;
        for (let idx in arrInfo) {
            if (lastRoundId != parseInt(arrInfo[idx].bet_round_fid)) {
                lastRoundId = parseInt(arrInfo[idx].bet_round_fid);
                trStyleNo = trStyleNo == 1 ? 2 : 1;
                tHtml += trStyleNo == 1 ? trStyle1 : trStyle2;
            } else tHtml += trStyleNo == 1 ? trStyle1 : trStyle2;
            if (arrInfo[idx].bet_state == 0 && lv > 9) {
                tHtml += " data-game='" + arrInfo[idx].bet_game + "' data-id='" + arrInfo[idx].bet_fid;
                tHtml += "' data-mode='" + arrInfo[idx].bet_mode;
                tHtml += "' data-uid='" + arrInfo[idx].mb_uid + "' data-name='" + arrInfo[idx].mb_nickname + "'";
            }

            tHtml += "><td class=\"tdDate\" style=\"height:40px;\">";
            tHtml += getGameName(arrInfo[idx].bet_game);
            // tHtml += "</td><td class=\"tdDate\">";
            if (parseInt(arrInfo[idx].bet_game) == GAME_COIN5_BALL) {
                // tHtml += arrInfo[idx].coin_round_hash != null ? arrInfo[idx].coin_round_hash : "";
                tHtml += "</td><td class=\"tdDate\" >" + arrInfo[idx].bet_round_no + "</td>";
                if (arrInfo[idx].coin_round_fid != null) {
                    tHtml += "<td class=\"tdDate\">" + getSplitString(arrInfo[idx].coin_round_normal) + "&nbsp;&nbsp;&nbsp;";
                    tHtml += "<font color=\"#0a601c\">" + parseInt(arrInfo[idx].coin_round_power) + "</font></td>";
                    tHtml += "<td class=\"tdDate\">" + getSplitSum(arrInfo[idx].coin_round_normal) + "</td>";
                    tHtml += "<td class=\"tdDate\">";
                    tHtml += getRoundResultHtml(arrInfo[idx].coin_round_result_1, 1);
                    tHtml += getRoundResultHtml(arrInfo[idx].coin_round_result_2, 2);
                    tHtml += getRoundResultHtml(arrInfo[idx].coin_round_result_3, 3);
                    tHtml += getRoundResultHtml(arrInfo[idx].coin_round_result_4, 4);
                    tHtml += getRoundResultHtml(arrInfo[idx].coin_round_result_5, 5);
                    tHtml += "</td>";
                } else {
                    tHtml += "<td class=\"tdDate\"></td>";
                    tHtml += "<td class=\"tdDate\"></td>";
                    tHtml += "<td class=\"tdDate\"></td>";
                }
            } else if (parseInt(arrInfo[idx].bet_game) == GAME_POWER_BALL){
                // tHtml += arrInfo[idx].bet_round_fid;
                tHtml += "</td><td class=\"tdDate\" >" + arrInfo[idx].bet_round_no + "</td>";
                if (arrInfo[idx].round_fid != null) {
                    tHtml += "<td class=\"tdDate\">" + getSplitString(arrInfo[idx].round_normal) + "&nbsp;&nbsp;&nbsp;";
                    tHtml += "<font color=\"#0a601c\">" + parseInt(arrInfo[idx].round_power) + "</font></td>";
                    tHtml += "<td class=\"tdDate\">" + getSplitSum(arrInfo[idx].round_normal) + "</td>";
                    tHtml += "<td class=\"tdDate\">";
                    tHtml += getRoundResultHtml(arrInfo[idx].round_result_1, 1);
                    tHtml += getRoundResultHtml(arrInfo[idx].round_result_2, 2);
                    tHtml += getRoundResultHtml(arrInfo[idx].round_result_3, 3);
                    tHtml += getRoundResultHtml(arrInfo[idx].round_result_4, 4);
                    tHtml += getRoundResultHtml(arrInfo[idx].round_result_5, 5);
                    tHtml += "</td>";
                } else {
                    tHtml += "<td class=\"tdDate\"></td>";
                    tHtml += "<td class=\"tdDate\"></td>";
                    tHtml += "<td class=\"tdDate\"></td>";
                }
            } else {
                tHtml += "</td><td class=\"tdDate\" >" + arrInfo[idx].bet_round_no + "</td>";
                if (arrInfo[idx].bgb_round_fid != null) {
                    tHtml += "<td class=\"tdDate\">" + getSplitString(arrInfo[idx].bgb_round_normal) + "&nbsp;&nbsp;&nbsp;";
                    tHtml += "<font color=\"#0a601c\">" + parseInt(arrInfo[idx].bgb_round_power) + "</font></td>";
                    tHtml += "<td class=\"tdDate\">" + getSplitSum(arrInfo[idx].bgb_round_normal) + "</td>";
                    tHtml += "<td class=\"tdDate\">";
                    tHtml += getRoundResultHtml(arrInfo[idx].bgb_round_result_1, 1);
                    tHtml += getRoundResultHtml(arrInfo[idx].bgb_round_result_2, 2);
                    tHtml += getRoundResultHtml(arrInfo[idx].bgb_round_result_3, 3);
                    tHtml += getRoundResultHtml(arrInfo[idx].bgb_round_result_4, 4);
                    tHtml += getRoundResultHtml(arrInfo[idx].bgb_round_result_5, 5);
                    tHtml += "</td>";
                } else {
                    tHtml += "<td class=\"tdDate\"></td>";
                    tHtml += "<td class=\"tdDate\"></td>";
                    tHtml += "<td class=\"tdDate\"></td>";
                }
            }


            tHtml += "<td class=\"tdDate\" >" + arrInfo[idx].mb_emp_nickname + "</td>";
            tHtml += "<td class=\"tdDate\" >" + arrInfo[idx].mb_uid + "</td>";
            tHtml += "<td class=\"tdDate\" >" + arrInfo[idx].mb_nickname + "</td>";
            tHtml += "<td class=\"tdDate\" >" + arrInfo[idx].mb_ip_last + "</td>";
            tHtml += "<td class=\"tdDate\">" + getBetTypeHtml(arrInfo[idx].bet_mode) + "</td>";
            tHtml += "<td class=\"tdDate\">" + parseInt(arrInfo[idx].bet_before_money).toLocaleString() + "</td>";
            if (parseInt(arrInfo[idx].bet_money) >= 500000)
                tHtml += "<td class=\"tdDate\" style=\"color:#ef0000;\" >";
            else tHtml += "<td class=\"tdDate\">";
            tHtml += parseInt(arrInfo[idx].bet_money).toLocaleString() + "</td>";
            tHtml += "<td class=\"tdDate\">" + (parseInt(arrInfo[idx].bet_before_money) - parseInt(arrInfo[idx].bet_money)).toLocaleString() + "</td>";
            tHtml += "<td class=\"tdDate\">" + parseInt(arrInfo[idx].bet_win_money).toLocaleString() + "</td>";
            tHtml += "<td class=\"tdDate\">" + getResultLast(arrInfo[idx].bet_state) + "</td>";
            tHtml += "<td class=\"tdDate\">" + arrInfo[idx].bet_time + "</td>";
            tHtml += "</tr>";

        }

    }
    $('#tbodyList').html(tHtml);
}

function changeGame() {

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
        "round_id": $('#inputGameNo').val(),
        "mb_uid": $('#inputUserID').val()
    };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/pbbetlist_count',
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
        "mb_uid": $('#inputUserID').val(),
        "page": getActivePage(),
        "cntper": CountPerPage
    };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/pbbetlist_page',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            console.log(jResult);
            if (jResult.status == "success") {
                showPage(jResult.data, jResult.level);
            } else if (jResult.status == "logout") {
                location.reload();
            }
        },
        error: function(request, status, error) {
            console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }

    });
}



var mBetType = 0;

function showEditBet(objTr) {
    return;

    initEditBet();

    let game = $(objTr).data('game');
    let id = $(objTr).data('id');
    let mode = $(objTr).data('mode');

    if (game.length < 0 || id.length < 0 || mode.length < 0)
        return;

    let betedClass = 'btnGamebeted';
    $('.btnGameSelect').removeClass(betedClass);
    $('#divBetTypeNew_' + mode).addClass(betedClass);

    $("#tdUserInfo").html("아이디:" + $(objTr).data('uid') + "  닉네임:" + $(objTr).data('name'))
    $("#tdUserBet").html("현재배팅:" + getGameName(game) + " => " + getBetTypeHtml(mode));

    $('#divEditBet').data("id", id);
    $('#divEditBet').data("mode", mode);
    $('#divEditBet').data("game", game);


    $('#divEditBet').show();
}

function closeEditBet() {
    $('#divEditBet').hide();
}

function initEditBet() {
    mBetType = 0;

    $('.btnGameSelect').removeClass('btnGameSelected');
    $("#tdEditBet").html('');
}



function setBetType(iBetType) {

    let activeClass = 'btnGameSelected';
    $('.btnGameSelect').removeClass(activeClass);

    if (iBetType < 0 || (iBetType > 24 && iBetType < 33) || iBetType > 50)
        return;

    $('#divBetTypeNew_' + iBetType).addClass(activeClass);

    mBetType = iBetType;
    $("#tdEditBet").html("배팅선택:" + getBetTypeHtml(mBetType));
}


function saveEditBet() {
    if (!confirm('선택된 베팅정보를 저장하시겠습니까?'))
        return;

    var objData = {
        "game": $('#divEditBet').data("game"),
        "mode": mBetType,
        "id": $('#divEditBet').data("id"),
    };

    var jsonData = JSON.stringify(objData);

    console.log(jsonData);

    $.ajax({
        url: '/api/edit_bet',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);

            if (jResult.status == "success") {
                showAlert("조작이 성공되었습니다.");
                reqPage();
                closeEditBet();

            } else if (jResult.status == "fail") {
                showAlert("조작이 실패되었습니다.");
                reqPage();
                closeEditBet();
            } else if (jResult.status == "logout") {
                location.reload();
            }
        },
        error: function(request, status, error) {
            // console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }

    });

}