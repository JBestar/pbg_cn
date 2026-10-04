var mArrNotice

$(document).ready(function() {

    reqCount();
    setTimeout(function() { fetchSubMember(); }, 1000);
});


function reqSearch() {
    reqCount();
}

function showPage(arrInfo) {
    let tHtml = "";
    mArrNotice = arrInfo;
    var me = window.ADMIN_UID || '';
    var t = window.ADMIN_I18N || {};
    if (arrInfo != null) {

        for (let idx in arrInfo) {
            var row = arrInfo[idx];
            var incoming = (row.notice_recv_uid === me);
            var unread = incoming && String(row.notice_recv_read) === '0';
            tHtml += "<tr" + (unread ? " class=\"memo-unread\"" : "") + ">";
            tHtml += "<td class=\"tdDate\">" + row.notice_send_uid + "</td>";
            tHtml += "<td class=\"tdDate\">" + row.notice_recv_uid + "</td>";
            tHtml += "<td class=\"tdDate\">" + row.notice_title + "</td>";
            tHtml += "<td class=\"tdDate\">" + row.notice_create_time + "</td>";

            tHtml += "<td class=\"tdDate\"><button type=\"button\" class=\"btn_blue\" ";
            tHtml += "onclick=\"showViewMemo(" + idx + ");\"> ";
            tHtml += (t.th_view_content || "내용보기") + "</button></td>";
            tHtml += "<td class=\"tdDate\">";
            if (incoming) {
                tHtml += "<button type=\"button\" class=\"btn_blue\" onclick=\"showEditMemo('" + String(row.notice_send_uid).replace(/'/g, '') + "');\" style=\"margin-right:5px;\">";
                tHtml += (t.btn_send_memo || "쪽지발송") + "</button>";
            } else {
                tHtml += "<button type=\"button\" class=\"btn_blue btn_icon\" title=\"수정\" aria-label=\"수정\" ";
                tHtml += "onclick=\"showModeMemo(" + idx + ");\" style=\"margin-right:5px;\">";
                tHtml += "<i class=\"fas fa-pencil-alt\"></i></button>";
            }
            tHtml += "<button type=\"button\" class=\"btn_red btn_icon\" title=\"" + (t.btn_delete || "삭제") + "\" aria-label=\"" + (t.btn_delete || "삭제") + "\" ";
            tHtml += "onclick=\"deleteMemo(" + row.notice_fid + ");\"><i class=\"fas fa-trash-alt\"></i></button></td>";
            tHtml += "</tr>";

        }

    }
    $('#tbodyList').html(tHtml);
}



function deleteMemo(no) {
    var t = window.ADMIN_I18N || {};
    if (!confirm((t.btn_delete || '삭제') + '?')) {
        return;
    }
    var objData = {
        "no": no
    };

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/memolist_delete',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);

            if (jResult.status == "success") {
                reqCount();
                if (typeof reqWaitTransfer === 'function') reqWaitTransfer();
            } else if (jResult.status == "logout") {
                location.reload();
            }
        },
        error: function(request, status, error) {
            // console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }

    });
}


function memoListQuery(extra) {
    var start = $('#inputDateS').val() || '';
    var end = $('#inputDateE').val() || '';
    var obj = {
        start: start,
        end: end,
        recv_uid: $('#inputUserID').val() || '',
        pending: (start === '' && end === '') ? 1 : 0
    };
    if (extra) {
        for (var k in extra) {
            if (extra.hasOwnProperty(k)) obj[k] = extra[k];
        }
    }
    return obj;
}

function reqCount() {

    var jsonData = JSON.stringify(memoListQuery());

    $.ajax({
        url: '/api/memolist_count',
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
    var jsonData = JSON.stringify(memoListQuery({
        page: getActivePage(),
        cntper: CountPerPage
    }));

    $.ajax({
        url: '/api/memolist_page',
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

//---------------------------------------------

function setSubMember(arrInfo) {
    let tHtml = "<option value=\"\">==수신자를 선택해주세요==</option>";
    if (arrInfo != null) {

        for (let idx in arrInfo) {

            tHtml += "<option value=\"" + arrInfo[idx].mb_uid + "\">" + arrInfo[idx].mb_uid;
            tHtml += " (" + arrInfo[idx].mb_nickname + ")</option>";

        }

    }
    $('#memoEditRecverId').html(tHtml);
    $('#memoModRecverId').html(tHtml);

}



function fetchSubMember() {
    $.ajax({
        url: '/api/member_subs',
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);

            if (jResult.status == "success") {
                setSubMember(jResult.data);
            } else if (jResult.status == "logout") {
                location.reload();
            }
        },
        error: function(request, status, error) {
            // console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }

    });
}


function showViewMemo(idx) {

    if (mArrNotice == null) {
        closeViewMemo();
        return;
    }
    var row = mArrNotice[idx];
    $('#tdViewSendUid').text(row.notice_send_uid);
    $('#tdViewRecvUid').text(row.notice_recv_uid);
    $('#tdViewTitle').text(row.notice_title);
    $('#memoViewContent').text(row.notice_content == null ? '' : row.notice_content);
    $('#divViewMemo').show();

    var me = window.ADMIN_UID || '';
    if (row.notice_recv_uid === me && String(row.notice_recv_read) === '0') {
        $.ajax({
            url: '/api/memolist_read',
            data: { json_: JSON.stringify({ no: row.notice_fid }) },
            type: 'post',
            dataType: 'json',
            success: function (jResult) {
                if (jResult.status === 'success') {
                    mArrNotice[idx].notice_recv_read = 1;
                    if (typeof reqWaitTransfer === 'function') reqWaitTransfer();
                    reqCount();
                }
            }
        });
    }

}

function closeViewMemo() {
    $('#divViewMemo').hide();
}





///---------------------------------


function showEditMemo(recvUid) {
    $('#memoEditRecverId').val(recvUid || '');
    $('#memoEditTitle').val('');
    $('#memoEditContent').val('');

    $('#divEditMemo').show();

}


function closeEditMemo() {
    $('#divEditMemo').hide();
}




function reqEditMemo() {

    var objData = {
        "recv_uid": $('#memoEditRecverId').val(),
        "title": $('#memoEditTitle').val(),
        "content": $('#memoEditContent').val()
    };

    if (objData.recv_uid.length < 1) {
        showAlert('수신자를 선택해주세요');
        return;
    }

    if (objData.title.length < 1) {
        showAlert('제목을 입력해주세요');
        return;
    }

    if (objData.content.length < 1) {
        showAlert('내용을 입력해주세요');
        return;
    }

    if (!confirm("발송하시겠습니까?")) {
        return;
    }
    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/memolist_reg',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);

            if (jResult.status == "success") {
                showAlert('쪽지가 발송되었습니다.');
                closeEditMemo();
                if (typeof reqWaitTransfer === 'function') reqWaitTransfer();
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




///--------------------------------

function showModeMemo(idx) {

    if (mArrNotice == null) {
        closeViewMemo();
        return;
    }

    $('#memoModeId').val(mArrNotice[idx].notice_fid);
    $('#memoModRecverId').val(mArrNotice[idx].notice_recv_uid);
    $('#memoModTitle').val(mArrNotice[idx].notice_title);
    $('#memoModContent').val(mArrNotice[idx].notice_content);
    $('#divModMemo').show();

}

function closeModeMemo() {
    $('#divModMemo').hide();
}




function reqModeMemo() {

    var objData = {
        "no": $('#memoModeId').val(),
        "title": $('#memoModTitle').val(),
        "content": $('#memoModContent').val()
    };


    if (objData.title.length < 1) {
        showAlert('제목을 입력해주세요');
        return;
    }

    if (objData.content.length < 1) {
        showAlert('내용을 입력해주세요');
        return;
    }

    var jsonData = JSON.stringify(objData);

    $.ajax({
        url: '/api/memolist_mod',
        data: { json_: jsonData },
        type: 'post',
        dataType: "json",
        success: function(jResult) {
            // console.log(jResult);

            if (jResult.status == "success") {
                showAlert('수정되었습니다.');
                closeModeMemo();
                reqCount();
            } else if (jResult.status == "fail") {
                showAlert('수정이 실패되었습니다.');
            } else if (jResult.status == "logout") {
                location.reload();
            }
        },
        error: function(request, status, error) {
            // console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
        }

    });

}