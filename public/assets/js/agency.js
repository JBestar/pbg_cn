$(document).ready(function() {
    $('#inputSubId').val('');
    reqPage();

});

function reqSearch() {
    reqPage();
}

function reqLoopRound() {

    reqPage();
}

function reqLoopSec() {

    reqBetUser();
}

function showPage(arrInfo) {
    let tHtml = "";
    let totalMember = 0;
    if (arrInfo != null) {
        let profit_bet = 0,
            profit_charge = 0;
        totalMember = arrInfo.length;
        for (let idx in arrInfo) {
            if (arrInfo[idx].mb_emp_nickname == null)
                continue;

            if (arrInfo[idx].mb_color.length > 0) {
                tHtml += "<tr style=\"background-color:" + arrInfo[idx].mb_color + "\" id=\"trFid-";
            } else tHtml += "<tr id=\"trFid-";
            tHtml += arrInfo[idx].mb_fid + "\">";

            tHtml += "<td class=\"tdDate\" >" + (parseInt(idx) + 1) + "</td>";
            tHtml += "<td class=\"tdDate\">" + getMemberLevelText(arrInfo[idx].mb_level) + "</td>";
            tHtml += "<td class=\"tdDate\" >" + arrInfo[idx].mb_emp_nickname + "</td>";
            tHtml += "<td class=\"tdDate\" >" + arrInfo[idx].mb_uid + "</td>";
            tHtml += "<td class=\"tdDate\" >" + arrInfo[idx].mb_nickname + "</td>";
            tHtml += "<td class=\"tdMoney\" >" + parseInt(arrInfo[idx].mb_money).toLocaleString() + "</td>";
            tHtml += "<td class=\"tdMoney\" >" + parseInt(arrInfo[idx].mb_point).toLocaleString() + "</td>";
            //배팅
            if (arrInfo[idx].bet_sum != null) {
                tHtml += "<td class=\"tdMoney\" >" + parseInt(arrInfo[idx].bet_sum).toLocaleString() + "</td>";
                tHtml += "<td class=\"tdMoney\" >" + parseInt(arrInfo[idx].bet_win_sum).toLocaleString() + "</td>";
                tHtml += "<td class=\"tdMoney\" >" + parseInt(arrInfo[idx].bet_empl_sum).toLocaleString() + "</td>";
                profit_bet = parseInt(arrInfo[idx].bet_sum) - parseInt(arrInfo[idx].bet_win_sum) - parseInt(arrInfo[idx].bet_empl_sum) - parseInt(arrInfo[idx].bet_agen_sum);
                tHtml += "<td class=\"tdMoney\" >";
                if (profit_bet >= 0) {
                    tHtml += "<font color=\"#0000fe\">";
                } else {
                    tHtml += "<font color=\"#fe0000\">";
                }
                tHtml += profit_bet.toLocaleString();
                tHtml += "</font></td>"

            } else {
                tHtml += "<td class=\"tdMoney\" >0</td>";
                tHtml += "<td class=\"tdMoney\" >0</td>";
                tHtml += "<td class=\"tdMoney\" >0</td>";
                tHtml += "<td class=\"tdMoney\" style=\"color:#0000fe;\">0</td>";
            }
            //충환전
            profit_charge = 0;
            if (arrInfo[idx].charge_default_sum != null) {
                tHtml += "<td class=\"tdMoney\" >" + parseInt(arrInfo[idx].charge_default_sum).toLocaleString() + "</td>";
                profit_charge += parseInt(arrInfo[idx].charge_default_sum);
            } else {
                tHtml += "<td class=\"tdMoney\" >0</td>";
            }

            if (arrInfo[idx].exchange_default_sum != null) {
                tHtml += "<td class=\"tdMoney\" >" + parseInt(arrInfo[idx].exchange_default_sum).toLocaleString() + "</td>";
                profit_charge -= parseInt(arrInfo[idx].exchange_default_sum);
            } else {
                tHtml += "<td class=\"tdMoney\" >0</td>";
            }
            tHtml += "<td class=\"tdMoney\" >";
            if (profit_charge >= 0) {
                tHtml += "<font color=\"#0000fe\">";
            } else {
                tHtml += "<font color=\"#fe0000\">";
            }
            tHtml += profit_charge.toLocaleString();
            tHtml += "</font></td>"

            //알 충환전
            if (arrInfo[idx].charge_present_sum != null) {
                tHtml += "<td class=\"tdMoney\" >" + parseInt(arrInfo[idx].charge_present_sum).toLocaleString() + "</td>";
                profit_charge += parseInt(arrInfo[idx].charge_present_sum);
            } else {
                tHtml += "<td class=\"tdMoney\" >0</td>";
            }

            if (arrInfo[idx].exchange_present_sum != null) {
                tHtml += "<td class=\"tdMoney\" >" + parseInt(arrInfo[idx].exchange_present_sum).toLocaleString() + "</td>";
                profit_charge -= parseInt(arrInfo[idx].exchange_present_sum);
            } else {
                tHtml += "<td class=\"tdMoney\" >0</td>";
            }
            tHtml += "<td class=\"tdDate\" >" + arrInfo[idx].mb_game_pb_ratio + " %</td>";
            tHtml += "<td class=\"tdMoney\" >" + parseInt(arrInfo[idx].mb_limit_round).toLocaleString() + "</td>";
            tHtml += "<td class=\"tdMoney\" >" + parseInt(arrInfo[idx].mb_limit_single).toLocaleString() + "<br>" +
                parseInt(arrInfo[idx].mb_limit_digit).toLocaleString() + "</td>";
            tHtml += "<td class=\"tdMoney\" >" + parseInt(arrInfo[idx].mb_limit_mix).toLocaleString() + "<br>" +
                parseInt(arrInfo[idx].mb_limit_three).toLocaleString() + "</td>";
            tHtml += "<td class=\"tdDate\">";
            tHtml += "<button type=\"button\" class=\"btn_blue\" onclick=\"showServiceCharge('";
            tHtml += arrInfo[idx].mb_uid + "', '" + arrInfo[idx].mb_nickname + "', '" + arrInfo[idx].mb_money + "');\">충전</button> ";
            tHtml += "<button type=\"button\" class=\"btn_red\" onclick=\"showServiceExchange('";
            tHtml += arrInfo[idx].mb_uid + "', '" + arrInfo[idx].mb_nickname + "', '" + arrInfo[idx].mb_money + "');\">회수</button>";

            tHtml += "</td><td class=\"tdDate\">";
            tHtml += "<button type=\"button\" class=\"btn_blue\" onclick=\"fetchEditMember(";
            tHtml += arrInfo[idx].mb_fid + ");\">수정</button> ";
            if (arrInfo[idx].mb_state_active == 1) {
                tHtml += "<button type=\"button\" class=\"btn_blue\" onclick=\"reqPermitMember(";
                tHtml += arrInfo[idx].mb_fid + ", 0);\">승인</button>";
            } else {
                tHtml += "<button type=\"button\" class=\"btn_red\" onclick=\"reqPermitMember(";
                tHtml += arrInfo[idx].mb_fid + ", 1);\">차단</button>";

            }
            tHtml += "</td><td class=\"tdDate\">";
            tHtml += "<button type=\"button\" class=\"btn_red\" onclick=\"reqDeleteMember(";
            tHtml += arrInfo[idx].mb_fid + ");\">삭제</button> ";
            tHtml += "</td></tr>";
        }

        if (totalMember > 0)
            reqBetUser();

    }

    $('#divSearchResult').text('Total ' + totalMember);
    $('#tbodyList').html(tHtml);
}

function showBetUser(arrInfo) {
    $('#tbodyList tr').removeClass('spanYellow');

    if (arrInfo != null) {

        for (let idx in arrInfo) {
            $('#trFid-' + arrInfo[idx].mb_fid).addClass('spanYellow');
        }
    }
}

function reqPermitMember(mb_fid, permit) {

    var objData = {
        "mb_fid": mb_fid,
        "permit": permit
    };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/member_permit',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);

            if (jResult.status == "success") {
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



function reqDeleteMember(mb_fid) {
    if (!confirm('삭제하시겠습니까?'))
        return;

    var objData = {
        "mb_fid": mb_fid
    };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/member_delete',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);

            if (jResult.status == "success") {
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
        "start": $('#inputDateS').val(),
        "end": $('#inputDateE').val(),
        "mb_uid": $('#inputSubId').val()
    };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/member_list',
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

function reqBetUser() {
    $.ajax({
        url: '/api/member_betlist',
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);
            if (jResult.status == "success") {
                showBetUser(jResult.data);
            } else if (jResult.status == "logout") {
                location.reload();
            }
        },
        error: function(request, status, error) {
            // console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }

    });
}



/////-------------------------------------------------

function showRegMember() {
    initRegMember();
    $('#divRegSub').show();
}

function closeRegMember() {
    $('#divRegSub').hide();
}

function initRegMember() {
    $('#regSubId').val('');
    $('#regSubName').val('');
    $('#regSubPwd').val('');
    $('#regSubExcPwd').val('');
    $('#regSubPhone').val('');
    $('#regSubBank').val('');
    $('#regSubBankNum').val('');
    $('#regSubBankOwner').val('');
    $('#regSubSingleDealRate').val('');

}


function reqRegMember() {
    var objData = {
        "uid": $('#regSubId').val(),
        "nickname": $('#regSubName').val(),
        "pwd": $('#regSubPwd').val(),
        "bank_pwd": $('#regSubExcPwd').val(),
        "phone": $('#regSubPhone').val(),
        "bank_name": $('#regSubBank').val(),
        "bank_num": $('#regSubBankNum').val(),
        "bank_owner": $('#regSubBankOwner').val(),
        "game_ratio": $('#regSubSingleDealRate').val(),

    };

    if (objData.uid.length < 1 || objData.nickname.length < 1 ||
        objData.pwd.length < 1 || objData.bank_pwd.length < 1) {
        showAlert('아이디, 이름, 비밀번호, 출금 비밀번호는 필수정보입니다.')
        return;
    }


    var jsonData = JSON.stringify(objData);

    if (!confirm("등록하시겠습니까?"))
        return;

    $.ajax({
        url: '/api/member_register',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);

            if (jResult.status == "success") {
                closeRegMember();
                showAlert('등록되었습니다.');
                reqPage();
            } else if (jResult.status == "fail") {
                if (jResult.code == 6)
                    showAlert('중복된 아이디입니다.');
                else if (jResult.code == 7)
                    showAlert('중복된 이름입니다.');
                else if (jResult.code == 10)
                    showAlert('수수료가 총판수수료보다 높게 설정되었습니다.');
                else showAlert('등록이 실패되었습니다.');
            } else if (jResult.status == "logout") {
                location.reload();
            }
        },
        error: function(request, status, error) {
            // console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }

    });
}

//////--------------------------------------------


function showEditMember(objInfo) {

    if (objInfo != null) {
        $('#editSubId').text(objInfo.mb_uid);
        $('#editSubName').text(objInfo.mb_nickname);
        $('#editSubPwd').val(objInfo.mb_pwd);
        $('#editSubExcPwd').val(objInfo.mb_bank_pwd);
        $('#editSubPhone').val(objInfo.mb_phone);
        $('#editSubBank').val(objInfo.mb_bank_name);
        $('#editSubBankNum').val(objInfo.mb_bank_num);
        $('#editSubBankOwner').val(objInfo.mb_bank_owner);

        $('#editSubTotalMaxBet').val(objInfo.mb_limit_round);
        $('#editSubSingleMaxBet').val(objInfo.mb_limit_single);
        $('#editSubMultiMaxBet').val(objInfo.mb_limit_mix);
        $('#editSubThreeMaxBet').val(objInfo.mb_limit_three);
        $('#editSubDigitMaxBet').val(objInfo.mb_limit_digit);

        $('#editSubSingleDealRate').val(objInfo.mb_game_pb_ratio);

        $('#divEditSub').show();

    } else {
        $('#editSubId').text('');
        $('#editSubName').text('');
        $('#editSubPwd').val('');
        $('#editSubExcPwd').val('');
    }

}

function closeEditMember() {
    $('#divEditSub').hide();
}


function fetchEditMember(mb_fid) {

    var objData = { "fid": mb_fid };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/member_fetch',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);
            if (jResult.status == "success") {
                showEditMember(jResult.data);
            } else if (jResult.status == "logout") {
                location.reload();
            }
        },
        error: function(request, status, error) {
            // console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }

    });

}

function reqEditMember() {
    var objData = {
        "uid": $('#editSubId').text(),
        "nickname": $('#editSubName').text(),
        "pwd": $('#editSubPwd').val(),
        "bank_pwd": $('#editSubExcPwd').val(),
        "phone": $('#editSubPhone').val(),
        "bank_name": $('#editSubBank').val(),
        "bank_num": $('#editSubBankNum').val(),
        "bank_owner": $('#editSubBankOwner').val(),
        "limit_round": $('#editSubTotalMaxBet').val(),
        "limit_single": $('#editSubSingleMaxBet').val(),
        "limit_mix": $('#editSubMultiMaxBet').val(),
        "limit_three": $('#editSubThreeMaxBet').val(),
        "limit_digit": $('#editSubDigitMaxBet').val(),
        "game_ratio": $('#editSubSingleDealRate').val(),

    };

    if (objData.uid.length < 1 || objData.nickname.length < 1 ||
        objData.pwd.length < 1 || objData.bank_pwd.length < 1) {
        showAlert('아이디, 이름, 비밀번호, 출금 비밀번호는 필수정보입니다.')
        return;
    }


    var jsonData = JSON.stringify(objData);

    if (!confirm("변경하시겠습니까?"))
        return;

    $.ajax({
        url: '/api/member_modify',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);
            if (jResult.status == "success") {
                closeEditMember();
                showAlert('매장이 변경되었습니다.');
                reqPage();
            } else if (jResult.status == "fail") {
                if (jResult.code == 10)
                    showAlert('수수료가 총판수수료보다 높게 설정되었습니다.');
                else showAlert('변경이 실패되었습니다.');
            } else if (jResult.status == "logout") {
                location.reload();
            }
        },
        error: function(request, status, error) {
            // console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }

    });
}


//////--------------------------------------------


function showServiceCharge(mb_uid, mb_nickname, mb_money) {

    if (mUser == null)
        return;

    serviceChargeMoney(0);
    $('#serviceChargeSenderId').text(mUser.mb_uid + " (" + mUser.mb_nickname + ")");
    $('#serviceChargeSenderMoney').text(parseInt(mUser.mb_money).toLocaleString());

    $('#serviceChargeRecverUid').val(mb_uid);
    $('#serviceChargeRecverId').text(mb_uid + " (" + mb_nickname + ")");
    $('#serviceChargeRecverMoney').text(parseInt(mb_money).toLocaleString());

    $('#divServiceCharge').show();
}

function closeServiceCharge() {
    $('#divServiceCharge').hide();
}

function serviceChargeMoney(nMoney) {
    let nCurMoney = $('#serviceChargeMoney').val();
    nCurMoney = parseInt(nCurMoney);

    if (nMoney > 0) {
        nCurMoney += nMoney;
    } else {
        nCurMoney = 0;
    }

    $('#serviceChargeMoney').val(nCurMoney);
}

function reqServiceCharge() {
    let nCurMoney = $('#serviceChargeMoney').val();
    var objData = {
        "uid": $('#serviceChargeRecverUid').val(),
        "money": parseInt(nCurMoney),
    };

    if (objData.money < 1) {
        showAlert('충전알을 입력해주세요');
        return;
    }

    var jsonData = JSON.stringify(objData);

    if (!confirm("충전하시겠습니까?"))
        return;

    $.ajax({
        url: '/api/charge_service',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);

            if (jResult.status == "success") {
                closeServiceCharge();
                showAlert('충전되었습니다.');
                reqPage();
                setTimeout(function() { reqAssets(); }, 1000);
            } else if (jResult.status == "fail") {
                if (jResult.code == 9)
                    showAlert('보내는 사람의 보유알이 부족합니다.');
                else showAlert('충전이 실패되었습니다.');
            } else if (jResult.status == "logout") {
                location.reload();
            }
        },
        error: function(request, status, error) {
            // console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }

    });
}


//////--------------------------------------------


function showServiceExchange(mb_uid, mb_nickname, mb_money) {
    if (mUser == null)
        return;

    serviceExchangeMoney(0);
    $('#serviceExchangeRecverId').text(mUser.mb_uid + " (" + mUser.mb_nickname + ")");
    $('#serviceExchangeRecverMoney').text(parseInt(mUser.mb_money).toLocaleString());

    $('#serviceExchangeSenderUid').val(mb_uid);
    $('#serviceExchangeSenderId').text(mb_nickname);
    $('#serviceExchangeSenderMoney').text(parseInt(mb_money).toLocaleString());

    $('#divServiceExchange').show();
}

function closeServiceExchange() {
    $('#divServiceExchange').hide();
}

function serviceExchangeMoney(nMoney) {
    let nCurMonny = $('#serviceExchangeMoney').val();
    nCurMonny = parseInt(nCurMonny);

    if (nMoney > 0) {
        nCurMonny += nMoney;
    } else {
        nCurMonny = 0;
    }

    $('#serviceExchangeMoney').val(nCurMonny);
}

function reqServiceExchange() {
    let nCurMonny = $('#serviceExchangeMoney').val();

    var objData = {
        "uid": $('#serviceExchangeSenderUid').val(),
        "money": parseInt(nCurMonny),
    };

    if (objData.money < 1) {
        showAlert('회수알을 입력해주세요');
        return;
    }

    var jsonData = JSON.stringify(objData);

    if (!confirm("회수하시겠습니까?"))
        return;

    $.ajax({
        url: '/api/exchange_service',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);

            if (jResult.status == "success") {
                closeServiceExchange();
                showAlert('회수되었습니다.');
                reqPage();
                setTimeout(function() { reqAssets(); }, 1000);
            } else if (jResult.status == "fail") {
                if (jResult.code == 9)
                    showAlert('보내는 사람의 보유알이 부족합니다.');
                else showAlert('회수가 실패되었습니다.');
            } else if (jResult.status == "logout") {
                location.reload();
            }
        },
        error: function(request, status, error) {
            // console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }

    });
}


///////////--------------------------------------

function showEditSubRate() {
    $('#divEditSubRate').show();
}


function closeEditSubRate() {
    $('#divEditSubRate').hide();
}

function reqEditSubRate() {

}