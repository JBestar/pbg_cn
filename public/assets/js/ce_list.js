$(document).ready(function() {
    reqPage();
});

function reqSearch() {
    reqPage();
}

function esc(s) {
    return String(s == null ? '' : s)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

function escAttr(s) {
    return String(s == null ? '' : s)
        .replace(/\\/g, '\\\\').replace(/'/g, "\\'");
}

function fmtMoney(n) {
    return (parseInt(n, 10) || 0).toLocaleString();
}

function levelText(lv) {
    if (typeof getMemberLevelText === 'function') {
        return getMemberLevelText(lv);
    }
    return (window.ADMIN_I18N && window.ADMIN_I18N.role_agency) ? window.ADMIN_I18N.role_agency : '총판';
}

function confirmLabel() {
    return (window.ADMIN_I18N && window.ADMIN_I18N.btn_ok) ? window.ADMIN_I18N.btn_ok : '확인';
}

/** Stop TTS loop immediately and refresh wait counters. */
function stopWaitAlertAndRefresh() {
    try {
        if (typeof window.speechSynthesis !== 'undefined') {
            window.speechSynthesis.cancel();
        }
    } catch (e) { /* ignore */ }
    if (typeof reqWaitTransfer === 'function') {
        reqWaitTransfer();
    }
    if (typeof reqAssets === 'function') {
        setTimeout(function() { reqAssets(); }, 400);
    }
}

function pendingReqCell(fid, money, kind) {
    fid = parseInt(fid, 10) || 0;
    money = parseInt(money, 10) || 0;
    var html = '<td class="tdMoney td-pending-req">';
    if (money > 0) {
        html += '<span class="ce-req-amt">' + fmtMoney(money) + '</span>';
        if (fid > 0) {
            html += ' ';
            if (kind === 'charge') {
                html += '<button type="button" class="btn-ce-confirm" onclick="permitAgencyCharge('
                    + fid + ');">' + esc(confirmLabel()) + '</button>';
            } else {
                html += '<button type="button" class="btn-ce-confirm" onclick="permitAgencyExchange('
                    + fid + ');">' + esc(confirmLabel()) + '</button>';
            }
        }
    } else {
        html += '—';
    }
    html += '</td>';
    return html;
}

function showPage(arrInfo) {
    var tHtml = '';
    var sumCharge = 0, sumEx = 0, sumPt = 0, sumDiff = 0;
    var sumChargeReq = 0, sumExchangeReq = 0;
    var detailLabel = (window.ADMIN_I18N && window.ADMIN_I18N.th_detail_view)
        ? window.ADMIN_I18N.th_detail_view : '상세보기';
    var totalLabel = (window.ADMIN_I18N && window.ADMIN_I18N.th_total)
        ? window.ADMIN_I18N.th_total : '합계';
    var showGrade = !!window.CE_SHOW_GRADE;
    var showAgency = !!window.CE_SHOW_AGENCY;
    var showPending = !!window.CE_SHOW_PENDING;
    var colSpanId = 2;
    if (showGrade) colSpanId += 1;
    if (showAgency) colSpanId += 1;

    if (arrInfo != null) {
        for (var idx in arrInfo) {
            var r = arrInfo[idx];
            var charge = parseInt(r.charge_sum, 10) || 0;
            var exchange = parseInt(r.exchange_sum, 10) || 0;
            var point = parseFloat(r.point_sum) || 0;
            var diff = parseFloat(r.diff_sum);
            if (isNaN(diff)) diff = charge - exchange - point;
            var chargeReq = parseInt(r.charge_req_money, 10);
            if (isNaN(chargeReq)) chargeReq = parseInt(r.pending_charge_money, 10) || 0;
            var exchangeReq = parseInt(r.exchange_req_money, 10);
            if (isNaN(exchangeReq)) exchangeReq = parseInt(r.pending_exchange_money, 10) || 0;
            sumCharge += charge;
            sumEx += exchange;
            sumPt += point;
            sumDiff += diff;
            sumChargeReq += chargeReq;
            sumExchangeReq += exchangeReq;

            var diffCls = diff >= 0 ? 'td-diff-pos' : 'td-diff-neg';
            tHtml += '<tr>';
            if (showGrade) {
                tHtml += '<td class="tdDate">' + esc(levelText(r.mb_level)) + '</td>';
            }
            if (showAgency) {
                tHtml += '<td class="tdDate">' + esc(r.agency_uid || '') + '</td>';
            }
            tHtml += '<td class="tdDate">' + esc(r.mb_uid) + '</td>';
            tHtml += '<td class="tdDate">' + esc(r.mb_nickname) + '</td>';
            if (showPending) {
                tHtml += pendingReqCell(r.pending_charge_fid, chargeReq, 'charge');
                tHtml += '<td class="tdMoney">' + fmtMoney(charge) + '</td>';
                tHtml += pendingReqCell(r.pending_exchange_fid, exchangeReq, 'exchange');
                tHtml += '<td class="tdMoney">' + fmtMoney(exchange) + '</td>';
            } else {
                tHtml += '<td class="tdMoney">' + fmtMoney(charge) + '</td>';
                tHtml += '<td class="tdMoney">' + fmtMoney(exchange) + '</td>';
            }
            tHtml += '<td class="tdMoney">' + fmtPoint(point) + '</td>';
            tHtml += '<td class="tdMoney ' + diffCls + '">' + fmtPoint(diff) + '</td>';
            tHtml += '<td class="tdDate"><button type="button" class="btn-detail-view" onclick="openCeDetail(\''
                + escAttr(r.mb_uid) + '\');">' + esc(detailLabel) + '</button></td>';
            tHtml += '</tr>';
        }
    }

    var sumDiffCls = sumDiff >= 0 ? 'td-diff-pos' : 'td-diff-neg';
    tHtml += '<tr style="background:#f5f5f5;font-weight:bold;">';
    tHtml += '<td class="tdDate" colspan="' + colSpanId + '">' + esc(totalLabel) + '</td>';
    if (showPending) {
        tHtml += '<td class="tdMoney">' + fmtMoney(sumChargeReq) + '</td>';
        tHtml += '<td class="tdMoney">' + fmtMoney(sumCharge) + '</td>';
        tHtml += '<td class="tdMoney">' + fmtMoney(sumExchangeReq) + '</td>';
        tHtml += '<td class="tdMoney">' + fmtMoney(sumEx) + '</td>';
    } else {
        tHtml += '<td class="tdMoney">' + fmtMoney(sumCharge) + '</td>';
        tHtml += '<td class="tdMoney">' + fmtMoney(sumEx) + '</td>';
    }
    tHtml += '<td class="tdMoney">' + fmtPoint(sumPt) + '</td>';
    tHtml += '<td class="tdMoney ' + sumDiffCls + '">' + fmtPoint(sumDiff) + '</td>';
    tHtml += '<td class="tdDate"></td>';
    tHtml += '</tr>';

    $('#tbodyList').html(tHtml);
}

function openCeDetail(uid) {
    var start = $('#inputDateS').val() || '';
    var end = $('#inputDateE').val() || '';
    var path = window.CE_DETAIL_PATH || '/Main/store_ce_detail';
    var url = path + '?uid=' + encodeURIComponent(uid)
        + '&start=' + encodeURIComponent(start)
        + '&end=' + encodeURIComponent(end);
    window.open(url, 'ceDetail_' + uid, 'width=1100,height=720,scrollbars=yes,resizable=yes');
}

function permitAgencyCharge(chargeId) {
    if (!confirm('승인하시겠습니까?')) return;
    $.ajax({
        url: '/api/chargeproc_permit',
        data: { json_: JSON.stringify({ charge_id: chargeId }) },
        type: 'post',
        dataType: 'json',
        success: function(jResult) {
            if (jResult.status === 'success') {
                stopWaitAlertAndRefresh();
                reqPage();
            } else if (jResult.status === 'logout') {
                location.reload();
            } else if (jResult.status === 'fail') {
                if (jResult.code == 9) showAlert('보유머니가 부족합니다.');
                else showAlert('충전처리가 실패되었습니다.');
            }
        }
    });
}

function permitAgencyExchange(exchangeId) {
    if (!confirm('승인하시겠습니까?')) return;
    $.ajax({
        url: '/api/exchangeproc_permit',
        data: { json_: JSON.stringify({ exchange_id: exchangeId }) },
        type: 'post',
        dataType: 'json',
        success: function(jResult) {
            if (jResult.status === 'success') {
                stopWaitAlertAndRefresh();
                reqPage();
            } else if (jResult.status === 'logout') {
                location.reload();
            } else if (jResult.status === 'fail') {
                showAlert('환전처리가 실패되었습니다.');
            }
        }
    });
}

function reqPage() {
    var objData = {
        start: $('#inputDateS').val(),
        end: $('#inputDateE').val(),
        mb_uid: $('#inputUserID').val()
    };
    var api = window.CE_API || '/api/store_ce_summary';
    $.ajax({
        url: api,
        data: { json_: JSON.stringify(objData) },
        type: 'post',
        dataType: 'json',
        success: function(jResult) {
            if (jResult.status == 'success') {
                showPage(jResult.data);
            } else if (jResult.status == 'logout') {
                location.reload();
            }
        }
    });
}
