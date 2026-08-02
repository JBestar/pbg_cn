function changeBetTime(gameId) {

    let bet_time = 0;
    if (gameId == 2) {
        bet_time = $('#inputBetTime').val();
    } else {
        bet_time = $('#inputBetTime1').val();
    }

    var objData = {
        "game": gameId,
        "bet_time": bet_time,
    };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/bettime_change',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);

            if (jResult.status == "success") {
                location.reload();
            } else if (jResult.status == "logout") {
                location.reload();
            }
        },
        error: function(request, status, error) {
            // console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }

    });

}

function changeBetLock() {
    var objData = {
        "bet_lock": $('#selectBetLock').val(),
    };

    if (objData.bet_lock == 0) {
        if (!confirm('정상운영하시겠습니까?'))
            return;
    } else {
        if (!confirm('점검을 진행하시겠습니까?'))
            return;
    }

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/maintain_change',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);

            if (jResult.status == "success") {
                location.reload();
            } else if (jResult.status == "logout") {
                location.reload();
            }
        },
        error: function(request, status, error) {
            // console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }

    });
}


function cleanDb() {
    var objData = {
        "date": $('#inputHistoryTime').val(),
    };

    if (objData.date < 1) {
        showAlert('정리일짜를 입력해주세요');
        return;
    }

    if (!confirm('디비정리를 진행하시겠습니까?'))
        return;

    var jsonData = JSON.stringify(objData);

    $('#btnClean').attr('disabled', true);

    $.ajax({
        url: '/api/clean_db',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            $('#btnClean').attr('disabled', false);

            if (jResult.status == "success") {
                showAlert('디비정리가 완료되었습니다.');
            } else if (jResult.status == "fail") {
                showAlert("조작이 실패되었습니다.");
            } else if (jResult.status == "logout") {
                location.reload();
            }
        },
        error: function(request, status, error) {
            $('#btnClean').attr('disabled', false);
            //console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }

    });
}








function setBankInfo() {
    var objData = {
        "bank_name": $('#inputBankName').val(),
        "bank_owner": $('#inputBankOwner').val(),
        "bank_num": $('#inputBankNum').val(),
    };

    if (!confirm('계좌를 변경하시겠습니까?'))
        return;

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/member_bank',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);

            if (jResult.status == "success") {
                showAlert("계좌정보가 변경되었습니다.");

            } else if (jResult.status == "logout") {
                location.reload();
            }
        },
        error: function(request, status, error) {
            // console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }

    });
}

function changeInfo() {
    var objData = {
        "pwd_cur": $('#inputCurrentPwd').val(),
        "pwd_new": $('#inputNewPwd').val(),
    };

    if (objData.pwd_cur.length < 1) {
        showAlert("현재비밀번호를 입력해주세요");
        return;
    }

    if (objData.pwd_new.length < 1) {
        showAlert("새 비밀번호를 입력해주세요");
        return;
    }

    if (objData.pwd_new != $('#inputNewPwd2').val()) {
        showAlert("새 비밀번호를 정확히 입력해주세요");
        return;
    }

    if (!confirm('비밀번호를 변경하시겠습니까?'))
        return;

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/member_pwd',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);

            if (jResult.status == "success") {
                showAlert('비밀번호가 변경되었습니다.');
            } else if (jResult.status == "fail") {
                if (jResult.code == 2) {
                    showAlert("현재비밀번호를 정확히 입력해주세요");
                }
            } else if (jResult.status == "logout") {
                location.reload();
            }
        },
        error: function(request, status, error) {
            // console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }

    });
}