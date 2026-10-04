/*
 * 하부 충전요청 / 환전요청 공용 목록.
 * View sets window.SUB_REQ_CONF = { kind: 'charge' | 'exchange' } before loading this file.
 */
var SUB_REQ = (function() {
    var kind = (window.SUB_REQ_CONF && window.SUB_REQ_CONF.kind === 'exchange') ? 'exchange' : 'charge';
    return {
        kind: kind,
        api: kind === 'exchange' ? 'exchangeproc' : 'chargeproc',
        idKey: kind === 'exchange' ? 'exchange_id' : 'charge_id',
        col: kind === 'exchange' ? 'exchange_' : 'charge_'
    };
})();

var mSubReqListSeq = 0;
var mSubReqBusy = {};

$(document).ready(function() {
    reqCount();
});

function subReqT(key, fallback) {
    var t = window.ADMIN_I18N || {};
    return t[key] || fallback;
}

function subReqEsc(s) {
    return String(s == null ? '' : s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function subReqMoney(v) {
    return (Number(v) || 0).toLocaleString(undefined, { maximumFractionDigits: 2 });
}

function subReqField(row, name) {
    return row ? row[SUB_REQ.col + name] : undefined;
}

function subReqStateHtml(state) {
    switch (parseInt(state, 10)) {
        case 0:
            return '<span class="req-badge req-badge-wait">' + subReqEsc(subReqT('state_unconfirmed', '미확인')) + '</span>';
        case 1:
            return '<span class="req-badge req-badge-ok">' + subReqEsc(subReqT('state_permitted', '승인')) + '</span>';
        case 2:
            return '<span class="req-badge req-badge-no">' + subReqEsc(subReqT('state_refused', '취소')) + '</span>';
        default:
            return '';
    }
}

function subReqTypeHtml(type) {
    var isPresent = parseInt(type, 10) === 1;
    var key, fallback;
    if (SUB_REQ.kind === 'exchange') {
        key = isPresent ? 'type_egg_recover' : 'type_exchange_req';
        fallback = isPresent ? '알회수' : '신청환전';
    } else {
        key = isPresent ? 'type_egg_charge' : 'type_charge_req';
        fallback = isPresent ? '알충전' : '신청충전';
    }
    return '<span class="req-type">' + subReqEsc(subReqT(key, fallback)) + '</span>';
}

function reqSearch() {
    reqCount();
}

function subReqQuery(extra) {
    var start = $('#inputDateS').val() || '';
    var end = $('#inputDateE').val() || '';
    var obj = {
        start: start,
        end: end,
        mb_uid: $.trim($('#inputSubId').val() || ''),
        channel: typeof getChannelFilter === 'function' ? getChannelFilter() : '',
        pending: (start === '' && end === '') ? 1 : 0
    };
    if (extra) {
        for (var k in extra) {
            if (extra.hasOwnProperty(k)) obj[k] = extra[k];
        }
    }
    return obj;
}

function showPage(arrInfo) {
    var rows = Array.isArray(arrInfo) ? arrInfo : [];
    var tHtml = '';
    var confirmLabel = subReqEsc(subReqT('btn_confirm', '확인'));
    var cancelLabel = subReqEsc(subReqT('btn_cancel', '취소'));
    var deleteLabel = subReqEsc(subReqT('btn_delete', '삭제'));

    for (var i = 0; i < rows.length; i++) {
        var row = rows[i] || {};
        var fid = parseInt(subReqField(row, 'fid'), 10) || 0;
        var state = parseInt(subReqField(row, 'action_state'), 10);
        var waiting = state === 0;

        tHtml += '<tr' + (waiting ? ' class="req-row-wait"' : '') + '>';
        tHtml += '<td class="tdDate">' + subReqEsc(subReqField(row, 'mb_uid')) + '</td>';
        tHtml += '<td class="tdDate">' + subReqEsc(row.mb_nickname) + '</td>';
        tHtml += '<td class="tdMoney">' + subReqMoney(subReqField(row, 'money')) + '</td>';
        tHtml += '<td class="tdDate">' + subReqStateHtml(state) + '</td>';
        tHtml += '<td class="tdDate">';
        if (waiting) {
            tHtml += '<button type="button" class="btn_blue" onclick="procSubReq(this, \'permit\', ' + fid + ');">' + confirmLabel + '</button> ';
            tHtml += '<button type="button" class="btn_red" onclick="procSubReq(this, \'cancel\', ' + fid + ');">' + cancelLabel + '</button>';
        } else {
            tHtml += '<button type="button" class="btn_red btn_icon" title="' + deleteLabel + '" aria-label="' + deleteLabel + '" '
                + 'onclick="procSubReq(this, \'delete\', ' + fid + ');"><i class="fas fa-trash-alt"></i></button>';
        }
        tHtml += '</td>';
        tHtml += '<td class="tdDate">' + subReqTypeHtml(subReqField(row, 'type')) + '</td>';
        tHtml += '<td class="tdDate">' + subReqEsc(subReqField(row, 'time_require')) + '</td>';
        tHtml += '<td class="tdDate">' + (waiting ? '' : subReqEsc(subReqField(row, 'time_process'))) + '</td>';
        tHtml += '</tr>';
    }
    $('#tbodyList').html(tHtml);
}

function procSubReq(btn, action, fid) {
    if (!(fid > 0) || mSubReqBusy[fid]) return;

    var msg;
    if (action === 'permit') msg = subReqT('msg_confirm_permit', '승인하시겠습니까?');
    else if (action === 'cancel') msg = subReqT('msg_confirm_refuse', '취소하시겠습니까?');
    else msg = subReqT('msg_confirm_delete', '삭제하시겠습니까?');
    if (!confirm(msg)) return;

    var payload = {};
    payload[SUB_REQ.idKey] = fid;

    mSubReqBusy[fid] = true;
    var $btns = $(btn).closest('td').find('button');
    $btns.prop('disabled', true);

    $.ajax({
        url: '/api/' + SUB_REQ.api + '_' + action,
        data: { json_: JSON.stringify(payload) },
        type: 'post',
        dataType: 'json',
        success: function(jResult) {
            if (jResult.status == 'success') {
                reqCount();
                if (typeof reqWaitTransfer === 'function') reqWaitTransfer();
                if (action === 'permit' && typeof reqAssets === 'function') {
                    setTimeout(function() { reqAssets(); }, 300);
                }
            } else if (jResult.status == 'logout') {
                location.reload();
            } else {
                if (parseInt(jResult.code, 10) === 9) {
                    showAlert(SUB_REQ.kind === 'exchange'
                        ? subReqT('msg_over_money_store', '매장 보유머니가 부족합니다.')
                        : subReqT('msg_over_money', '보유머니가 부족합니다.'));
                } else {
                    showAlert(subReqT('msg_proc_fail', '처리가 실패되었습니다.'));
                }
                reqCount();
            }
        },
        error: function() {
            showAlert(subReqT('msg_proc_fail', '처리가 실패되었습니다.'));
        },
        complete: function() {
            delete mSubReqBusy[fid];
            $btns.prop('disabled', false);
        }
    });
}

function reqSum() {
    var seq = mSubReqListSeq;
    $.ajax({
        url: '/api/' + SUB_REQ.api + '_sum',
        data: { json_: JSON.stringify(subReqQuery()) },
        type: 'post',
        dataType: 'json',
        success: function(jResult) {
            if (seq !== mSubReqListSeq) return;
            if (jResult.status == 'success') {
                $('#tdSumMoney').text(subReqMoney(jResult.data));
            }
        }
    });
}

function reqCount() {
    var seq = ++mSubReqListSeq;
    $.ajax({
        url: '/api/' + SUB_REQ.api + '_count',
        type: 'post',
        data: { json_: JSON.stringify(subReqQuery()) },
        dataType: 'json',
        success: function(jResult) {
            if (seq !== mSubReqListSeq) return;
            if (jResult.status == 'success') {
                TotalCount = parseInt(jResult.data, 10) || 0;
                setFirstPage();
                reqPage();
                reqSum();
            } else if (jResult.status == 'logout') {
                location.reload();
            }
        }
    });
}

function reqPage() {
    var seq = ++mSubReqListSeq;
    $.ajax({
        url: '/api/' + SUB_REQ.api + '_page',
        data: { json_: JSON.stringify(subReqQuery({ page: getActivePage(), cntper: CountPerPage })) },
        type: 'post',
        dataType: 'json',
        success: function(jResult) {
            if (seq !== mSubReqListSeq) return;
            if (jResult.status == 'success') {
                showPage(jResult.data);
            } else if (jResult.status == 'logout') {
                location.reload();
            }
        }
    });
}
