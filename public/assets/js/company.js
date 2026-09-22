$(document).ready(function() {
    $('#inputSubId').val('');
    reqPage();

});

function t(key, fallback) {
    return (window.ADMIN_I18N && window.ADMIN_I18N[key]) ? window.ADMIN_I18N[key] : fallback;
}

function reqSearch() {
    reqPage();
}

function reqLoopRound() {

    reqPage();
}

function displayPwd(p) {
    p = String(p == null ? '' : p);
    if (!p) return '';
    if (p.indexOf('$2y$') === 0 || p.indexOf('$2a$') === 0 || p.indexOf('$argon') === 0) return '—';
    return p;
}

function showPage(arrInfo) {
    let tHtml = "";
    let totalMember = 0;
    if (arrInfo != null) {
        totalMember = arrInfo.length;
        for (let idx in arrInfo) {
            var r = arrInfo[idx];
            if (r.mb_color && r.mb_color.length > 0) {
                tHtml += "<tr style=\"background-color:" + r.mb_color + "\">";
            } else {
                tHtml += "<tr>";
            }
            tHtml += "<td class=\"tdDate\">" + (parseInt(idx, 10) + 1) + "</td>";
            tHtml += "<td class=\"tdDate\">" + getMemberLevelText(r.mb_level) + "</td>";
            tHtml += "<td class=\"tdDate\">" + r.mb_uid + "</td>";
            tHtml += "<td class=\"tdDate\">" + r.mb_nickname + "</td>";
            tHtml += "<td class=\"tdDate\">" + displayPwd(r.mb_pwd) + "</td>";
            tHtml += "<td class=\"tdMoney\">" + (parseInt(r.mb_money, 10) || 0).toLocaleString() + "</td>";
            tHtml += "<td class=\"tdMoney\">" + fmtPoint(r.mb_point) + "</td>";
            tHtml += "<td class=\"tdDate\">" + (r.mb_game_pb_ratio || 0) + " %</td>";
            tHtml += "<td class=\"tdDate\">";
            tHtml += "<button type=\"button\" class=\"btn_blue\" onclick=\"fetchSubMember('" + r.mb_fid + "');\">";
            tHtml += (r.mb_user_count != null ? parseInt(r.mb_user_count, 10) : 0).toLocaleString();
            tHtml += "</button></td>";

            tHtml += "<td class=\"tdDate\">";
            tHtml += "<button type=\"button\" class=\"btn_blue btn_chip\" onclick=\"showServiceCharge('";
            tHtml += r.mb_uid + "', '" + r.mb_nickname + "', '" + r.mb_money + "');\">"
                + t('btn_egg_charge', '충전') + "</button> ";
            tHtml += "<button type=\"button\" class=\"btn_red btn_chip\" onclick=\"showServiceExchange('";
            tHtml += r.mb_uid + "', '" + r.mb_nickname + "', '" + r.mb_money + "');\">"
                + t('btn_egg_recover', '회수') + "</button>";
            tHtml += "</td>";

            tHtml += "<td class=\"tdDate\">";
            tHtml += "<button type=\"button\" class=\"btn_blue btn_icon\" title=\"" + t('btn_edit', '수정') + "\" aria-label=\"" + t('btn_edit', '수정') + "\" onclick=\"fetchEditMember(" + r.mb_fid + ");\">"
                + "<i class=\"fas fa-pencil-alt\"></i></button> ";
            if (parseInt(r.mb_state_active, 10) == 1) {
                tHtml += "<button type=\"button\" class=\"btn_blue btn_chip\" onclick=\"reqPermitMember(" + r.mb_fid + ", 0);\">"
                    + t('btn_approve', '승인') + "</button>";
            } else {
                tHtml += "<button type=\"button\" class=\"btn_red btn_chip\" onclick=\"reqPermitMember(" + r.mb_fid + ", 1);\">"
                    + t('btn_block', '차단') + "</button>";
            }
            tHtml += "</td>";

            tHtml += "<td class=\"tdDate\">";
            tHtml += "<button type=\"button\" class=\"btn_red btn_icon\" title=\"" + t('btn_delete', '삭제') + "\" aria-label=\"" + t('btn_delete', '삭제') + "\" onclick=\"reqDeleteMember(" + r.mb_fid + ");\">"
                + "<i class=\"fas fa-trash-alt\"></i></button>";
            tHtml += "</td>";

            tHtml += "<td class=\"tdDate\">";
            if (parseInt(r.is_online, 10) === 1) {
                tHtml += "<span class=\"online-blink\">" + t('status_online', '접속중') + "</span>";
            }
            tHtml += "</td>";
            tHtml += "<td class=\"tdDate\">" + (r.mb_time_join || "") + "</td>";
            tHtml += "</tr>";
        }
    }

    $('#tbodyList').html(tHtml);
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


function reqRestMember(mb_fid, permit) {

    var objData = {
        "mb_fid": mb_fid,
        "rest": permit
    };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/member_rest',
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
                showAlert('변경되었습니다.');
                reqPage();
            } else if (jResult.status == "fail") {
                showAlert('변경이 실패되었습니다.');
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


function showServiceCharge(mb_uid, mb_nickname, mb_money, emp_fid = '') {

    if (mUser == null)
        return;

    serviceChargeMoney(0);
    $('#serviceChargeSenderId').text(mUser.mb_uid + " (" + mUser.mb_nickname + ")");
    $('#serviceChargeSenderMoney').text('');

    $('#serviceChargeRecverEmpid').val(emp_fid);
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
                if ($('#serviceChargeRecverEmpid').val().length > 0) {
                    fetchSubMember($('#serviceChargeRecverEmpid').val());
                } else
                    reqPage();
                setTimeout(function() { reqAssets(); }, 500);
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



function showServiceExchange(mb_uid, mb_nickname, mb_money, emp_fid = '') {
    if (mUser == null)
        return;

    serviceExchangeMoney(0);
    $('#serviceExchangeRecverId').text(mUser.mb_uid + " (" + mUser.mb_nickname + ")");
    $('#serviceExchangeRecverMoney').text('');

    $('#serviceExchangeSenderUid').val(mb_uid);
    $("#serviceExchangeSenderEmpid").val(emp_fid);
    $('#serviceExchangeSenderId').text(mb_uid + " (" + mb_nickname + ")");
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
                if ($('#serviceExchangeSenderEmpid').val().length > 0) {
                    fetchSubMember($('#serviceExchangeSenderEmpid').val());
                } else
                    reqPage();
                setTimeout(function() { reqAssets(); }, 500);
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

///////////-------------------------------------


function fetchSubMember(mb_fid) {

    var objData = { "fid": mb_fid };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/submember_fetch',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);
            if (jResult.status == "success") {
                showSubMember(jResult.data, jResult.info);
            } else if (jResult.status == "logout") {
                location.reload();
            }
        },
        error: function(request, status, error) {
            // console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }

    });

}


function showSubMember(arrInfo, objEmp) {


    let tHtml = "";
    let totalMember = 0;
    if (arrInfo != null) {

        totalMember = arrInfo.length;
        for (let idx in arrInfo) {

            tHtml += "<tr>";
            tHtml += "<td class=\"tdDate\" >" + (parseInt(idx) + 1) + "</td>";
            tHtml += "<td class=\"tdDate\">" + getMemberLevelText(arrInfo[idx].mb_level) + "</td>";
            tHtml += "<td class=\"tdDate\" >" + arrInfo[idx].mb_uid + "</td>";
            tHtml += "<td class=\"tdDate\" >" + arrInfo[idx].mb_nickname + "</td>";
            tHtml += "<td class=\"tdMoney\" >" + parseInt(arrInfo[idx].mb_money).toLocaleString() + "</td>";
            tHtml += "<td class=\"tdMoney\" >" + fmtPoint(arrInfo[idx].mb_point) + "</td>";

            tHtml += "<td class=\"tdDate\">";
            tHtml += "<button type=\"button\" class=\"btn_blue btn_chip\" onclick=\"showServiceCharge('";
            tHtml += arrInfo[idx].mb_uid + "', '" + arrInfo[idx].mb_nickname + "', '" + arrInfo[idx].mb_money;
            tHtml += "', '" + objEmp.mb_fid + "');\">" + t('btn_egg_charge', '충전') + "</button> ";

            tHtml += "<button type=\"button\" class=\"btn_red btn_chip\" onclick=\"showServiceExchange('";
            tHtml += arrInfo[idx].mb_uid + "', '" + arrInfo[idx].mb_nickname + "', '" + arrInfo[idx].mb_money
            tHtml += "', '" + objEmp.mb_fid + "');\">" + t('btn_egg_recover', '회수') + "</button>";
            tHtml += "</td></tr>";
        }
    }

    if (objEmp != null) {
        $('#subTitleId').text(objEmp.mb_nickname + " 하부매장");
    }

    $('#tSubbodyList').html(tHtml);



    $('#divSubMember').show();
}

function closeSubMember() {
    $('#divSubMember').hide();
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