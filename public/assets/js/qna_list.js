var mArrNotice

$(document).ready(function() {

    reqCount();
});


function showPage(arrInfo) {
    let tHtml = "";
    mArrNotice = arrInfo;
    if (arrInfo != null) {

        for (let idx in arrInfo) {
            tHtml += "<tr>";
            tHtml += "<td class=\"tdDate\">" + arrInfo[idx].notice_send_uid + "</td>";
            tHtml += "<td class=\"tdDate\">" + arrInfo[idx].notice_title + "</td>";
            tHtml += "<td class=\"tdDate\">" + getQnaStateText(arrInfo[idx].notice_answer_state) + "</td>";
            tHtml += "<td class=\"tdDate\">" + arrInfo[idx].notice_create_time + "</td>";

            tHtml += "<td class=\"tdDate\"><button type=\"button\" class=\"btn_blue\" ";
            tHtml += "onclick=\"showAnswerQna(" + idx + ");\"> ";
            tHtml += "답변</button></td>";
            tHtml += "<td class=\"tdDate\"><button type=\"button\" class=\"btn_red\" ";
            tHtml += "onclick=\"deleteQna(" + arrInfo[idx].notice_fid + ");\">삭제</button></td>";
            tHtml += "</tr>";

        }

    }
    $('#tbodyList').html(tHtml);
}


function deleteQna(no) {
    var objData = {
        "no": no
    };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/qnalist_delete',
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
        url: '/api/qnalist_count',
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
        url: '/api/qnalist_page',
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


function showAnswerQna(idx) {

    if (mArrNotice == null) {
        closeAnswerQna();
        return;
    }
    $('#qnaId').val(mArrNotice[idx].notice_fid);
    $('#qnaSenderId').val(mArrNotice[idx].notice_send_uid);
    $('#qnaTitle').val(mArrNotice[idx].notice_title);
    $('#qnaContent').val(mArrNotice[idx].notice_content);
    $('#qnaAnswer').val(mArrNotice[idx].notice_answer);

    $('#divAnswerQna').show();

}

function closeAnswerQna() {
    $('#divAnswerQna').hide();
}




function reqAnswerQna() {

    var objData = {
        "no": $('#qnaId').val(),
        "answer": $('#qnaAnswer').val()
    };


    if (objData.answer.length < 1) {
        showAlert('답변내용을 입력해주세요');
        return;
    }

    if (!confirm("발송하시겠습니까?")) {
        return;
    }
    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/qnalist_reg',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);

            if (jResult.status == "success") {
                showAlert('답변이 발송되었습니다.');
                closeAnswerQna();
                reqCount();
            } else if (jResult.status == "fail") {
                showAlert('발송이 실패되었습니다.');
            } else if (jResult.status == "logout") {
                location.reload();
            }
        },
        error: function(request, status, error) {
            // console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }

    });

}