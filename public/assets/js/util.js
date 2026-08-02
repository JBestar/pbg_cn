const GAME_POWER_BALL = 0;
const GAME_COIN5_BALL = 1;
const GAME_BOGLE_BALL = 2;

function showAlert(msg) {
    $('#layui-layer-shade').show();
    $('#layui-layer').show();
    $('#layui-layer-msg').text(msg);

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
        odd: adminT('mark_odd', '홀'),
        even: adminT('mark_even', '짝'),
        under: adminT('mark_under', '언더'),
        over: adminT('mark_over', '오버'),
        big: adminT('mark_big', '대'),
        mid: adminT('mark_mid', '중'),
        small: adminT('mark_small', '소'),
        pb: adminT('mark_pb', adminT('game_powerball', '파워볼')),
        power: adminT('mark_power', '파워'),
        pOdd: adminT('mark_power_odd', '파워홀'),
        pEven: adminT('mark_power_even', '파워짝'),
        normal: adminT('mark_normal', '일반')
    };
}

function getBetTypeText(type) {

    switch (parseInt(type)) {
        case 0:
            return '[홀]   ';
        case 1:
            return '[짝]   ';
        case 2:
            return '[언더]   ';
        case 3:
            return '[오버]   ';
        case 4:
            return '[홀 + 언더]   ';
        case 5:
            return '[짝 + 언더]   ';
        case 6:
            return '[홀 + 오버]   ';
        case 7:
            return '[짝 + 오버]   ';
        case 8:
            return '[대]   ';
        case 9:
            return '[중]   ';
        case 10:
            return '[소]   ';
        case 11:
            return '[홀 + 대]   ';
        case 12:
            return '[홀 + 중]   ';
        case 13:
            return '[홀 + 소]   ';
        case 14:
            return '[짝 + 대]   ';
        case 15:
            return '[짝 + 중]   ';
        case 16:
            return '[짝 + 소]   ';
        case 17:
            return '[파워볼 홀]   ';
        case 18:
            return '[파워볼 짝]   ';
        case 19:
            return '[파워볼 언더]   ';
        case 20:
            return '[파워볼 오버]   ';
        case 21:
            return '[파워 홀 + 언더]   ';
        case 22:
            return '[파워 짝 + 언더]   ';
        case 23:
            return '[파워 홀 + 오버]   ';
        case 24:
            return '[파워 짝 + 오버]   ';

        case 33:
            return '[홀 + 언더 + 파홀]';
        case 34:
            return '[홀 + 언더 + 파짝]';
        case 35:
            return '[홀 + 오버 + 파홀]';
        case 36:
            return '[홀 + 오버 + 파짝]';
        case 37:
            return '[짝 + 언더 + 파홀]';
        case 38:
            return '[짝 + 언더 + 파짝]';
        case 39:
            return '[짝 + 오버 + 파홀]';
        case 40:
            return '[짝 + 오버 + 파짝]';

        case 41:
            return '[파워 0] ';
        case 42:
            return '[파워 1] ';
        case 43:
            return '[파워 2] ';
        case 44:
            return '[파워 3] ';
        case 45:
            return '[파워 4] ';
        case 46:
            return '[파워 5] ';
        case 47:
            return '[파워 6] ';
        case 48:
            return '[파워 7] ';
        case 49:
            return '[파워 8] ';
        case 50:
            return '[파워 9] ';

        default:
            return '';
    }
}

function getBetTypeTextOrg(type) {
    var m = betMarks();
    switch (parseInt(type)) {
        case 0: return m.odd;
        case 1: return m.even;
        case 2: return m.under;
        case 3: return m.over;
        case 4: return m.odd + ' + ' + m.under;
        case 5: return m.even + ' + ' + m.under;
        case 6: return m.odd + ' + ' + m.over;
        case 7: return m.even + ' + ' + m.over;
        case 8: return m.big;
        case 9: return m.mid;
        case 10: return m.small;
        case 11: return m.odd + ' + ' + m.big;
        case 12: return m.odd + ' + ' + m.mid;
        case 13: return m.odd + ' + ' + m.small;
        case 14: return m.even + ' + ' + m.big;
        case 15: return m.even + ' + ' + m.mid;
        case 16: return m.even + ' + ' + m.small;
        case 17: return m.pb + ' ' + m.odd;
        case 18: return m.pb + ' ' + m.even;
        case 19: return m.pb + ' ' + m.under;
        case 20: return m.pb + ' ' + m.over;
        case 21: return m.power + ' ' + m.odd + ' + ' + m.under;
        case 22: return m.power + ' ' + m.even + ' + ' + m.under;
        case 23: return m.power + ' ' + m.odd + ' + ' + m.over;
        case 24: return m.power + ' ' + m.even + ' + ' + m.over;
        case 25: return m.power + ' ' + m.odd + ' + ' + m.normal + ' ' + m.odd;
        case 26: return m.power + ' ' + m.odd + ' + ' + m.normal + ' ' + m.even;
        case 27: return m.power + ' ' + m.even + ' + ' + m.normal + ' ' + m.odd;
        case 28: return m.power + ' ' + m.even + ' + ' + m.normal + ' ' + m.even;
        case 29: return m.power + ' ' + m.under + ' + ' + m.normal + ' ' + m.under;
        case 30: return m.power + ' ' + m.under + ' + ' + m.normal + ' ' + m.over;
        case 31: return m.power + ' ' + m.over + ' + ' + m.normal + ' ' + m.under;
        case 32: return m.power + ' ' + m.over + ' + ' + m.normal + ' ' + m.over;
        case 33: return m.odd + ' + ' + m.under + ' + ' + m.pOdd;
        case 34: return m.odd + ' + ' + m.under + ' + ' + m.pEven;
        case 35: return m.odd + ' + ' + m.over + ' + ' + m.pOdd;
        case 36: return m.odd + ' + ' + m.over + ' + ' + m.pEven;
        case 37: return m.even + ' + ' + m.under + ' + ' + m.pOdd;
        case 38: return m.even + ' + ' + m.under + ' + ' + m.pEven;
        case 39: return m.even + ' + ' + m.over + ' + ' + m.pOdd;
        case 40: return m.even + ' + ' + m.over + ' + ' + m.pEven;
        case 41: return m.power + ' 0';
        case 42: return m.power + ' 1';
        case 43: return m.power + ' 2';
        case 44: return m.power + ' 3';
        case 45: return m.power + ' 4';
        case 46: return m.power + ' 5';
        case 47: return m.power + ' 6';
        case 48: return m.power + ' 7';
        case 49: return m.power + ' 8';
        case 50: return m.power + ' 9';
        default:
            return '';
    }
}

function getBetTypeHtml(type) {
    var m = betMarks();
    switch (parseInt(type)) {
        case 0: return spanBlue(m.odd, 'bw-oe');
        case 1: return spanRed(m.even, 'bw-oe');
        case 2: return spanUnOverBlue(m.under, 'bw-uo');
        case 3: return spanUnOverRed(m.over, 'bw-uo');
        case 4: return spanBlue(m.odd, 'bw-oe') + ' + ' + spanUnOverBlue(m.under, 'bw-uo');
        case 5: return spanRed(m.even, 'bw-oe') + ' + ' + spanUnOverBlue(m.under, 'bw-uo');
        case 6: return spanBlue(m.odd, 'bw-oe') + ' + ' + spanUnOverRed(m.over, 'bw-uo');
        case 7: return spanRed(m.even, 'bw-oe') + ' + ' + spanUnOverRed(m.over, 'bw-uo');
        case 8: return spanGreen(m.big, 'bw-lms');
        case 9: return spanGreen(m.mid, 'bw-lms');
        case 10: return spanGreen(m.small, 'bw-lms');
        case 11: return spanBlue(m.odd, 'bw-oe') + ' + ' + spanGreen(m.big, 'bw-lms');
        case 12: return spanBlue(m.odd, 'bw-oe') + ' + ' + spanGreen(m.mid, 'bw-lms');
        case 13: return spanBlue(m.odd, 'bw-oe') + ' + ' + spanGreen(m.small, 'bw-lms');
        case 14: return spanRed(m.even, 'bw-oe') + ' + ' + spanGreen(m.big, 'bw-lms');
        case 15: return spanRed(m.even, 'bw-oe') + ' + ' + spanGreen(m.mid, 'bw-lms');
        case 16: return spanRed(m.even, 'bw-oe') + ' + ' + spanGreen(m.small, 'bw-lms');
        case 17: return spanBlue(m.pb + ' ' + m.odd, 'bw-pb-oe');
        case 18: return spanRed(m.pb + ' ' + m.even, 'bw-pb-oe');
        case 19: return spanUnOverBlue(m.pb + ' ' + m.under, 'bw-pb-uo');
        case 20: return spanUnOverRed(m.pb + ' ' + m.over, 'bw-pb-uo');
        case 21: return spanBlue(m.power + ' ' + m.odd, 'bw-pw-oe') + ' + ' + spanUnOverBlue(m.under, 'bw-uo');
        case 22: return spanRed(m.power + ' ' + m.even, 'bw-pw-oe') + ' + ' + spanUnOverBlue(m.under, 'bw-uo');
        case 23: return spanBlue(m.power + ' ' + m.odd, 'bw-pw-oe') + ' + ' + spanUnOverRed(m.over, 'bw-uo');
        case 24: return spanRed(m.power + ' ' + m.even, 'bw-pw-oe') + ' + ' + spanUnOverRed(m.over, 'bw-uo');
        case 25: return spanBlue(m.power + ' ' + m.odd, 'bw-pw-oe') + ' + ' + spanBlue(m.normal + ' ' + m.odd, 'bw-nm-oe');
        case 26: return spanBlue(m.power + ' ' + m.odd, 'bw-pw-oe') + ' + ' + spanRed(m.normal + ' ' + m.even, 'bw-nm-oe');
        case 27: return spanRed(m.power + ' ' + m.even, 'bw-pw-oe') + ' + ' + spanBlue(m.normal + ' ' + m.odd, 'bw-nm-oe');
        case 28: return spanRed(m.power + ' ' + m.even, 'bw-pw-oe') + ' + ' + spanRed(m.normal + ' ' + m.even, 'bw-nm-oe');
        case 29: return spanUnOverBlue(m.power + ' ' + m.under, 'bw-pw-uo') + ' + ' + spanUnOverBlue(m.normal + ' ' + m.under, 'bw-nm-uo');
        case 30: return spanUnOverBlue(m.power + ' ' + m.under, 'bw-pw-uo') + ' + ' + spanUnOverRed(m.normal + ' ' + m.over, 'bw-nm-uo');
        case 31: return spanUnOverRed(m.power + ' ' + m.over, 'bw-pw-uo') + ' + ' + spanUnOverBlue(m.normal + ' ' + m.under, 'bw-nm-uo');
        case 32: return spanUnOverRed(m.power + ' ' + m.over, 'bw-pw-uo') + ' + ' + spanUnOverRed(m.normal + ' ' + m.over, 'bw-nm-uo');
        case 33: return spanBlue(m.odd, 'bw-oe') + ' + ' + spanUnOverBlue(m.under, 'bw-uo') + ' + ' + spanBlue(m.pOdd, 'bw-p-oe');
        case 34: return spanBlue(m.odd, 'bw-oe') + ' + ' + spanUnOverBlue(m.under, 'bw-uo') + ' + ' + spanRed(m.pEven, 'bw-p-oe');
        case 35: return spanBlue(m.odd, 'bw-oe') + ' + ' + spanUnOverRed(m.over, 'bw-uo') + ' + ' + spanBlue(m.pOdd, 'bw-p-oe');
        case 36: return spanBlue(m.odd, 'bw-oe') + ' + ' + spanUnOverRed(m.over, 'bw-uo') + ' + ' + spanRed(m.pEven, 'bw-p-oe');
        case 37: return spanRed(m.even, 'bw-oe') + ' + ' + spanUnOverBlue(m.under, 'bw-uo') + ' + ' + spanBlue(m.pOdd, 'bw-p-oe');
        case 38: return spanRed(m.even, 'bw-oe') + ' + ' + spanUnOverBlue(m.under, 'bw-uo') + ' + ' + spanRed(m.pEven, 'bw-p-oe');
        case 39: return spanRed(m.even, 'bw-oe') + ' + ' + spanUnOverRed(m.over, 'bw-uo') + ' + ' + spanBlue(m.pOdd, 'bw-p-oe');
        case 40: return spanRed(m.even, 'bw-oe') + ' + ' + spanUnOverRed(m.over, 'bw-uo') + ' + ' + spanRed(m.pEven, 'bw-p-oe');
        case 41: return m.power + ' 0';
        case 42: return m.power + ' 1';
        case 43: return m.power + ' 2';
        case 44: return m.power + ' 3';
        case 45: return m.power + ' 4';
        case 46: return m.power + ' 5';
        case 47: return m.power + ' 6';
        case 48: return m.power + ' 7';
        case 49: return m.power + ' 8';
        case 50: return m.power + ' 9';
        default:
            return '';
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
    var m = betMarks();
    switch (mode) {
        case 1:
            if (round_result == 'P') {
                tHtml = spanBlue(m.pb + ' ' + m.odd, 'bw-pb-oe') + '&nbsp;';
            } else if (round_result == 'B') {
                tHtml = spanRed(m.pb + ' ' + m.even, 'bw-pb-oe') + '&nbsp;';
            }
            break;
        case 2:
            if (round_result == 'P') {
                tHtml = spanUnOverBlue(m.pb + ' ' + m.under, 'bw-pb-uo') + '&nbsp;';
            } else if (round_result == 'B') {
                tHtml = spanUnOverRed(m.pb + ' ' + m.over, 'bw-pb-uo') + '&nbsp;';
            }
            break;
        case 3:
            if (round_result == 'P') {
                tHtml = spanBlue(m.odd, 'bw-oe') + '&nbsp;';
            } else if (round_result == 'B') {
                tHtml = spanRed(m.even, 'bw-oe') + '&nbsp;';
            }
            break;
        case 4:
            if (round_result == 'P') {
                tHtml = spanUnOverBlue(m.under, 'bw-uo') + '&nbsp;';
            } else if (round_result == 'B') {
                tHtml = spanUnOverRed(m.over, 'bw-uo') + '&nbsp;';
            }
            break;
        case 5:
            if (round_result == 'L') {
                tHtml = spanGreen(m.big, 'bw-lms') + '&nbsp;&nbsp;';
            } else if (round_result == 'M') {
                tHtml = spanGreen(m.mid, 'bw-lms') + '&nbsp;';
            } else if (round_result == 'S') {
                tHtml = spanGreen(m.small, 'bw-lms') + '&nbsp;';
            }
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