var mMoney;

$(document).ready(function() {
    initMoney();
    reqCount();

});

function initMoney() {
    mMoney = 0;
    onMoneyClick(mMoney);
}

function onMoneyClick(nMoney) {
    if (nMoney > 0)
        mMoney += nMoney;
    else mMoney = 0;

    $('#inputMoney').val(mMoney.toLocaleString());
}


function askBankInfo() {

}



function showPage(arrInfo) {
    let tHtml = "";
    if (arrInfo != null) {

        for (let idx in arrInfo) {
            tHtml += "<tr>";
            tHtml += "<td class=\"tdDate\" style=\"height:40px;\">" + arrInfo[idx].charge_mb_name + "</td>";
            tHtml += "<td class=\"tdDate\" >" + parseInt(arrInfo[idx].charge_money).toLocaleString() + "</td>";
            tHtml += "<td class=\"tdDate\">" + arrInfo[idx].charge_time_require + "</td>";
            tHtml += "<td class=\"tdDate\">";
            if (parseInt(arrInfo[idx].charge_action_state) > 0)
                tHtml += arrInfo[idx].charge_time_process;
            tHtml += "</td>";
            tHtml += "<td class=\"tdDate\">" + getChargeStateText(arrInfo[idx].charge_action_state) + "</td>";

            tHtml += "<td class=\"tdDate\">";
            if (parseInt(arrInfo[idx].charge_action_state) > 0) {
                tHtml += "<button type=\"button\" class=\"btn_red\" onclick=\"deleteCharge(";
                tHtml += arrInfo[idx].charge_fid + ");\">삭제</button>";
            }
            tHtml += "</td></tr>";

        }

    }
    $('#tbodyList').html(tHtml);
}



function reqCharge() {
    if (mMoney < 1) {
        showAlert('요청금액을 입력해주세요')
        return;
    }

    if (!confirm('충전신청 하시겠습니까?'))
        return;
    var objData = {
        "amount": mMoney,
        "name": $('#bank_member').val()
    };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/charge_req',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);

            if (jResult.status == "success") {
                initMoney();
                reqCount();
            } else if (jResult.status == "fail") {
                if (jResult.code == 3)
                    showAlert('신청 대기중입니다.');
                else showAlert('신청이 거절되었습니다.');
            } else if (jResult.status == "logout") {
                location.reload();
            }
        },
        error: function(request, status, error) {
            // console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }

    });
}

function deleteCharge(chargeId) {
    if (!confirm('삭제하시겠습니까?'))
        return;

    var objData = {
        "charge_id": chargeId
    };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/charge_delete',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);

            if (jResult.status == "success") {
                reqCount();
            } else if (jResult.status == "logout") {
                location.reload();
            }
        },
        error: function(request, status, error) {
            // console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }

    });
}



function reqCount() {

    $.ajax({
        url: '/api/charge_count',
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);

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
        "page": getActivePage(),
        "cntper": CountPerPage
    };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/charge_page',
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