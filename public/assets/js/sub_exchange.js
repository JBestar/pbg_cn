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
            tHtml += "<td class=\"tdDate\" style=\"height:40px;\">" + parseInt(arrInfo[idx].exchange_money).toLocaleString() + "</td>";
            tHtml += "<td class=\"tdDate\">" + arrInfo[idx].exchange_time_require + "</td>";
            tHtml += "<td class=\"tdDate\">";
            if (parseInt(arrInfo[idx].exchange_action_state) > 0)
                tHtml += arrInfo[idx].exchange_time_process;
            tHtml += "</td>";
            tHtml += "<td class=\"tdDate\">" + getChargeStateText(arrInfo[idx].exchange_action_state) + "</td>";

            tHtml += "<td class=\"tdDate\">";
            if (parseInt(arrInfo[idx].exchange_action_state) > 0) {
                tHtml += "<button type=\"button\" class=\"btn_red btn_icon\" title=\"삭제\" aria-label=\"삭제\" onclick=\"deleteExchange(";
                tHtml += arrInfo[idx].exchange_fid + ");\"><i class=\"fas fa-trash-alt\"></i></button>";
            }
            tHtml += "</td></tr>";

        }

    }
    $('#tbodyList').html(tHtml);
}



function reqExchange() {
    if (mMoney < 1) {
        showAlert('요청금액을 입력해주세요')
        return;
    }

    if (!confirm('환전신청 하시겠습니까?'))
        return;
    var objData = {
        "amount": mMoney,
        "bank_name": $('#bank_name').val(),
        "bank_number": $('#bank_number').val(),
        "bank_owner": $('#bank_owner').val(),
        "bank_pwd": $('#bank_pwd').val()
    };

    var jsonData = JSON.stringify(objData);
    // console.log(jsonData);
    $.ajax({
        url: '/api/exchange_req',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);

            if (jResult.status == "success") {
                initMoney();
                reqCount();
                setTimeout(function() { reqAssets(); }, 500);
            } else if (jResult.status == "fail") {
                if (jResult.code == 3)
                    showAlert('신청 대기중입니다.');
                else if (jResult.code == 4)
                    showAlert('출금비번이 틀림니다. 다시 확인해주세요.');
                else if (jResult.code == 9)
                    showAlert('보유머니를 초과하셧습니다.');
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

function deleteExchange(exchangeId) {
    if (!confirm('삭제하시겠습니까?'))
        return;

    var objData = {
        "exchange_id": exchangeId
    };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/exchange_delete',
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
        url: '/api/exchange_count',
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
        url: '/api/exchange_page',
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