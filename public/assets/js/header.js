var mUser;
var mWorker;

$(document).ready(function() {
    mUser = null;

    reqAssets();
    setTimeout(function() { reqAccount(); }, 500);
    setTimeout(function() { reqWaitTransfer(); }, 1000);
    startWorker();

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') closeAdminMenu();
    });
});


function clickMenu(menuPage) {
    closeAdminMenu();
    location.href = '/Main/' + menuPage;
}

function toggleAdminMenu() {
    document.body.classList.toggle('admin-menu-open');
}

function closeAdminMenu() {
    document.body.classList.remove('admin-menu-open');
}

function openAdminMenu() {
    document.body.classList.add('admin-menu-open');
}

function logOut() {
    location.replace('/pages/logout');
}

/** 본사: 상단 사이트 점검 토글 */
function toggleSiteMaintain() {
    var $btn = $('#btnSiteMaintain');
    if (!$btn.length) return;
    var cur = parseInt($btn.attr('data-lock'), 10) || 0;
    var next = cur ? 0 : 1;
    if (next === 0) {
        if (!confirm('정상운영하시겠습니까?')) return;
    } else {
        if (!confirm('점검을 진행하시겠습니까?')) return;
    }
    $.ajax({
        url: '/api/maintain_change',
        data: { json_: JSON.stringify({ bet_lock: next }) },
        type: 'post',
        dataType: 'json',
        success: function (jResult) {
            if (jResult.status === 'success') {
                location.reload();
            } else if (jResult.status === 'logout') {
                location.reload();
            }
        }
    });
}

function showUserInfo(objUser) {
    if (objUser == null)
        return;

    mUser = objUser;
    $('#spanUserName').text(mUser.mb_nickname);
    $('#spanUserMoney').text(parseInt(mUser.mb_money).toLocaleString() + " 원");
    $('#spanUserPoint').text(fmtPoint(mUser.mb_point) + " 원");
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
    if (!arrInfo || arrInfo.length < 2 || !arrInfo[0] || !arrInfo[1]) {
        return;
    }
    var tHtml = "";
    var bet_profit = 0,
        charge_profit = 0;
    //금일 베팅, 충환전
    tHtml += "<td class=\"tdMoney\">" + Math.abs(arrInfo[0].money_bet || 0).toLocaleString() + "</td>";
    tHtml += "<td class=\"tdMoney\">" + Math.abs(arrInfo[0].money_win || 0).toLocaleString() + "</td>";
    if (mbLevel == 8)
        tHtml += "<td class=\"tdMoney\">" + fmtPoint(Math.abs(arrInfo[0].point_agen || 0)) + "</td>";
    else
        tHtml += "<td class=\"tdMoney\">" + fmtPoint(Math.abs(arrInfo[0].point_agen || 0) + Math.abs(arrInfo[0].point_empl || 0)) + "</td>";
    bet_profit = Math.abs(arrInfo[0].money_bet || 0) - Math.abs(arrInfo[0].money_win || 0) - Math.abs(arrInfo[0].point_agen || 0) - Math.abs(arrInfo[0].point_empl || 0);
    tHtml += "<td class=\"tdMoney\">";
    if (bet_profit >= 0) {
        tHtml += "<font color=\"#0000fe\">";
    } else {
        tHtml += "<font color=\"#fe0000\">";
    }
    tHtml += fmtPoint(bet_profit) + "</font></td>";
    tHtml += "<td class=\"tdMoney\">" + Math.abs(arrInfo[0].money_charge || 0).toLocaleString() + "</td>";
    tHtml += "<td class=\"tdMoney\">" + Math.abs(arrInfo[0].money_exchange || 0).toLocaleString() + "</td>";
    charge_profit = Math.abs(arrInfo[0].money_charge || 0) - Math.abs(arrInfo[0].money_exchange || 0);
    tHtml += "<td class=\"tdMoney\">";
    if (charge_profit >= 0) {
        tHtml += "<font color=\"#0000fe\">";
    } else {
        tHtml += "<font color=\"#fe0000\">";
    }
    tHtml += charge_profit.toLocaleString() + "</font></td>";
    //월간 알충전/회수/충전/환전
    tHtml += "<td class=\"tdMoney\">" + Math.abs(arrInfo[1].money_give || 0).toLocaleString() + "</td>";
    tHtml += "<td class=\"tdMoney\">" + Math.abs(arrInfo[1].money_recovery || 0).toLocaleString() + "</td>";
    tHtml += "<td class=\"tdMoney\">" + Math.abs(arrInfo[1].money_charge || 0).toLocaleString() + "</td>";
    tHtml += "<td class=\"tdMoney\">" + Math.abs(arrInfo[1].money_exchange || 0).toLocaleString() + "</td>";
    charge_profit = Math.abs(arrInfo[1].money_charge || 0) - Math.abs(arrInfo[1].money_exchange || 0);
    tHtml += "<td class=\"tdMoney\">";
    if (charge_profit >= 0) {
        tHtml += "<font color=\"#0000fe\">";
    } else {
        tHtml += "<font color=\"#fe0000\">";
    }
    tHtml += charge_profit.toLocaleString() + "</font></td>";

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