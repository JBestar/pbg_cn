var mUser;
var mWorker;

$(document).ready(function() {
    mUser = null;

    reqAssets();
    setTimeout(function() { reqAccount(); }, 500);
    setTimeout(function() { reqWaitTransfer(); }, 1000);
    startWorker();
});


function clickMenu(menuPage) {

    location.href = '/Main/' + menuPage;

}

function logOut() {
    location.replace('/pages/logout');
}

function showUserInfo(objUser) {
    if (objUser == null)
        return;

    mUser = objUser;
    $('#spanUserName').text(mUser.mb_nickname);
    $('#spanUserMoney').text(parseInt(mUser.mb_money).toLocaleString() + " 원");
    $('#spanUserPoint').text(parseInt(mUser.mb_point).toLocaleString() + " 원");
    $('#spanUserIp').text(mUser.mb_ip_last);
    if (mUser.mb_level == 8) {
        $('#spanGameRate').text(mUser.mb_game_pb_ratio + " %");
    }
}


function showAccountInfo(arrInfo) {

    let mbLevel = 8;
    if (mUser != null) {
        mbLevel = mUser.mb_level;
    }
    var tHtml = "";
    var bet_profit = 0,
        charge_profit = 0;;
    if (arrInfo.length == 2) {
        //금일 베팅, 충환전
        tHtml += "<td class=\"tdMoney\">" + Math.abs(arrInfo[0].money_bet).toLocaleString() + "</td>";
        tHtml += "<td class=\"tdMoney\">" + Math.abs(arrInfo[0].money_win).toLocaleString() + "</td>";
        if (mbLevel == 8)
            tHtml += "<td class=\"tdMoney\">" + Math.abs(arrInfo[0].point_agen).toLocaleString() + "</td>";
        else
            tHtml += "<td class=\"tdMoney\">" + Math.abs(arrInfo[0].point_agen + arrInfo[0].point_empl).toLocaleString() + "</td>";
        bet_profit = Math.abs(arrInfo[0].money_bet) - Math.abs(arrInfo[0].money_win) - Math.abs(arrInfo[0].point_agen) - Math.abs(arrInfo[0].point_empl);
        tHtml += "<td class=\"tdMoney\">";
        if (bet_profit >= 0) {
            tHtml += "<font color=\"#0000fe\">";
        } else {
            tHtml += "<font color=\"#fe0000\">";
        }
        tHtml += bet_profit.toLocaleString() + "</font></td>";
        tHtml += "<td class=\"tdMoney\">" + Math.abs(arrInfo[0].money_charge).toLocaleString() + "</td>";
        tHtml += "<td class=\"tdMoney\">" + Math.abs(arrInfo[0].money_exchange).toLocaleString() + "</td>";
        charge_profit = Math.abs(arrInfo[0].money_charge) - Math.abs(arrInfo[0].money_exchange);
        tHtml += "<td class=\"tdMoney\">" + charge_profit.toLocaleString() + "</td>";
        //월간 충환전
        tHtml += "<td class=\"tdMoney\">" + Math.abs(arrInfo[1].money_give).toLocaleString() + "</td>";
        tHtml += "<td class=\"tdMoney\">" + Math.abs(arrInfo[1].money_recovery).toLocaleString() + "</td>";
        tHtml += "<td class=\"tdMoney\">" + Math.abs(arrInfo[1].money_charge).toLocaleString() + "</td>";
        tHtml += "<td class=\"tdMoney\">" + Math.abs(arrInfo[1].money_exchange).toLocaleString() + "</td>";
        charge_profit = Math.abs(arrInfo[1].money_charge) - Math.abs(arrInfo[1].money_exchange);
        tHtml += "<td class=\"tdMoney\">";
        if (charge_profit >= 0) {
            tHtml += "<font color=\"#0000fe\">";
        } else {
            tHtml += "<font color=\"#fe0000\">";
        }
        tHtml += charge_profit.toLocaleString() + "</font></td>";

    }
    $('#tbAccount').html(tHtml);
}

function showWaitTansfer(arrInfo) {
    if (arrInfo == null)
        return;

    $('#spanChargeCnt').text(arrInfo['charge']);
    $('#spanExchangeCnt').text(arrInfo['exchange']);

    if (arrInfo['charge'] > 0) {

        speak("사랑합니다.", { rate: 1, pitch: 1.2 });
    } else if (arrInfo['exchange'] > 0) {
        speak("미안합니다.", { rate: 1, pitch: 1.2 });
    }

    if (arrInfo['level'] == 8) {
        $('#spanQnaCnt').text(arrInfo['notice']);
        if (arrInfo['notice'] > 0) {
            if (mUser != null) {
                speak(mUser.mb_nickname, { rate: 1, pitch: 1.2 });
            }
            setTimeout(function() { speak("문의가 도착하였습니다.", { rate: 1, pitch: 1.2 }); }, 1000);

        }
    }

}


function pointToMoney() {
    if (!confirm('포인트를 전환하시겠습니까?'))
        return;

    $.ajax({
        url: '/api/point_change',
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);

            if (jResult.status == "success") {
                reqAssets();
            } else if (jResult.status == "logout") {
                location.reload();
            }
        },
        error: function(request, status, error) {
            // console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }

    });
}


function reqAssets() {

    $.ajax({
        type: "POST",
        dataType: "json",
        url: "/api/assets",
        success: function(jResult) {
            // console.log(jResult);
            if (jResult.status == "success") {
                showUserInfo(jResult.data);
            } else if (jResult.status == "logout") {
                location.replace('/');
            }
        },
        error: function(request, status, error) {
            // console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }
    });


}


function reqAccount() {

    $.ajax({
        type: "POST",
        dataType: "json",
        url: "/api/account_today",
        success: function(jResult) {
            // console.log(jResult);
            if (jResult.status == "success") {
                showAccountInfo(jResult.data);
            } else if (jResult.status == "logout") {
                location.replace('/');
            }
        },
        error: function(request, status, error) {
            // console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }
    });


}



function reqWaitTransfer() {

    $.ajax({
        type: "POST",
        dataType: "json",
        url: "/api/transfer_wait",
        success: function(jResult) {
            // console.log(jResult);
            if (jResult.status == "success") {
                showWaitTansfer(jResult.data);
            } else if (jResult.status == "logout") {
                location.reload();
            }
        },
        error: function(request, status, error) {
            // console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }
    });

}










/*=============MainLoop=============== */


// worker 실행
function startWorker() {

    // Worker 지원 유무 확인
    if (!!window.Worker) {

        // 실행하고 있는 워커 있으면 중지시키기
        if (mWorker) {
            stopWorker();
        }

        mWorker = new Worker('/assets/js/lib/worker.js');
        mWorker.postMessage('워커 실행'); // 워커에 메시지를 보낸다.

        // 워커로 부터 메시지를 수신한다.
        mWorker.onmessage = function(e) {
            showTime();

        };
    }

}


// worker 중지
function stopWorker() {


    if (mWorker) {
        mWorker.terminate();
        mWorker = null;
    }

}



function showTime() {

    let tmCurrent = new Date();

    let nCurSec = tmCurrent.getSeconds();
    let nCurMin = tmCurrent.getMinutes();


    if (nCurSec % 10 == 2) {
        reqAssets();
        if (typeof reqLoopSec !== 'undefined' && typeof reqLoopSec === 'function') {
            setTimeout(function() { reqLoopSec(); }, 1000);
        }
    }

    if (nCurSec % 10 == 4) {
        reqWaitTransfer();
    }

    if (nCurMin % 5 == 0 && nCurSec % 60 == 0) {
        reqAccount();

        if (typeof reqLoopRound !== 'undefined' && typeof reqLoopRound === 'function') {
            setTimeout(function() { reqLoopRound(); }, 1000);
        } 

    }

    if (nCurMin % 5 == 0 && (nCurSec % 60 == 15 || nCurSec % 60 == 0) ) {
        if (typeof reqBetPage !== 'undefined' && typeof reqBetPage === 'function') {
            setTimeout(function() { reqBetPage(); }, 1000);
        }
    }

}