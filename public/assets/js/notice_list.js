var mArrNoticeBoard = null;
var mNoticeSaving = false;
var mNoticeListSeq = 0;

$(document).ready(function() {
    reqCount();
});

function noticeT(key, fallback) {
    var t = window.ADMIN_I18N || {};
    return t[key] || fallback;
}

function noticeEsc(s) {
    return String(s == null ? '' : s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function reqSearch() {
    reqCount();
}

function noticeListQuery(extra) {
    var obj = {
        start: $('#inputDateS').val() || '',
        end: $('#inputDateE').val() || '',
        title: $.trim($('#inputNoticeTitle').val() || '')
    };
    if (extra) {
        for (var k in extra) {
            if (extra.hasOwnProperty(k)) obj[k] = extra[k];
        }
    }
    return obj;
}

function showPage(arrInfo) {
    var tHtml = '';
    mArrNoticeBoard = Array.isArray(arrInfo) ? arrInfo : [];
    var page = getActivePage();
    var startNo = TotalCount - (page - 1) * CountPerPage;
    var delLabel = noticeEsc(noticeT('btn_delete', '삭제'));
    var editLabel = noticeEsc(noticeT('btn_edit', '수정'));

    for (var i = 0; i < mArrNoticeBoard.length; i++) {
        var row = mArrNoticeBoard[i] || {};
        var fid = parseInt(row.notice_fid, 10) || 0;
        tHtml += '<tr>';
        tHtml += '<td class="tdDate">' + (startNo - i) + '</td>';
        tHtml += '<td class="tdDate notice-title-cell">' + noticeEsc(row.notice_title) + '</td>';
        tHtml += '<td class="tdDate">' + noticeEsc(row.notice_create_time) + '</td>';
        tHtml += '<td class="tdDate"><button type="button" class="btn_blue" onclick="showViewNotice(' + i + ');">'
            + noticeEsc(noticeT('th_view_content', '내용보기')) + '</button></td>';
        tHtml += '<td class="tdDate">';
        tHtml += '<button type="button" class="btn_blue btn_icon" title="' + editLabel + '" aria-label="' + editLabel + '" '
            + 'onclick="showEditNotice(' + i + ');" style="margin-right:5px;"><i class="fas fa-pencil-alt"></i></button>';
        tHtml += '<button type="button" class="btn_red btn_icon" title="' + delLabel + '" aria-label="' + delLabel + '" '
            + 'onclick="deleteNotice(' + fid + ');"><i class="fas fa-trash-alt"></i></button>';
        tHtml += '</td>';
        tHtml += '</tr>';
    }
    $('#tbodyList').html(tHtml);
}

function reqCount() {
    var seq = ++mNoticeListSeq;
    $.ajax({
        url: '/api/noticelist_count',
        data: { json_: JSON.stringify(noticeListQuery()) },
        type: 'post',
        dataType: 'json',
        success: function(jResult) {
            if (seq !== mNoticeListSeq) return;
            if (jResult.status == 'success') {
                TotalCount = parseInt(jResult.data, 10) || 0;
                setFirstPage();
                reqPage();
            } else if (jResult.status == 'logout') {
                location.reload();
            }
        }
    });
}

function reqPage() {
    var seq = ++mNoticeListSeq;
    $.ajax({
        url: '/api/noticelist_page',
        data: { json_: JSON.stringify(noticeListQuery({ page: getActivePage(), cntper: CountPerPage })) },
        type: 'post',
        dataType: 'json',
        success: function(jResult) {
            if (seq !== mNoticeListSeq) return;
            if (jResult.status == 'success') {
                showPage(jResult.data);
            } else if (jResult.status == 'logout') {
                location.reload();
            }
        }
    });
}

//---------------------------------------------

function showViewNotice(idx) {
    var row = mArrNoticeBoard ? mArrNoticeBoard[idx] : null;
    if (!row) {
        closeViewNotice();
        return;
    }
    $('#tdNoticeViewTitle').text(row.notice_title == null ? '' : row.notice_title);
    $('#tdNoticeViewDate').text(row.notice_create_time == null ? '' : row.notice_create_time);
    $('#noticeViewContent').text(row.notice_content == null ? '' : row.notice_content);
    $('#divEditNotice').hide();
    $('#divViewNotice').show();
}

function closeViewNotice() {
    $('#divViewNotice').hide();
}

function openNoticeEditor(no, title, content) {
    var isEdit = no > 0;
    $('#noticeEditId').val(String(no));
    $('#noticeEditTitle').val(title);
    $('#noticeEditContent').val(content);
    $('#divEditNoticeTitle').text(isEdit ? noticeT('title_notice_edit', '공지수정') : noticeT('title_notice_write', '공지작성'));
    $('#btnNoticeSave').text(isEdit ? noticeT('btn_edit', '수정') : noticeT('btn_register', '등록'));
    $('#divViewNotice').hide();
    $('#divEditNotice').show();
    $('#noticeEditTitle').trigger('focus');
}

function showWriteNotice() {
    openNoticeEditor(0, '', '');
}

function showEditNotice(idx) {
    var row = mArrNoticeBoard ? mArrNoticeBoard[idx] : null;
    if (!row) return;
    openNoticeEditor(parseInt(row.notice_fid, 10) || 0,
        row.notice_title == null ? '' : row.notice_title,
        row.notice_content == null ? '' : row.notice_content);
}

function closeEditNotice() {
    $('#divEditNotice').hide();
}

function reqSaveNotice() {
    if (mNoticeSaving) return;
    var no = parseInt($('#noticeEditId').val(), 10) || 0;
    var title = $.trim($('#noticeEditTitle').val() || '');
    var content = $.trim($('#noticeEditContent').val() || '');

    if (title.length < 1) {
        showAlert(noticeT('msg_notice_title_req', '제목을 입력해주세요'));
        return;
    }
    if (content.length < 1) {
        showAlert(noticeT('msg_notice_content_req', '내용을 입력해주세요'));
        return;
    }

    var payload = { title: title, content: content };
    if (no > 0) payload.no = no;

    mNoticeSaving = true;
    $('#btnNoticeSave').prop('disabled', true);
    $.ajax({
        url: no > 0 ? '/api/noticelist_mod' : '/api/noticelist_reg',
        data: { json_: JSON.stringify(payload) },
        type: 'post',
        dataType: 'json',
        success: function(jResult) {
            if (jResult.status == 'success') {
                showAlert(noticeT('msg_notice_saved', '저장되었습니다.'));
                closeEditNotice();
                reqCount();
            } else if (jResult.status == 'logout') {
                location.reload();
            } else {
                showAlert(noticeT('msg_notice_fail', '처리에 실패했습니다.'));
            }
        },
        error: function() {
            showAlert(noticeT('msg_notice_fail', '처리에 실패했습니다.'));
        },
        complete: function() {
            mNoticeSaving = false;
            $('#btnNoticeSave').prop('disabled', false);
        }
    });
}

function deleteNotice(no) {
    if (!(no > 0)) return;
    if (!confirm(noticeT('msg_notice_delete_confirm', '이 공지를 삭제하시겠습니까?'))) {
        return;
    }
    $.ajax({
        url: '/api/noticelist_delete',
        data: { json_: JSON.stringify({ no: no }) },
        type: 'post',
        dataType: 'json',
        success: function(jResult) {
            if (jResult.status == 'success') {
                closeViewNotice();
                reqCount();
            } else if (jResult.status == 'logout') {
                location.reload();
            } else {
                showAlert(noticeT('msg_notice_fail', '처리에 실패했습니다.'));
            }
        }
    });
}
