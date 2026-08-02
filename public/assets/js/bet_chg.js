$(document).ready(function() {
    reqBetPage();
});

function showPage(arrInfo) {
    let tHtml = "";

    if (arrInfo != null) {
        let lastRoundId = 0;
        let trStyle1 = "<tr style=\"background-color:#f7f4ac;\" ";
        let trStyle2 = "<tr style=\"background-color:#dddddd;\" ";
        let trStyleNo = 0;
        for (let idx in arrInfo) {

            if (lastRoundId != parseInt(arrInfo[idx].bet_round_fid)) {
                lastRoundId = parseInt(arrInfo[idx].bet_round_fid);
                trStyleNo = trStyleNo == 1 ? 2 : 1;
                tHtml += trStyleNo == 1 ? trStyle1 : trStyle2;
            } else tHtml += trStyleNo == 1 ? trStyle1 : trStyle2;

            tHtml += "><td class=\"tdDate\" style=\"height:40px;\">";
            tHtml += getGameName(arrInfo[idx].bet_game);
            tHtml += "</td><td class=\"tdDate\">";
            if (parseInt(arrInfo[idx].bet_game) == 1) {
                tHtml += arrInfo[idx].coin_round_hash != null ? arrInfo[idx].coin_round_hash : "";
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
            } else {
                tHtml += arrInfo[idx].bet_round_fid;
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
            }


            tHtml += "<td class=\"tdDate\" >" + arrInfo[idx].mb_emp_nickname + "</td>";
            tHtml += "<td class=\"tdDate\" >" + arrInfo[idx].mb_uid + "</td>";
            tHtml += "<td class=\"tdDate\" >" + arrInfo[idx].mb_nickname + "</td>";
            tHtml += "<td class=\"tdDate\">" + getBetTypeHtml(arrInfo[idx].bet_mode) + "</td>";
            if (parseInt(arrInfo[idx].bet_money) >= 500000)
                tHtml += "<td class=\"tdDate\" style=\"color:#ef0000;\" >";
            else tHtml += "<td class=\"tdDate\">";
            tHtml += parseInt(arrInfo[idx].bet_money).toLocaleString() + "</td>";
            tHtml += "<td class=\"tdDate\">" + parseInt(arrInfo[idx].bet_win_money).toLocaleString() + "</td>";
            tHtml += "<td class=\"tdDate\">" + getResultLast(arrInfo[idx].bet_state) + "</td>";
            tHtml += "<td class=\"tdDate\">" + arrInfo[idx].bet_time + "</td>";
            tHtml += "<td class=\"tdDate\">" ;
            if (arrInfo[idx].bet_state == 1) {
                tHtml += "<button class=\"btn_red\" onclick='chgBet(this);'" ;

                tHtml += " data-game='" + arrInfo[idx].bet_game + "' data-id='" + arrInfo[idx].bet_fid;
                tHtml += "' data-mode='" + arrInfo[idx].bet_mode;
                tHtml += "' data-uid='" + arrInfo[idx].mb_uid + "' data-name='" + arrInfo[idx].mb_nickname + "'";
                tHtml += ">변경</button" ;
            }
            tHtml += "</td>";

            tHtml += "</tr>";

        }

    }
    $('#tbodyList').html(tHtml);
}

function changeGame() {
    reqBetPage();
}

function reqSearch() {
    reqBetPage();
}


function reqBetPage() {
    var objData = {
        "game": $('#selectGameType').val(),
        "mb_uid": $('#inputUserID').val(),
    };
    // console.log(objData);
    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/pbbetlist_last',
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


function chgBet(objBtn){
    let game = $(objBtn).data('game');
    let id = $(objBtn).data('id');
    let mode = $(objBtn).data('mode');
    let target = -1;
    switch (parseInt(mode)){
        case 0: target = 1; break;
        case 1: target = 0; break;
        case 2: target = 3; break;
        case 3: target = 2; break;
        case 17: target = 1; break;
        case 18: target = 0;break;
        case 19: target = 3; break;
        case 20: target = 2; break;
        default:break;
    } 
    if(target < 0)
        return;

    let sBet = getBetTypeHtml(mode);
    let sTarget = getBetTypeHtml(target);

    Swal.fire({
        title: '유저배팅변경',
        html: sBet+"을 "+ sTarget+"으로 변경하시겠습니까?",
        showCancelButton: true,
        confirmButtonText: '확인',
        cancelButtonText: '취소',
    }).then((result) => {
        // console.log(result);
        if (result.value == true) {
          reqChgBet(game, id, mode);
        } 
    })

}


function reqChgBet(game, id, mode) {

    var objData = {
        "game": game,
        "mode": mode,
        "id": id,
    };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/chg_bet',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);
            if (jResult.status == "success") {
                showAlert("조작이 성공되었습니다.");
                reqBetPage();

            } else if (jResult.status == "fail") {
                if(jResult.msg)
                    showAlert(jResult.msg);
                else showAlert("조작이 실패되었습니다.");
                reqBetPage();
            } else if (jResult.status == "logout") {
                location.reload();
            }
        },
        error: function(request, status, error) {
            // console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }

    });

}