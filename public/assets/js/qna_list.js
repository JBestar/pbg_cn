var mArrNotice
var mQnaAnswerBusy = false;

$(document).ready(function() {

    reqCount();
});


function qnaEsc(s) {
    return String(s == null ? '' : s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function qnaRefreshWait() {
    if (typeof reqWaitTransfer === 'function') {
        reqWaitTransfer();
    }
}

function showPage(arrInfo) {
    let tHtml = "";
    mArrNotice = arrInfo;
    if (arrInfo != null) {

        for (let idx in arrInfo) {
            var row = arrInfo[idx];
            var waiting = String(row.notice_answer_state) === '0';
            var fid = parseInt(row.notice_fid, 10) || 0;
            tHtml += "<tr" + (waiting ? " class=\"qna-wait\"" : "") + ">";
            tHtml += "<td class=\"tdDate\">" + qnaEsc(row.notice_send_uid) + "</td>";
            tHtml += "<td class=\"tdDate\">" + qnaEsc(row.notice_title) + "</td>";
            tHtml += "<td class=\"tdDate\">" + getQnaStateText(row.notice_answer_state) + "</td>";
            tHtml += "<td class=\"tdDate\">" + qnaEsc(row.notice_create_time) + "</td>";

            tHtml += "<td class=\"tdDate\"><button type=\"button\" class=\"btn_blue\" ";
            tHtml += "onclick=\"showAnswerQna(" + parseInt(idx, 10) + ");\"> ";
            tHtml += "답변</button></td>";
            tHtml += "<td class=\"tdDate\"><button type=\"button\" class=\"btn_red btn_icon\" title=\"삭제\" aria-label=\"삭제\" ";
            tHtml += "onclick=\"deleteQna(" + fid + ");\"><i class=\"fas fa-trash-alt\"></i></button></td>";
            tHtml += "</tr>";

        }

    }
    $('#tbodyList').html(tHtml);
}


function deleteQna(no) {
    if (!confirm("삭제하시겠습니까?")) {
        return;
    }
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
                qnaRefreshWait();
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

    if (mQnaAnswerBusy) {
        return;
    }
    if (!confirm("발송하시겠습니까?")) {
        return;
    }
    var jsonData = JSON.stringify(objData);
    mQnaAnswerBusy = true;

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
                qnaRefreshWait();
            } else if (jResult.status == "fail") {
                showAlert('발송이 실패되었습니다.');
            } else if (jResult.status == "logout") {
                location.reload();
            }
        },
        error: function(request, status, error) {
            showAlert('발송이 실패되었습니다.');
        },
        complete: function() {
            mQnaAnswerBusy = false;
        }

    });

}