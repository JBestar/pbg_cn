var mMaxFeeRatio = null;

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
    if ($input.length) $input.attr('max', mMaxFeeRatio);
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
                applyMaxFeeHint(jResult.data.mb_game_pb_ratio || 0);
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
        showAlert(feeOverMsg(mMaxFeeRatio));
        return false;
    }
    return true;
}

function initStoreReg() {
    loadMaxFee();
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
        game_ratio: $('#regSubSingleDealRate').val() || '0'
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
