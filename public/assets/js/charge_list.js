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
            tHtml += "<td class=\"tdDate\">" + getChargeTypeText(arrInfo[idx].charge_type) + "</td>";
            tHtml += "<td class=\"tdDate\" >" + parseInt(arrInfo[idx].charge_money).toLocaleString() + "</td>";
            tHtml += "<td class=\"tdDate\">" + getMemberLevelText(arrInfo[idx].mb_level) + "</td>";
            tHtml += "<td class=\"tdDate\" >" + arrInfo[idx].charge_mb_uid + "</td>";
            tHtml += "<td class=\"tdDate\" >" + arrInfo[idx].mb_nickname + "</td>";
            tHtml += "<td class=\"tdDate\" >" + arrInfo[idx].charge_mb_name + "</td>";
            tHtml += "<td class=\"tdDate\">" + arrInfo[idx].charge_time_require + "</td>";
            tHtml += "<td class=\"tdDate\">";
            if (parseInt(arrInfo[idx].charge_action_state) > 0)
                tHtml += arrInfo[idx].charge_time_process;
            tHtml += "</td>";
            tHtml += "<td class=\"tdDate\">" + getChargeStateText(arrInfo[idx].charge_action_state) + "</td>";

            tHtml += "<td class=\"tdDate\">";
            if (parseInt(arrInfo[idx].charge_action_state) > 0) {
                tHtml += "<button type=\"button\" class=\"btn_red btn_icon\" title=\"삭제\" aria-label=\"삭제\" onclick=\"deleteChargeProc(";
                tHtml += arrInfo[idx].charge_fid + ");\"><i class=\"fas fa-trash-alt\"></i></button>";
            } else {
                tHtml += "<button type=\"button\" class=\"btn_blue\" onclick=\"permitChargeProc(";
                tHtml += arrInfo[idx].charge_fid + ");\">확인</button> ";
                tHtml += "<button type=\"button\" class=\"btn_red\" onclick=\"refuseChargeProc(";
                tHtml += arrInfo[idx].charge_fid + ");\">취소</button>";
            }
            tHtml += "</td></tr>";

        }

    }
    $('#tbodyList').html(tHtml);
}


function deleteChargeProc(chargeId) {
    if (!confirm('삭제하시겠습니까?'))
        return;

    var objData = {
        "charge_id": chargeId
    };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/chargeproc_delete',
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

function permitChargeProc(chargeId) {
    if (!confirm('승인하시겠습니까?'))
        return;

    var objData = {
        "charge_id": chargeId
    };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/chargeproc_permit',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);

            if (jResult.status == "success") {
                reqCount();
                setTimeout(function() { reqAssets(); }, 500);
            } else if (jResult.status == "logout") {
                location.reload();
            } else if (jResult.status == "fail") {
                if (jResult.code == 9)
                    showAlert('보유머니가 부족합니다.');
                else showAlert('충전처리가 실패되었습니다.');
            }
        },
        error: function(request, status, error) {
            // console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }

    });
}

function refuseChargeProc(chargeId) {
    if (!confirm('취소하시겠습니까?'))
        return;

    var objData = {
        "charge_id": chargeId
    };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/chargeproc_cancel',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);

            if (jResult.status == "success") {
                reqCount();
            } else if (jResult.status == "logout") {
                location.reload();
            } else if (jResult.status == "fail") {
                showAlert('충전처리가 실패되었습니다.');
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
        url: '/api/chargeproc_count',
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
        url: '/api/chargeproc_page',
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