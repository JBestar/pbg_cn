var mMaxFeeRatio = null;
var mAgencyFeeRatio = null;

function showAlert(msg) {
    if (window.Swal) {
        Swal.fire({
            text: String(msg),
            confirmButtonText: i18n('btn_ok', '확인')
        });
    } else {
        alert(msg);
    }
}

function i18n(key, fallback) {
    if (window.ADMIN_I18N && window.ADMIN_I18N[key]) return window.ADMIN_I18N[key];
    return fallback || key;
}

function feeOverMsg(max) {
    return i18n('msg_fee_over', '수수료가 총판 수수료({max}%)를 초과할 수 없습니다.')
        .replace('{max}', String(max));
}

function feeMaxHint(max) {
    return i18n('hint_fee_max', '최대 {max}%').replace('{max}', String(max));
}

function notifyOpenerRefresh() {
    try {
        if (window.opener && typeof window.opener.refreshStoreList === 'function') {
            window.opener.refreshStoreList();
        } else if (window.opener && typeof window.opener.reqPage === 'function') {
            window.opener.reqPage();
        }
    } catch (e) { /* ignore */ }
}

function plainOrEmpty(p) {
    p = String(p == null ? '' : p);
    if (!p) return '';
    if (p.indexOf('$2y$') === 0 || p.indexOf('$2a$') === 0 || p.indexOf('$argon') === 0) return '';
    return p;
}

function applyMaxFeeHint(max) {
    mMaxFeeRatio = parseFloat(max);
    if (isNaN(mMaxFeeRatio)) mMaxFeeRatio = 0;
    var $hint = $('#hintFeeMax');
    if ($hint.length) $hint.text(feeMaxHint(mMaxFeeRatio));
    var $input = $('#regSubSingleDealRate, #editSubSingleDealRate');
    if ($input.length) {
        $input.attr('max', mMaxFeeRatio);
        // Max fee value itself is the placeholder (e.g. "2")
        $input.attr('placeholder', String(mMaxFeeRatio));
    }
}

function loadMaxFee(done) {
    $.ajax({
        url: '/api/assets',
        type: 'post',
        dataType: 'json',
        success: function (jResult) {
            if (jResult.status === 'logout') {
                window.close();
                return;
            }
            if (jResult.status === 'success' && jResult.data) {
                mAgencyFeeRatio = parseFloat(jResult.data.mb_game_pb_ratio) || 0;
                applyMaxFeeHint(mAgencyFeeRatio);
            }
            if (typeof done === 'function') done();
        },
        error: function () {
            if (typeof done === 'function') done();
        }
    });
}

function validateFee(gameRatio) {
    if (mMaxFeeRatio === null) return true;
    var fee = parseFloat(gameRatio);
    if (isNaN(fee)) fee = 0;
    if (fee > mMaxFeeRatio) {
        var parentUid = $.trim($('#regSubParent').val() || '');
        if (parentUid) {
            showAlert(i18n('msg_fee_over_parent', '수수료가 상위 매장 수수료를 초과할 수 없습니다.'));
        } else {
            showAlert(feeOverMsg(mMaxFeeRatio));
        }
        return false;
    }
    return true;
}

/** Build a flat parent-option list with indent from mb_parent_fid. */
function fillParentCombo(rows) {
    var $sel = $('#regSubParent');
    if (!$sel.length) return;
    var noneLabel = i18n('parent_store_none', '(없음 — 총판 직속)');
    $sel.empty().append($('<option>').val('').text(noneLabel));
    if (!rows || !rows.length) return;

    var byParent = {};
    var map = {};
    var i, r, fid, pf;
    for (i = 0; i < rows.length; i++) {
        r = rows[i];
        fid = parseInt(r.mb_fid, 10) || 0;
        pf = parseInt(r.mb_parent_fid, 10) || 0;
        map[fid] = r;
        if (!byParent[pf]) byParent[pf] = [];
        byParent[pf].push(r);
    }
    Object.keys(byParent).forEach(function (k) {
        byParent[k].sort(function (a, b) {
            return String(a.mb_uid).localeCompare(String(b.mb_uid));
        });
    });

    var visited = {};
    function addOption(row, depth) {
        var rate = parseFloat(row.mb_game_pb_ratio);
        if (isNaN(rate)) rate = 0;
        var pad = '';
        for (var d = 0; d < depth; d++) pad += '　';
        var label = pad + (depth ? '└ ' : '') + row.mb_uid
            + (row.mb_nickname ? ' / ' + row.mb_nickname : '')
            + ' (' + rate + '%)';
        $sel.append(
            $('<option>')
                .val(row.mb_uid)
                .attr('data-rate', rate)
                .text(label)
        );
    }
    function walk(parentFid, depth) {
        var list = byParent[parentFid] || [];
        for (var n = 0; n < list.length; n++) {
            var child = list[n];
            var cf = parseInt(child.mb_fid, 10) || 0;
            if (visited[cf]) continue;
            visited[cf] = true;
            addOption(child, depth);
            walk(cf, depth + 1);
        }
    }
    walk(0, 0);
    // Orphans (parent missing from list) as extra roots
    for (i = 0; i < rows.length; i++) {
        r = rows[i];
        fid = parseInt(r.mb_fid, 10) || 0;
        if (visited[fid]) continue;
        pf = parseInt(r.mb_parent_fid, 10) || 0;
        if (pf > 0 && !map[pf]) {
            visited[fid] = true;
            addOption(r, 0);
            walk(fid, 1);
        }
    }
}

function onParentChange() {
    var $opt = $('#regSubParent option:selected');
    var rate = $opt.attr('data-rate');
    if (rate == null || rate === '') {
        applyMaxFeeHint(mAgencyFeeRatio != null ? mAgencyFeeRatio : 0);
    } else {
        applyMaxFeeHint(rate);
    }
}

function loadParentOptions(done) {
    $.ajax({
        url: '/api/member_subs',
        type: 'post',
        dataType: 'json',
        success: function (jResult) {
            if (jResult.status === 'logout') {
                window.close();
                return;
            }
            if (jResult.status === 'success') {
                fillParentCombo(jResult.data || []);
            }
            if (typeof done === 'function') done();
        },
        error: function () {
            if (typeof done === 'function') done();
        }
    });
}

function initStoreReg() {
    loadMaxFee(function () {
        loadParentOptions(function () {
            onParentChange();
        });
    });
    $('#regSubParent').off('change.fee').on('change.fee', onParentChange);
}

function reqRegMember() {
    var objData = {
        uid: $('#regSubId').val(),
        nickname: $('#regSubName').val(),
        pwd: $('#regSubPwd').val(),
        bank_pwd: $('#regSubExcPwd').val(),
        phone: $('#regSubPhone').val(),
        bank_name: $('#regSubBank').val(),
        bank_num: $('#regSubBankNum').val(),
        bank_owner: $('#regSubBankOwner').val(),
        game_ratio: $('#regSubSingleDealRate').val() || '0',
        parent_uid: $.trim($('#regSubParent').val() || '')
    };
    if (!objData.uid || !objData.nickname || !objData.pwd || !objData.bank_pwd) {
        showAlert('아이디, 이름, 비밀번호, 출금 비밀번호는 필수입니다.');
        return;
    }
    if (!validateFee(objData.game_ratio)) return;
    if (!confirm('OK?')) return;
    $.ajax({
        url: '/api/member_register',
        data: { json_: JSON.stringify(objData) },
        type: 'post',
        dataType: 'json',
        success: function (jResult) {
            if (jResult.status === 'success') {
                showAlert('OK');
                notifyOpenerRefresh();
                setTimeout(function () { window.close(); }, 500);
            } else if (jResult.status === 'fail') {
                if (jResult.code == 6) showAlert('ID exists');
                else if (jResult.code == 7) showAlert('Name exists');
                else if (jResult.code == 10) showAlert(feeOverMsg(mMaxFeeRatio != null ? mMaxFeeRatio : ''));
                else if (jResult.code == 13) showAlert(i18n('msg_parent_invalid', '상위 추천계정을 찾을 수 없습니다.'));
                else if (jResult.code == 14) showAlert(i18n('msg_fee_over_parent', '수수료가 상위 매장 수수료를 초과할 수 없습니다.'));
                else showAlert('Fail');
            } else if (jResult.status === 'logout') {
                window.close();
            }
        }
    });
}

function initStoreEdit() {
    var fid = window.STORE_EDIT_FID;
    if (!fid) return;
    loadMaxFee();
    $.ajax({
        url: '/api/member_fetch',
        data: { json_: JSON.stringify({ fid: fid }) },
        type: 'post',
        dataType: 'json',
        success: function (jResult) {
            if (jResult.status === 'success' && jResult.data) {
                fillEditForm(jResult.data);
            } else if (jResult.status === 'logout') {
                window.close();
            }
        }
    });
}

function fillEditForm(d) {
    $('#editSubNo').val(d.mb_fid);
    $('#editSubId').text(d.mb_uid);
    $('#editSubName').text(d.mb_nickname);
    $('#editSubPwd').val(plainOrEmpty(d.mb_pwd));
    $('#editSubExcPwd').val(plainOrEmpty(d.mb_bank_pwd));
    $('#editSubPhone').val(d.mb_phone || '');
    $('#editSubBank').val(d.mb_bank_name || '');
    $('#editSubBankNum').val(d.mb_bank_num || '');
    $('#editSubBankOwner').val(d.mb_bank_owner || '');
    $('#editSubLimitRound').val(d.mb_limit_round || 0);
    $('#editSubLimitSingle').val(d.mb_limit_single || 0);
    $('#editSubLimitMix').val(d.mb_limit_mix || 0);
    $('#editSubLimitThree').val(d.mb_limit_three || 0);
    $('#editSubLimitDigit').val(d.mb_limit_digit || 0);
    $('#editSubSingleDealRate').val(d.mb_game_pb_ratio || 0);
    if (d.parent_uid) {
        $('#editSubParent').text(d.parent_uid);
        $('#editSubParentRow').show();
    }
}

function reqEditMember() {
    var objData = {
        uid: $('#editSubId').text(),
        nickname: $('#editSubName').text(),
        pwd: $('#editSubPwd').val(),
        bank_pwd: $('#editSubExcPwd').val(),
        phone: $('#editSubPhone').val(),
        bank_name: $('#editSubBank').val(),
        bank_num: $('#editSubBankNum').val(),
        bank_owner: $('#editSubBankOwner').val(),
        game_ratio: $('#editSubSingleDealRate').val() || '0',
        limit_round: $('#editSubLimitRound').val() || 0,
        limit_single: $('#editSubLimitSingle').val() || 0,
        limit_mix: $('#editSubLimitMix').val() || 0,
        limit_three: $('#editSubLimitThree').val() || 0,
        limit_digit: $('#editSubLimitDigit').val() || 0
    };
    if (!objData.uid || !objData.pwd || !objData.bank_pwd) {
        showAlert('비밀번호, 출금 비밀번호는 필수입니다.');
        return;
    }
    if (!validateFee(objData.game_ratio)) return;
    if (!confirm('OK?')) return;
    $.ajax({
        url: '/api/member_modify',
        data: { json_: JSON.stringify(objData) },
        type: 'post',
        dataType: 'json',
        success: function (jResult) {
            if (jResult.status === 'success') {
                showAlert('OK');
                notifyOpenerRefresh();
                setTimeout(function () { window.close(); }, 500);
            } else if (jResult.status === 'fail') {
                if (jResult.code == 10) showAlert(feeOverMsg(mMaxFeeRatio != null ? mMaxFeeRatio : ''));
                else if (jResult.code == 14) showAlert(i18n('msg_fee_over_parent', '수수료가 상위 매장 수수료를 초과할 수 없습니다.'));
                else if (jResult.code == 15) showAlert(i18n('msg_fee_under_child', '수수료가 하부 매장 수수료보다 낮을 수 없습니다.'));
                else showAlert('Fail');
            } else if (jResult.status === 'logout') {
                window.close();
            }
        }
    });
}

function loadMeAndTarget(targetUid, done) {
    $.ajax({
        url: '/api/assets',
        type: 'post',
        dataType: 'json',
        success: function (jMe) {
            if (jMe.status === 'logout') { window.close(); return; }
            var meObj = jMe.data || {};
            $.ajax({
                url: '/api/member_list',
                data: { json_: JSON.stringify({ mb_uid: targetUid }) },
                type: 'post',
                dataType: 'json',
                success: function (jList) {
                    var target = null;
                    if (jList.data) {
                        for (var i in jList.data) {
                            if (jList.data[i].mb_uid === targetUid) {
                                target = jList.data[i];
                                break;
                            }
                        }
                    }
                    done(meObj, target);
                }
            });
        }
    });
}

function addChargeAmt(n) {
    var cur = parseInt($('#serviceChargeMoney').val(), 10) || 0;
    $('#serviceChargeMoney').val(n > 0 ? cur + n : 0);
}

function addRecoverAmt(n) {
    var cur = parseInt($('#serviceExchangeMoney').val(), 10) || 0;
    $('#serviceExchangeMoney').val(n > 0 ? cur + n : 0);
}

function initStoreCharge() {
    var uid = $('#serviceChargeRecverUid').val();
    loadMeAndTarget(uid, function (meObj, target) {
        $('#serviceChargeSenderId').text((meObj.mb_uid || '') + ' (' + (meObj.mb_nickname || '') + ')');
        $('#serviceChargeSenderMoney').text((parseInt(meObj.mb_money, 10) || 0).toLocaleString());
        if (target) {
            $('#serviceChargeRecverId').text(target.mb_uid + ' (' + target.mb_nickname + ')');
            $('#serviceChargeRecverMoney').text((parseInt(target.mb_money, 10) || 0).toLocaleString());
        } else {
            $('#serviceChargeRecverId').text(uid);
        }
    });
}

function reqServiceCharge() {
    var money = parseInt($('#serviceChargeMoney').val(), 10) || 0;
    if (money < 1) { showAlert('amount'); return; }
    if (!confirm('OK?')) return;
    $.ajax({
        url: '/api/charge_service',
        data: { json_: JSON.stringify({ uid: $('#serviceChargeRecverUid').val(), money: money }) },
        type: 'post',
        dataType: 'json',
        success: function (jResult) {
            if (jResult.status === 'success') {
                showAlert('OK');
                notifyOpenerRefresh();
                setTimeout(function () { window.close(); }, 500);
            } else if (jResult.status === 'fail') {
                showAlert(jResult.code == 9 ? 'insufficient' : 'Fail');
            } else if (jResult.status === 'logout') {
                window.close();
            }
        }
    });
}

function initStoreRecover() {
    var uid = $('#serviceExchangeSenderUid').val();
    loadMeAndTarget(uid, function (meObj, target) {
        $('#serviceExchangeRecverId').text((meObj.mb_uid || '') + ' (' + (meObj.mb_nickname || '') + ')');
        $('#serviceExchangeRecverMoney').text((parseInt(meObj.mb_money, 10) || 0).toLocaleString());
        if (target) {
            $('#serviceExchangeSenderId').text(target.mb_nickname || target.mb_uid);
            $('#serviceExchangeSenderMoney').text((parseInt(target.mb_money, 10) || 0).toLocaleString());
        } else {
            $('#serviceExchangeSenderId').text(uid);
        }
    });
}

function reqServiceExchange() {
    var money = parseInt($('#serviceExchangeMoney').val(), 10) || 0;
    if (money < 1) { showAlert('amount'); return; }
    if (!confirm('OK?')) return;
    $.ajax({
        url: '/api/exchange_service',
        data: { json_: JSON.stringify({ uid: $('#serviceExchangeSenderUid').val(), money: money }) },
        type: 'post',
        dataType: 'json',
        success: function (jResult) {
            if (jResult.status === 'success') {
                showAlert('OK');
                notifyOpenerRefresh();
                setTimeout(function () { window.close(); }, 500);
            } else if (jResult.status === 'fail') {
                showAlert('Fail');
            } else if (jResult.status === 'logout') {
                window.close();
            }
        }
    });
}
