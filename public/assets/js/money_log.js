$(document).ready(function() {
    reqCount();

});

function reqSearch() {
    reqCount();
}



function showPage(arrInfo) {
    let tHtml = "";
    let nPage = getActivePage();
    if (arrInfo != null) {

        let rowNo = (nPage - 1) * CountPerPage;
        for (let idx in arrInfo) {
            rowNo++;
            tHtml += "<tr>";
            tHtml += "<td class=\"tdDate\">" + rowNo + "</td>";
            tHtml += "<td class=\"tdDate\">" + arrInfo[idx].money_mb_uid + "</td>";
            tHtml += "<td class=\"tdDate\">" + arrInfo[idx].mb_nickname + "</td>";
            tHtml += "<td class=\"tdDate\">" + getMoneyChangeTypeText(arrInfo[idx].money_change_type) + "</td>";
            tHtml += "<td class=\"tdDate\">" + parseInt(arrInfo[idx].money_before).toLocaleString() + "</td>";
            tHtml += "<td class=\"tdDate\">" + parseInt(arrInfo[idx].money_amount).toLocaleString() + "</td>";
            tHtml += "<td class=\"tdDate\">" + parseInt(arrInfo[idx].money_after).toLocaleString() + "</td>";
            tHtml += "<td class=\"tdDate\">" + arrInfo[idx].money_mb_ech_uid + "</td>";
            tHtml += "<td class=\"tdDate\">" + arrInfo[idx].money_update_time + "</td>";
            tHtml += "</tr>";

        }

    }
    $('#tbodyList').html(tHtml);
}


function reqCount() {

    var objData = {
        "start": $('#inputDateS').val(),
        "end": $('#inputDateE').val(),
        "type": $('#selectMoneyType').val()
    };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/moneylog_count',
        data: { json_: jsonData },
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
        "start": $('#inputDateS').val(),
        "end": $('#inputDateE').val(),
        "type": $('#selectMoneyType').val(),
        "page": getActivePage(),
        "cntper": CountPerPage
    };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/moneylog_page',
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