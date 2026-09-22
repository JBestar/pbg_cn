$(document).ready(function() {

    reqCount();

});


function reqSearch() {
    reqCount();
}


function showPage(arrInfo) {
    let tHtml = "";
    if (arrInfo != null) {

        for (let idx in arrInfo) {
            tHtml += "<tr>";
            tHtml += "<td class=\"tdDate\">" + getExchangeTypeText(arrInfo[idx].exchange_type) + "</td>";
            tHtml += "<td class=\"tdDate\">" + parseInt(arrInfo[idx].exchange_money).toLocaleString() + "</td>";
            tHtml += "<td class=\"tdDate\">" + getMemberLevelText(arrInfo[idx].mb_level) + "</td>";
            tHtml += "<td class=\"tdDate\">" + arrInfo[idx].exchange_mb_uid + "</td>";
            tHtml += "<td class=\"tdDate\">" + arrInfo[idx].mb_nickname + "</td>";
            tHtml += "<td class=\"tdDate\">" + arrInfo[idx].exchange_bank_name + "</td>";
            tHtml += "<td class=\"tdDate\">" + arrInfo[idx].exchange_bank_owner + "</td>";
            tHtml += "<td class=\"tdDate\">" + arrInfo[idx].exchange_bank_number + "</td>";
            tHtml += "<td class=\"tdDate\">" + arrInfo[idx].exchange_time_require + "</td>";
            tHtml += "<td class=\"tdDate\">";
            if (parseInt(arrInfo[idx].exchange_action_state) > 0) {
                tHtml += arrInfo[idx].exchange_time_process;
            }
            tHtml += "</td>";
            tHtml += "<td class=\"tdDate\">" + getChargeStateText(arrInfo[idx].exchange_action_state) + "</td>";
            tHtml += "<td class=\"tdDate\">";
            if (parseInt(arrInfo[idx].exchange_action_state) > 0) {
                tHtml += "<button type=\"button\" class=\"btn_red btn_icon\" title=\"삭제\" aria-label=\"삭제\" onclick=\"deleteExchangeProc(";
                tHtml += arrInfo[idx].exchange_fid + ");\"><i class=\"fas fa-trash-alt\"></i></button>";
            } else {
                tHtml += "<button type=\"button\" class=\"btn_blue\" onclick=\"permitExchangeProc(";
                tHtml += arrInfo[idx].exchange_fid + ");\">확인</button> ";
            }
            tHtml += "</td></tr>";

        }

    }
    $('#tbodyList').html(tHtml);
}




function deleteExchangeProc(exchangeId) {
    if (!confirm('삭제하시겠습니까?'))
        return;

    var objData = {
        "exchange_id": exchangeId
    };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/exchangeproc_delete',
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


function permitExchangeProc(exchangeId) {

    var objData = {
        "exchange_id": exchangeId
    };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/exchangeproc_permit',
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
    var objData = {
        "start": $('#inputDateS').val(),
        "end": $('#inputDateE').val(),
        "mb_uid": $('#inputSubId').val()
    };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/exchangeproc_count',
        type: 'post',
        data: { json_: jsonData },
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
        "start": $('#inputDateS').val(),
        "end": $('#inputDateE').val(),
        "mb_uid": $('#inputSubId').val(),
        "page": getActivePage(),
        "cntper": CountPerPage
    };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/exchangeproc_page',
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