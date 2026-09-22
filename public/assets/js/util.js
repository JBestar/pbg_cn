const GAME_POWER_BALL = 0;
const GAME_COIN5_BALL = 1;
const GAME_BOGLE_BALL = 2;

function showAlert(msg) {
    $('#layui-layer-shade').show();
    $('#layui-layer').show();
    $('#layui-layer-msg').text(msg);

}

/** Point / commission display — always 2 decimal places (display only). */
function fmtPoint(n) {
    return Number(n || 0).toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}


function closeAlert() {
    $('#layui-layer-shade').hide();
    $('#layui-layer').hide();
    $('#layui-layer-msg').text('');

}



function setCookie(name, value, expiredays) {

    if (expiredays) {
        var date = new Date();
        date.setTime(date.getTime() + (expiredays * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + date.toGMTString();
    } else {
        var expires = "; expires=0";
    }

    document.cookie = name + "=" + value + expires + "; path=/";

}


function getCookie(name) {

    var cName = name + "=";
    var x = 0;
    while (x <= document.cookie.length) {
        var y = (x + cName.length);
        if (document.cookie.substring(x, y) == cName) {
            if ((endOfCookie = document.cookie.indexOf(";", y)) == -1)
                endOfCookie = document.cookie.length;
            return unescape(document.cookie.substring(y, endOfCookie));
        }

        x = document.cookie.indexOf(" ", x) + 1;
        if (x == 0)
            break;
    }
    return "";
}


var TotalCount = 0;
var CountPerPage = 20;
var ViewPage = 10;


function setFirstPage() {

    if (TotalCount <= CountPerPage) {
        $("#tdPageNation").hide();
        $("#spanPaginationNum").html("");
        return;
    }

    var tHtml = "";
    var pageCnt = TotalCount % CountPerPage == 0 ? TotalCount / CountPerPage : TotalCount / CountPerPage + 1;

    $("#aPagePrev").hide();

    if (pageCnt > ViewPage) {
        pageCnt = ViewPage;
    }

    if (TotalCount > CountPerPage * pageCnt)
        $("#aPageNext").show();
    else $("#aPageNext").hide();


    for (var page = 1; page <= pageCnt; page++) {
        tHtml += "<a class=\"light-theme\" >";
        if (page == 1)
            tHtml += "<span class=\"current\">";
        else tHtml += "<span class=\"light-theme\">";

        tHtml += page.toString();
        tHtml += "</span></a>";

    }
    $("#spanPaginationNum").html(tHtml);
    $("#tdPageNation").show();
    addPageEventListner();


}

function getFirstPage() {
    var pageBtns = $("#spanPaginationNum").find("span");
    if (pageBtns == null)
        return -1;

    if (pageBtns.length < 1)
        return -1;

    if (pageBtns[0].innerHTML.length > 0)
        return parseInt(pageBtns[0].innerHTML);
    return -1;

}

function getActivePage() {
    var pageBtns = $("#spanPaginationNum").find(".current");
    if (pageBtns == null)
        return 1;

    if (pageBtns.length < 1)
        return 1;

    if (pageBtns[0].innerHTML.length > 0)
        return parseInt(pageBtns[0].innerHTML);
    return 1;

}

function prevPage() {

    if (TotalCount <= CountPerPage) {
        $("#tdPageNation").hide();
        $("#spanPaginationNum").html("");
        return;
    }

    var firstPage = getFirstPage();
    if (firstPage < 0)
        return;

    var layountCnt = parseInt(firstPage / ViewPage) * ViewPage * CountPerPage;

    if (layountCnt > TotalCount)
        return;

    var tHtml = "";
    var pageCnt = layountCnt % CountPerPage == 0 ? layountCnt / CountPerPage : layountCnt / CountPerPage + 1;

    if (layountCnt > ViewPage * CountPerPage)
        $("#aPagePrev").show();
    else $("#aPagePrev").hide();

    if (pageCnt > ViewPage) {
        pageCnt = ViewPage;
    }
    $("#aPageNext").show();

    firstPage -= ViewPage;
    for (var page = 1; page <= pageCnt; page++) {

        tHtml += "<a class=\"light-theme\" >";
        if (page == 1)
            tHtml += "<span class=\"current\">";
        else tHtml += "<span class=\"light-theme\">";

        tHtml += (firstPage + page - 1).toString();
        tHtml += "</span></a>";

    }
    $("#spanPaginationNum").html(tHtml);
    $("#tdPageNation").show();
    addPageEventListner();
    reqPage();
}

function nextPage() {

    if (TotalCount <= CountPerPage) {
        $("#tdPageNation").hide();
        $("#spanPaginationNum").html("");
        return;
    }

    var pageBtns = $("#spanPaginationNum").find("span");
    if (pageBtns == null)
        return;

    if (pageBtns.length < ViewPage)
        return;

    var firstPage = parseInt(pageBtns[0].innerHTML);

    var layountCnt = TotalCount - (parseInt(firstPage / ViewPage) + 1) * ViewPage * CountPerPage;

    var tHtml = "";
    var pageCnt = layountCnt % CountPerPage == 0 ? layountCnt / CountPerPage : layountCnt / CountPerPage + 1;

    $("#aPagePrev").show();
    if (pageCnt > ViewPage) {
        pageCnt = ViewPage;
    }

    if (layountCnt > CountPerPage * pageCnt)
        $("#aPageNext").show();
    else $("#aPageNext").hide();

    firstPage += ViewPage;
    for (var page = 1; page <= pageCnt; page++) {
        tHtml += "<a class=\"light-theme\" >";
        if (page == 1)
            tHtml += "<span class=\"current\">";
        else tHtml += "<span class=\"light-theme\">";

        tHtml += (firstPage + page - 1).toString();
        tHtml += "</span></a>";

    }
    $("#spanPaginationNum").html(tHtml);
    $("#tdPageNation").show();
    addPageEventListner();
    reqPage();

}

function addPageEventListner() {
    var pageBtns = $("#spanPaginationNum").find("span");
    if (pageBtns == null)
        return;

    for (var idx = 0; idx < pageBtns.length; idx++) {

        pageBtns[idx].addEventListener("click", function() {

            if (this.className != "current") {
                $("#spanPaginationNum").find(".current").removeClass("current").addClass("light-theme");
                this.className = "current";
                reqPage();
            }

        });

    }
}

//-----------------------------------
function getGameName(gameId) {

    if (parseInt(gameId) == GAME_COIN5_BALL) {
        return "코인볼";
    } else if (parseInt(gameId) == GAME_POWER_BALL) {
        return (window.ADMIN_I18N && window.ADMIN_I18N.game_powerball) ? window.ADMIN_I18N.game_powerball : "파워볼";
    } else {
        return "보글볼";
    }
}

function adminT(key, fallback) {
    if (window.ADMIN_I18N && window.ADMIN_I18N[key]) {
        return window.ADMIN_I18N[key];
    }
    return fallback;
}

function spanCls(base, t, size) {
    return '<span class="' + base + (size ? ' ' + size : '') + '">' + t + '</span>';
}
function spanBlue(t, size) { return spanCls('spanBlue', t, size); }
function spanRed(t, size) { return spanCls('spanRed', t, size); }
function spanUnOverBlue(t, size) { return spanCls('spanUnOverBlue', t, size); }
function spanUnOverRed(t, size) { return spanCls('spanUnOverRed', t, size); }
function spanGreen(t, size) { return spanCls('spanGreen', t, size); }
function spanGray(t, size) { return spanCls('spanGray', t, size); }

function betMarks() {
    return {
        odd: adminT('mark_odd', 'P'),
        even: adminT('mark_even', 'B'),
        under: adminT('mark_under', 'P'),
        over: adminT('mark_over', 'B'),
        big: adminT('mark_big', '대'),
        mid: adminT('mark_mid', '중'),
        small: adminT('mark_small', '소'),
        pb: adminT('mark_pb', adminT('game_powerball', '파워볼')),
        power: adminT('mark_power', '파워'),
        pOdd: adminT('mark_power_odd', '파워홀'),
        pEven: adminT('mark_power_even', '파워짝'),
        normal: adminT('mark_normal', '일반'),
        p: adminT('mark_p', 'P'),
        b: adminT('mark_b', 'B'),
        room1: adminT('room1', '제1번'),
        room2: adminT('room2', '제2번'),
        room3: adminT('room3', '제3번'),
        room4: adminT('room4', '제4번'),
    };
}

function roomPartHtml(roomKey, isP) {
    var m = betMarks();
    var room = m[roomKey] || roomKey;
    var mark = isP ? m.p : m.b;
    return room + ' ' + (isP ? spanBlue(mark) : spanRed(mark));
}

function roomPartText(roomKey, isP) {
    var m = betMarks();
    var room = m[roomKey] || roomKey;
    var mark = isP ? m.p : m.b;
    return room + ' ' + mark;
}

function getBetTypeText(type) {
    var t = parseInt(type);
    /* terminal modes 1–16 */
    var term = {
        1: function () { return roomPartText('room1', true); },
        2: function () { return roomPartText('room1', false); },
        3: function () { return roomPartText('room2', true); },
        4: function () { return roomPartText('room2', false); },
        5: function () { return roomPartText('room1', true) + ' + ' + roomPartText('room2', true); },
        6: function () { return roomPartText('room1', false) + ' + ' + roomPartText('room2', true); },
        7: function () { return roomPartText('room1', true) + ' + ' + roomPartText('room2', false); },
        8: function () { return roomPartText('room1', false) + ' + ' + roomPartText('room2', false); },
        9: function () { return roomPartText('room3', true); },
        10: function () { return roomPartText('room3', false); },
        11: function () { return roomPartText('room4', true); },
        12: function () { return roomPartText('room4', false); },
        13: function () { return roomPartText('room3', true) + ' + ' + roomPartText('room4', true); },
        14: function () { return roomPartText('room3', false) + ' + ' + roomPartText('room4', true); },
        15: function () { return roomPartText('room3', true) + ' + ' + roomPartText('room4', false); },
        16: function () { return roomPartText('room3', false) + ' + ' + roomPartText('room4', false); },
    };
    if (term[t]) return '[' + term[t]() + ']   ';
    if (t >= 30 && t <= 39) {
        return '[' + (betMarks().pb || '파워볼') + ' ' + (t - 30) + '] ';
    }

    /* legacy tiger admin codes (if any remain) */
    switch (t) {
        case 0: return '[' + roomPartText('room3', true) + ']   ';
        case 17: return '[' + roomPartText('room1', true) + ']   ';
        case 18: return '[' + roomPartText('room1', false) + ']   ';
        case 19: return '[' + roomPartText('room2', true) + ']   ';
        case 20: return '[' + roomPartText('room2', false) + ']   ';
        case 21: return '[' + roomPartText('room1', true) + ' + ' + roomPartText('room2', true) + ']   ';
        case 22: return '[' + roomPartText('room1', false) + ' + ' + roomPartText('room2', true) + ']   ';
        case 23: return '[' + roomPartText('room1', true) + ' + ' + roomPartText('room2', false) + ']   ';
        case 24: return '[' + roomPartText('room1', false) + ' + ' + roomPartText('room2', false) + ']   ';
        case 41: case 42: case 43: case 44: case 45:
        case 46: case 47: case 48: case 49: case 50:
            return '[' + (betMarks().power || '파워') + ' ' + (t - 41) + '] ';
        default:
            return '';
    }
}

function getBetTypeTextOrg(type) {
    var plain = getBetTypeText(type);
    return String(plain).replace(/^\[/, '').replace(/\]\s*$/, '').trim();
}

function getBetTypeHtml(type) {
    var t = parseInt(type);
    var term = {
        1: function () { return roomPartHtml('room1', true); },
        2: function () { return roomPartHtml('room1', false); },
        3: function () { return roomPartHtml('room2', true); },
        4: function () { return roomPartHtml('room2', false); },
        5: function () { return roomPartHtml('room1', true) + ' + ' + roomPartHtml('room2', true); },
        6: function () { return roomPartHtml('room1', false) + ' + ' + roomPartHtml('room2', true); },
        7: function () { return roomPartHtml('room1', true) + ' + ' + roomPartHtml('room2', false); },
        8: function () { return roomPartHtml('room1', false) + ' + ' + roomPartHtml('room2', false); },
        9: function () { return roomPartHtml('room3', true); },
        10: function () { return roomPartHtml('room3', false); },
        11: function () { return roomPartHtml('room4', true); },
        12: function () { return roomPartHtml('room4', false); },
        13: function () { return roomPartHtml('room3', true) + ' + ' + roomPartHtml('room4', true); },
        14: function () { return roomPartHtml('room3', false) + ' + ' + roomPartHtml('room4', true); },
        15: function () { return roomPartHtml('room3', true) + ' + ' + roomPartHtml('room4', false); },
        16: function () { return roomPartHtml('room3', false) + ' + ' + roomPartHtml('room4', false); },
    };
    if (term[t]) return term[t]();
    if (t >= 30 && t <= 39) {
        return (betMarks().pb || '파워볼') + ' ' + (t - 30);
    }
    switch (t) {
        case 0: return roomPartHtml('room3', true);
        case 17: return roomPartHtml('room1', true);
        case 18: return roomPartHtml('room1', false);
        case 19: return roomPartHtml('room2', true);
        case 20: return roomPartHtml('room2', false);
        case 21: return roomPartHtml('room1', true) + ' + ' + roomPartHtml('room2', true);
        case 22: return roomPartHtml('room1', false) + ' + ' + roomPartHtml('room2', true);
        case 23: return roomPartHtml('room1', true) + ' + ' + roomPartHtml('room2', false);
        case 24: return roomPartHtml('room1', false) + ' + ' + roomPartHtml('room2', false);
        case 41: case 42: case 43: case 44: case 45:
        case 46: case 47: case 48: case 49: case 50:
            return (betMarks().power || '파워') + ' ' + (t - 41);
        default:
            return getBetTypeTextOrg(t);
    }
}

function getResultWin(status, no, bLast) {
    if (status == 0) {
        return "<span class='spanGreen' onclick='cancelBet(" + no + ", " + bLast + ");' style='cursor:pointer;'>취소</span>";
    } else if (status == 1) {
        return "<span class='spanRed'>낙첨</span>";
    } else if (status == 2) {
        return "<span class='spanBlue'>당첨</span>";
    } else if (status == 3) {
        return "<span class='spanGray'>적특</span>";
    } else {
        return "";
    }
}

function getResultLast(status) {
    if (status == 0) {
        return spanGray(adminT('bet_waiting', '대기중'));
    } else if (status == 1) {
        return spanRed(adminT('bet_lose', '낙첨'));
    } else if (status == 2) {
        return spanBlue(adminT('bet_win', '당첨'));
    } else if (status == 3) {
        return spanGray(adminT('bet_void', '적특'));
    } else {
        return "";
    }
}


function getMoneyChangeTypeText(iType) {
    switch (parseInt(iType)) {
        case 1:
            return "충전";
        case 2:
            return "환전";
        case 3:
            return "알충전";
        case 4:
            return "알회수";
        case 5:
            return "게임구매";
        case 6:
            return "배팅당첨";
        case 7:
            return "구매취소";
        case 8:
            return "포인트적립";
        case 9:
            return "포인트취소";
        case 10:
            return "포인트전환";
        case 11:
            return "충전승인";
        case 12:
            return "환전승인";
        case 13:
            return "알충전승인";
        case 14:
            return "알회수승인";

        default:
            return '';

    }
}

function getMemberLevelText(nLevel) {
    switch (parseInt(nLevel)) {
        case 7:
            return "매장";
        case 8:
            return "총판";
        default:
            return '';
    }
}

function getChargeTypeText(iState) {
    switch (parseInt(iState)) {
        case 0:
            return "신청충전";
        case 1:
            return "알충전";
        default:
            return '';
    }
}


function getExchangeTypeText(iState) {
    switch (parseInt(iState)) {
        case 0:
            return "신청환전";
        case 1:
            return "알회수";
        default:
            return '';
    }
}

function getChargeStateText(iState) {
    switch (parseInt(iState)) {
        case 0:
            return "대기";
        case 1:
            return "완료";
        case 2:
            return "취소";
        default:
            return '';
    }
}



function getQnaStateText(iState) {

    if (iState == 0) {
        return "<font color=\"#fe0000\">답변미정</font>";
    } else {
        return "답변완료";
    }


}

function getMemoStateText(iState) {
    if (iState == 0) {
        return '읽지않음';
    } else {
        return '읽음';
    }
}


function getRoundResultHtml(round_result, mode) {
    var tHtml = "";
    if (round_result == null)
        return tHtml;
    switch (mode) {
        case 1:
            if (round_result == 'P') tHtml = roomPartHtml('room1', true) + '&nbsp;';
            else if (round_result == 'B') tHtml = roomPartHtml('room1', false) + '&nbsp;';
            break;
        case 2:
            if (round_result == 'P') tHtml = roomPartHtml('room2', true) + '&nbsp;';
            else if (round_result == 'B') tHtml = roomPartHtml('room2', false) + '&nbsp;';
            break;
        case 3:
            if (round_result == 'P') tHtml = roomPartHtml('room3', true) + '&nbsp;';
            else if (round_result == 'B') tHtml = roomPartHtml('room3', false) + '&nbsp;';
            break;
        case 4:
            if (round_result == 'P') tHtml = roomPartHtml('room4', true) + '&nbsp;';
            else if (round_result == 'B') tHtml = roomPartHtml('room4', false) + '&nbsp;';
            break;
        case 5:
            var m = betMarks();
            if (round_result == 'L') tHtml = spanGreen(m.big, 'bw-lms') + '&nbsp;&nbsp;';
            else if (round_result == 'M') tHtml = spanGreen(m.mid, 'bw-lms') + '&nbsp;';
            else if (round_result == 'S') tHtml = spanGreen(m.small, 'bw-lms') + '&nbsp;';
            break;
    }
    return tHtml;
}


//------------------------------------



function getSplitString(round_nums) {
    var resStr = "";
    var arrNum = round_nums.split(',');
    for (var idx in arrNum) {
        if (idx > 0)
            resStr += ",";
        resStr += getZeroString(arrNum[idx]);
    }
    return resStr;
}


function getSplitSum(round_nums) {
    var nSum = 0;
    var arrNum = round_nums.split(',');
    for (var idx in arrNum) {
        nSum += parseInt(arrNum[idx]);
    }
    return nSum;
}


function getZeroString(num) {
    var retVal = num;
    if (parseInt(num) < 10) {
        retVal = "0" + parseInt(num);
    }
    return retVal;
}





function speak(text, opt_prop) {
    if (typeof SpeechSynthesisUtterance === "undefined" || typeof window.speechSynthesis === "undefined") {
        //alert("이 브라우저는 음성 합성을 지원하지 않습니다.");
        return;
    }

    window.speechSynthesis.cancel(); // 현재 읽고있다면 초기화

    const prop = opt_prop; // {}

    const speechMsg = new SpeechSynthesisUtterance();
    speechMsg.rate = prop.rate; // 1 // 속도: 0.1 ~ 10      
    speechMsg.pitch = prop.pitch; // 1 // 음높이: 0 ~ 2
    speechMsg.lang = "ko-KR"; //prop.lang ;// "ko-KR"
    speechMsg.text = text;

    // SpeechSynthesisUtterance에 저장된 내용을 바탕으로 음성합성 실행
    window.speechSynthesis.speak(speechMsg);
}