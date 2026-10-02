/**
 * Mobile management page: profile + menu grid + history/point panels.
 */
(function () {
  'use strict';

  var hooks = {
    toast: null,
    api: null,
    logout: null,
    onBalance: null,
    onNavigate: null,
  };

  var state = {
    member: { uid: '', name: '', balance: 0, point: 0 },
    busy: false,
    panel: null,
    liveTimer: null,
  };

  function I18N() { return window.PBGM_I18N; }
  function $(id) { return document.getElementById(id); }
  function $all(sel, root) {
    return Array.prototype.slice.call((root || document).querySelectorAll(sel));
  }

  function toast(msg) {
    if (typeof hooks.toast === 'function') hooks.toast(msg);
  }

  function fmtMoney(n) {
    var lang = I18N().getLang();
    var loc = lang === 'zh' ? 'zh-CN' : (lang === 'en' ? 'en-US' : 'ko-KR');
    return (Number(n) || 0).toLocaleString(loc, { maximumFractionDigits: 0 }) + 'u';
  }

  function fmtPoint(n) {
    var lang = I18N().getLang();
    var loc = lang === 'zh' ? 'zh-CN' : (lang === 'en' ? 'en-US' : 'ko-KR');
    return (Number(n) || 0).toLocaleString(loc, { maximumFractionDigits: 2 });
  }

  function esc(s) {
    return String(s == null ? '' : s)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function modeLabel(mode) {
    var m = parseInt(mode, 10) || 0;
    var t = function (k) { return I18N().t(k); };
    var P = t('markP') || 'P';
    var B = t('markB') || 'B';
    var map = {
      1: t('room1Title') + ' ' + P,
      2: t('room1Title') + ' ' + B,
      3: t('room2Title') + ' ' + P,
      4: t('room2Title') + ' ' + B,
      5: t('room1Title') + '+' + t('room2Title') + ' ' + P + '+' + P,
      6: t('room1Title') + '+' + t('room2Title') + ' ' + B + '+' + P,
      7: t('room1Title') + '+' + t('room2Title') + ' ' + P + '+' + B,
      8: t('room1Title') + '+' + t('room2Title') + ' ' + B + '+' + B,
      9: t('room3Title') + ' ' + P,
      10: t('room3Title') + ' ' + B,
      11: t('room4Title') + ' ' + P,
      12: t('room4Title') + ' ' + B,
      13: t('room3Title') + '+' + t('room4Title') + ' ' + P + '+' + P,
      14: t('room3Title') + '+' + t('room4Title') + ' ' + B + '+' + P,
      15: t('room3Title') + '+' + t('room4Title') + ' ' + P + '+' + B,
      16: t('room3Title') + '+' + t('room4Title') + ' ' + B + '+' + B,
    };
    if (map[m]) return map[m];
    if (m >= 30 && m <= 39) return t('betNumberGame') + ' ' + (m - 30);
    return '#' + m;
  }

  function stateMeta(st) {
    st = parseInt(st, 10) || 0;
    if (st === 1) return { cls: 'is-wait', key: 'mgmtStateWait' };
    if (st === 2) return { cls: 'is-lose', key: 'mgmtStateLose' };
    if (st === 3) return { cls: 'is-win', key: 'mgmtStateWin' };
    if (st === 4) return { cls: 'is-cancel', key: 'mgmtStateCancel' };
    return { cls: '', key: '' };
  }

  function renderProfile() {
    var m = state.member || {};
    var uid = $('mgmtUserId');
    var bal = $('mgmtBalance');
    var pt = $('mgmtPoint');
    if (uid) uid.textContent = m.uid || m.name || '-';
    if (bal) bal.textContent = fmtMoney(m.balance);
    if (pt) pt.textContent = fmtPoint(m.point) + 'u';
  }

  function showGrid(show) {
    var grid = $('mgmtGrid');
    if (grid) grid.hidden = !show;
  }

  function closePanel() {
    state.panel = null;
    var panel = $('mgmtPanel');
    if (panel) panel.hidden = true;
    showGrid(true);
  }

  function openPanel(title, html) {
    var panel = $('mgmtPanel');
    var titleEl = $('mgmtPanelTitle');
    var body = $('mgmtPanelBody');
    if (!panel || !body) return;
    if (titleEl) titleEl.textContent = title || '';
    body.innerHTML = html || '';
    panel.hidden = false;
    showGrid(false);
  }

  function soonHtml() {
    return '<p class="m-mgmt-soon">' + esc(I18N().t('mgmtSoon')) + '</p>';
  }

  function openSoon(titleKey) {
    state.panel = 'soon';
    openPanel(I18N().t(titleKey), soonHtml());
  }

  function chargeStateLabel(st) {
    st = parseInt(st, 10);
    if (st === 0) return I18N().t('mgmtChargeWait');
    if (st === 1) return I18N().t('mgmtChargeOk');
    if (st === 2) return I18N().t('mgmtChargeRefuse');
    return '-';
  }

  function chargeStateCls(st) {
    st = parseInt(st, 10);
    if (st === 0) return 'is-wait';
    if (st === 1) return 'is-win';
    if (st === 2) return 'is-cancel';
    return '';
  }

  function renderChargeForm(rows) {
    var uid = (state.member && (state.member.uid || state.member.name)) || '-';
    var html = ''
      + '<div class="m-charge">'
      + '<div class="m-charge-row">'
      + '<label>' + esc(I18N().t('mgmtChargeAmount')) + '</label>'
      + '<input type="text" id="mChargeAmount" class="m-charge-input" value="0" inputmode="numeric" readonly />'
      + '</div>'
      + '<div class="m-charge-presets" id="mChargePresets">'
      + [10, 30, 50, 100, 500, 1000, 5000, 10000].map(function (n) {
        return '<button type="button" class="m-charge-preset" data-charge-amt="' + n + '">' + n + 'u</button>';
      }).join('')
      + '</div>'
      + '<div class="m-charge-row">'
      + '<label>' + esc(I18N().t('mgmtChargeId')) + '</label>'
      + '<input type="text" class="m-charge-input" value="' + esc(uid) + '" readonly />'
      + '</div>'
      + '<div class="m-charge-actions">'
      + '<button type="button" class="m-mgmt-btn m-mgmt-btn--ok" id="mChargeSubmit">' + esc(I18N().t('mgmtCharge')) + '</button>'
      + '<button type="button" class="m-mgmt-btn m-mgmt-btn--danger" id="mChargeCancel">' + esc(I18N().t('mgmtCancel')) + '</button>'
      + '</div>'
      + '<button type="button" class="m-charge-account" id="mChargeAccount">' + esc(I18N().t('mgmtChargeAccount')) + '</button>'
      + '<h4 class="m-charge-hist-title">' + esc(I18N().t('mgmtChargeHist')) + '</h4>'
      + '<div class="m-charge-table-wrap"><table class="m-charge-table"><thead><tr>'
      + '<th>' + esc(I18N().t('mgmtChargeColAmount')) + '</th>'
      + '<th>' + esc(I18N().t('mgmtChargeColState')) + '</th>'
      + '<th>' + esc(I18N().t('mgmtChargeColReq')) + '</th>'
      + '<th>' + esc(I18N().t('mgmtChargeColProc')) + '</th>'
      + '</tr></thead><tbody id="mChargeHistBody">';
    if (!rows || !rows.length) {
      html += '<tr><td colspan="4" class="m-charge-empty">' + esc(I18N().t('mgmtHistEmpty')) + '</td></tr>';
    } else {
      for (var i = 0; i < rows.length; i++) {
        var r = rows[i] || {};
        html += '<tr>'
          + '<td>' + esc((Number(r.amount) || 0).toLocaleString()) + '</td>'
          + '<td><span class="m-charge-badge ' + chargeStateCls(r.state) + '">' + esc(chargeStateLabel(r.state)) + '</span></td>'
          + '<td>' + esc(r.requested_at || '-') + '</td>'
          + '<td>' + esc(r.processed_at || '-') + '</td>'
          + '</tr>';
      }
    }
    html += '</tbody></table></div></div>';
    return html;
  }

  function bindChargeUi() {
    var amount = 0;
    var input = $('mChargeAmount');
    function setAmount(n) {
      amount = Math.max(0, n | 0);
      if (input) input.value = amount > 0 ? String(amount) + 'u' : '0';
    }
    setAmount(0);
    $all('[data-charge-amt]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        setAmount(amount + (parseInt(btn.getAttribute('data-charge-amt'), 10) || 0));
      });
    });
    var cancel = $('mChargeCancel');
    if (cancel) {
      cancel.addEventListener('click', function () { setAmount(0); });
    }
    var submit = $('mChargeSubmit');
    if (submit) {
      submit.addEventListener('click', function () { doChargeRequest(amount); });
    }
    var acc = $('mChargeAccount');
    if (acc) {
      acc.addEventListener('click', doAccountRequest);
    }
  }

  function fillChargeHistory(rows) {
    var tbody = $('mChargeHistBody');
    if (!tbody) return;
    if (!rows || !rows.length) {
      tbody.innerHTML = '<tr><td colspan="4" class="m-charge-empty">' + esc(I18N().t('mgmtHistEmpty')) + '</td></tr>';
      return;
    }
    var html = '';
    for (var i = 0; i < rows.length; i++) {
      var r = rows[i] || {};
      html += '<tr>'
        + '<td>' + esc((Number(r.amount) || 0).toLocaleString()) + '</td>'
        + '<td><span class="m-charge-badge ' + chargeStateCls(r.state) + '">' + esc(chargeStateLabel(r.state)) + '</span></td>'
        + '<td>' + esc(r.requested_at || '-') + '</td>'
        + '<td>' + esc(r.processed_at || '-') + '</td>'
        + '</tr>';
    }
    tbody.innerHTML = html;
  }

  function openCharge() {
    // Hide mgmt overlay panel only — do NOT call closePanel()
    // (closePanel nulls state.panel and would abort the charge_list callback)
    var mgmtPanel = $('mgmtPanel');
    if (mgmtPanel) mgmtPanel.hidden = true;
    showGrid(true);
    state.panel = 'charge';
    if (typeof hooks.onNavigate === 'function') hooks.onNavigate('charge');

    var body = $('chargePageBody');
    if (!body) return;
    // Paint form immediately; history fills async
    body.innerHTML = renderChargeForm([]);
    bindChargeUi();
    var hist = $('mChargeHistBody');
    if (hist) {
      hist.innerHTML = '<tr><td colspan="4" class="m-charge-empty">' + esc(I18N().t('mgmtLoading')) + '</td></tr>';
    }

    if (typeof hooks.api !== 'function') {
      fillChargeHistory([]);
      return;
    }
    hooks.api('charge_list', { qs: '&limit=30' }).then(function (r) {
      if (state.panel !== 'charge') return;
      var json = (r && r.json) || {};
      var rows = (json.status === 'success' && Array.isArray(json.data)) ? json.data : [];
      fillChargeHistory(rows);
    }, function () {
      if (state.panel !== 'charge') return;
      fillChargeHistory([]);
    });
  }

  function closeChargePage() {
    if (state.panel === 'charge') state.panel = null;
    if (typeof hooks.onNavigate === 'function') hooks.onNavigate('mgmt');
  }

  function renderExchangeForm(rows) {
    var uid = (state.member && (state.member.uid || state.member.name)) || '-';
    var html = ''
      + '<div class="m-charge">'
      + '<div class="m-charge-row">'
      + '<label>' + esc(I18N().t('mgmtExchangeAmount')) + '</label>'
      + '<input type="text" id="mExchangeAmount" class="m-charge-input" value="0" inputmode="numeric" readonly />'
      + '</div>'
      + '<div class="m-charge-presets" id="mExchangePresets">'
      + [10, 30, 50, 100, 500, 1000, 5000, 10000].map(function (n) {
        return '<button type="button" class="m-charge-preset" data-exchange-amt="' + n + '">' + n + 'u</button>';
      }).join('')
      + '</div>'
      + '<div class="m-charge-row">'
      + '<label>' + esc(I18N().t('mgmtExchangeId')) + '</label>'
      + '<input type="text" class="m-charge-input" value="' + esc(uid) + '" readonly />'
      + '</div>'
      + '<div class="m-charge-actions">'
      + '<button type="button" class="m-mgmt-btn m-mgmt-btn--ok" id="mExchangeSubmit">' + esc(I18N().t('mgmtExchangeDo')) + '</button>'
      + '<button type="button" class="m-mgmt-btn m-mgmt-btn--danger" id="mExchangeCancel">' + esc(I18N().t('mgmtCancel')) + '</button>'
      + '</div>'
      + '<h4 class="m-charge-hist-title">' + esc(I18N().t('mgmtExchangeHist')) + '</h4>'
      + '<div class="m-charge-table-wrap"><table class="m-charge-table"><thead><tr>'
      + '<th>' + esc(I18N().t('mgmtExchangeColAmount')) + '</th>'
      + '<th>' + esc(I18N().t('mgmtExchangeColState')) + '</th>'
      + '<th>' + esc(I18N().t('mgmtExchangeColReq')) + '</th>'
      + '<th>' + esc(I18N().t('mgmtExchangeColProc')) + '</th>'
      + '</tr></thead><tbody id="mExchangeHistBody">';
    if (!rows || !rows.length) {
      html += '<tr><td colspan="4" class="m-charge-empty">' + esc(I18N().t('mgmtHistEmpty')) + '</td></tr>';
    } else {
      for (var i = 0; i < rows.length; i++) {
        var r = rows[i] || {};
        html += '<tr>'
          + '<td>' + esc((Number(r.amount) || 0).toLocaleString()) + '</td>'
          + '<td><span class="m-charge-badge ' + chargeStateCls(r.state) + '">' + esc(chargeStateLabel(r.state)) + '</span></td>'
          + '<td>' + esc(r.requested_at || '-') + '</td>'
          + '<td>' + esc(r.processed_at || '-') + '</td>'
          + '</tr>';
      }
    }
    html += '</tbody></table></div></div>';
    return html;
  }

  function bindExchangeUi() {
    var amount = 0;
    var input = $('mExchangeAmount');
    function setAmount(n) {
      amount = Math.max(0, n | 0);
      if (input) input.value = amount > 0 ? String(amount) + 'u' : '0';
    }
    setAmount(0);
    $all('[data-exchange-amt]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        setAmount(amount + (parseInt(btn.getAttribute('data-exchange-amt'), 10) || 0));
      });
    });
    var cancel = $('mExchangeCancel');
    if (cancel) {
      cancel.addEventListener('click', function () { setAmount(0); });
    }
    var submit = $('mExchangeSubmit');
    if (submit) {
      submit.addEventListener('click', function () { doExchangeRequest(amount); });
    }
  }

  function fillExchangeHistory(rows) {
    var tbody = $('mExchangeHistBody');
    if (!tbody) return;
    if (!rows || !rows.length) {
      tbody.innerHTML = '<tr><td colspan="4" class="m-charge-empty">' + esc(I18N().t('mgmtHistEmpty')) + '</td></tr>';
      return;
    }
    var html = '';
    for (var i = 0; i < rows.length; i++) {
      var r = rows[i] || {};
      html += '<tr>'
        + '<td>' + esc((Number(r.amount) || 0).toLocaleString()) + '</td>'
        + '<td><span class="m-charge-badge ' + chargeStateCls(r.state) + '">' + esc(chargeStateLabel(r.state)) + '</span></td>'
        + '<td>' + esc(r.requested_at || '-') + '</td>'
        + '<td>' + esc(r.processed_at || '-') + '</td>'
        + '</tr>';
    }
    tbody.innerHTML = html;
  }

  function openExchange() {
    var mgmtPanel = $('mgmtPanel');
    if (mgmtPanel) mgmtPanel.hidden = true;
    showGrid(true);
    state.panel = 'exchange';
    if (typeof hooks.onNavigate === 'function') hooks.onNavigate('exchange');

    var body = $('exchangePageBody');
    if (!body) return;
    body.innerHTML = renderExchangeForm([]);
    bindExchangeUi();
    var hist = $('mExchangeHistBody');
    if (hist) {
      hist.innerHTML = '<tr><td colspan="4" class="m-charge-empty">' + esc(I18N().t('mgmtLoading')) + '</td></tr>';
    }

    if (typeof hooks.api !== 'function') {
      fillExchangeHistory([]);
      return;
    }
    hooks.api('exchange_list', { qs: '&limit=30' }).then(function (r) {
      if (state.panel !== 'exchange') return;
      var json = (r && r.json) || {};
      var rows = (json.status === 'success' && Array.isArray(json.data)) ? json.data : [];
      fillExchangeHistory(rows);
    }, function () {
      if (state.panel !== 'exchange') return;
      fillExchangeHistory([]);
    });
  }

  function closeExchangePage() {
    if (state.panel === 'exchange') state.panel = null;
    if (typeof hooks.onNavigate === 'function') hooks.onNavigate('mgmt');
  }

  function doExchangeRequest(amount) {
    amount = parseInt(amount, 10) || 0;
    if (amount < 1) {
      toast(I18N().t('mgmtExchangeNeedAmount'));
      return;
    }
    var bal = Number(state.member && state.member.balance) || 0;
    if (bal < amount) {
      toast(I18N().msg('BALANCE') || I18N().t('mgmtExchangeFail'));
      return;
    }
    if (!window.confirm(I18N().t('mgmtExchangeConfirm'))) return;
    if (typeof hooks.api !== 'function' || state.busy) return;
    state.busy = true;
    var btn = $('mExchangeSubmit');
    if (btn) btn.disabled = true;
    hooks.api('exchange_request', {
      body: { amount: amount },
    }).then(function (r) {
      state.busy = false;
      if (btn) btn.disabled = false;
      var json = (r && r.json) || {};
      if (json.status !== 'success') {
        var code = json.code || '';
        toast((code && I18N().msg(code)) || json.message || I18N().t('mgmtExchangeFail'));
        return;
      }
      toast(I18N().t('mgmtExchangeOkMsg'));
      openExchange();
    }, function () {
      state.busy = false;
      if (btn) btn.disabled = false;
      toast(I18N().t('mgmtExchangeFail'));
    });
  }

  function doChargeRequest(amount) {
    amount = parseInt(amount, 10) || 0;
    if (amount < 1) {
      toast(I18N().t('mgmtChargeNeedAmount'));
      return;
    }
    if (!window.confirm(I18N().t('mgmtChargeConfirm'))) return;
    if (typeof hooks.api !== 'function' || state.busy) return;
    state.busy = true;
    var btn = $('mChargeSubmit');
    if (btn) btn.disabled = true;
    hooks.api('charge_request', {
      body: { amount: amount, name: (state.member && state.member.uid) || '' },
    }).then(function (r) {
      state.busy = false;
      if (btn) btn.disabled = false;
      var json = (r && r.json) || {};
      if (json.status !== 'success') {
        var code = json.code || '';
        toast((code && I18N().msg(code)) || json.message || I18N().t('mgmtChargeFail'));
        return;
      }
      toast(I18N().t('mgmtChargeOkMsg'));
      openCharge();
    }, function () {
      state.busy = false;
      if (btn) btn.disabled = false;
      toast(I18N().t('mgmtChargeFail'));
    });
  }

  function doAccountRequest() {
    if (!window.confirm(I18N().t('mgmtAccountConfirm'))) return;
    if (typeof hooks.api !== 'function' || state.busy) return;
    state.busy = true;
    hooks.api('account_request', { body: {} }).then(function (r) {
      state.busy = false;
      var json = (r && r.json) || {};
      if (json.status !== 'success') {
        var code = json.code || '';
        toast((code && I18N().msg(code)) || json.message || I18N().t('mgmtAccountFail'));
        return;
      }
      toast(I18N().t('mgmtAccountOk'));
    }, function () {
      state.busy = false;
      toast(I18N().t('mgmtAccountFail'));
    });
  }

  function openPointConvert() {
    var mgmtPanel = $('mgmtPanel');
    if (mgmtPanel) mgmtPanel.hidden = true;
    showGrid(true);
    state.panel = 'point';
    if (typeof hooks.onNavigate === 'function') hooks.onNavigate('point');

    var body = $('pointPageBody');
    if (!body) return;
    body.innerHTML = renderPointForm([]);
    bindPointUi();
    var hist = $('mPointHistBody');
    if (hist) {
      hist.innerHTML = '<tr><td colspan="2" class="m-charge-empty">' + esc(I18N().t('mgmtLoading')) + '</td></tr>';
    }

    if (typeof hooks.api !== 'function') {
      fillPointHistory([]);
      return;
    }
    hooks.api('point_convert_list', { qs: '&limit=30' }).then(function (r) {
      if (state.panel !== 'point') return;
      var json = (r && r.json) || {};
      var rows = (json.status === 'success' && Array.isArray(json.data)) ? json.data : [];
      fillPointHistory(rows);
    }, function () {
      if (state.panel !== 'point') return;
      fillPointHistory([]);
    });
  }

  function closePointPage() {
    if (state.panel === 'point') state.panel = null;
    if (typeof hooks.onNavigate === 'function') hooks.onNavigate('mgmt');
  }

  function renderPointForm(rows) {
    var pt = Number(state.member && state.member.point) || 0;
    var html = ''
      + '<div class="m-charge m-point-page-inner">'
      + '<p class="m-point-have">' + esc(I18N().t('mgmtPointHave')) + ' <strong id="mPointHave">' + esc(fmtPoint(pt)) + '</strong></p>'
      + '<div class="m-charge-row m-point-amount-row">'
      + '<label>' + esc(I18N().t('mgmtPointAsk')) + '</label>'
      + '<div class="m-point-amount-wrap">'
      + '<input type="text" id="mPointAmount" class="m-charge-input" value="" inputmode="numeric" />'
      + '<button type="button" class="m-point-full" id="mPointFull">' + esc(I18N().t('mgmtPointFull')) + '</button>'
      + '</div></div>'
      + '<div class="m-charge-actions">'
      + '<button type="button" class="m-mgmt-btn m-mgmt-btn--ok" id="mPointSubmit">' + esc(I18N().t('mgmtPointSubmit')) + '</button>'
      + '<button type="button" class="m-mgmt-btn m-mgmt-btn--danger" id="mPointCancel">' + esc(I18N().t('mgmtCancel')) + '</button>'
      + '</div>'
      + '<div class="m-charge-table-wrap"><table class="m-charge-table"><thead><tr>'
      + '<th>' + esc(I18N().t('mgmtPointColAmt')) + '</th>'
      + '<th>' + esc(I18N().t('mgmtPointColProc')) + '</th>'
      + '</tr></thead><tbody id="mPointHistBody">';
    if (!rows || !rows.length) {
      html += '<tr><td colspan="2" class="m-charge-empty">' + esc(I18N().t('mgmtHistEmpty')) + '</td></tr>';
    } else {
      for (var i = 0; i < rows.length; i++) {
        var r = rows[i] || {};
        html += '<tr>'
          + '<td>' + esc((Number(r.amount) || 0).toLocaleString()) + '</td>'
          + '<td>' + esc(r.processed_at || '-') + '</td>'
          + '</tr>';
      }
    }
    html += '</tbody></table></div></div>';
    return html;
  }

  function bindPointUi() {
    var input = $('mPointAmount');
    var full = $('mPointFull');
    var cancel = $('mPointCancel');
    var submit = $('mPointSubmit');
    function havePt() {
      return Math.floor(Number(state.member && state.member.point) || 0);
    }
    function setAmount(n) {
      n = Math.max(0, Math.floor(Number(n) || 0));
      if (input) input.value = n > 0 ? String(n) : '';
    }
    if (full) {
      full.addEventListener('click', function () { setAmount(havePt()); });
    }
    if (cancel) {
      cancel.addEventListener('click', function () { setAmount(0); });
    }
    if (submit) {
      submit.addEventListener('click', function () {
        var amt = parseInt((input && input.value) || '0', 10) || 0;
        doPointConvert(amt);
      });
    }
    if (input) {
      input.addEventListener('input', function () {
        var v = String(input.value || '').replace(/[^\d]/g, '');
        input.value = v;
      });
    }
  }

  function fillPointHistory(rows) {
    var tbody = $('mPointHistBody');
    if (!tbody) return;
    if (!rows || !rows.length) {
      tbody.innerHTML = '<tr><td colspan="2" class="m-charge-empty">' + esc(I18N().t('mgmtHistEmpty')) + '</td></tr>';
      return;
    }
    var html = '';
    for (var i = 0; i < rows.length; i++) {
      var r = rows[i] || {};
      html += '<tr>'
        + '<td>' + esc((Number(r.amount) || 0).toLocaleString()) + '</td>'
        + '<td>' + esc(r.processed_at || '-') + '</td>'
        + '</tr>';
    }
    tbody.innerHTML = html;
  }

  function doPointConvert(amount) {
    amount = parseInt(amount, 10) || 0;
    if (amount < 1) {
      toast(I18N().t('mgmtPointNeedAmount'));
      return;
    }
    var have = Math.floor(Number(state.member && state.member.point) || 0);
    if (have <= 0) {
      toast(I18N().msg('NO_POINT') || I18N().t('mgmtNoPoint'));
      return;
    }
    if (amount > have) {
      toast(I18N().msg('BALANCE') || I18N().t('mgmtConvertFail'));
      return;
    }
    if (!window.confirm(I18N().t('mgmtPointConvertConfirm'))) return;
    if (typeof hooks.api !== 'function' || state.busy) return;
    state.busy = true;
    var btn = $('mPointSubmit');
    if (btn) btn.disabled = true;
    hooks.api('point_convert', { body: { amount: amount } }).then(function (r) {
      state.busy = false;
      if (btn) btn.disabled = false;
      var json = (r && r.json) || {};
      if (json.status !== 'success') {
        var code = json.code || '';
        toast((code && I18N().msg(code)) || json.message || I18N().t('mgmtConvertFail'));
        return;
      }
      var d = json.data || {};
      if (!state.member) state.member = {};
      if (d.balance != null) state.member.balance = d.balance;
      if (d.point != null) state.member.point = d.point;
      renderProfile();
      if (typeof hooks.onBalance === 'function') {
        hooks.onBalance({ balance: state.member.balance, point: state.member.point });
      }
      toast(I18N().t('mgmtConvertOk'));
      openPointConvert();
    }, function () {
      state.busy = false;
      if (btn) btn.disabled = false;
      toast(I18N().t('mgmtConvertFail'));
    });
  }

  function histRowHtml(row) {
    var st = stateMeta(row.state);
    var roundTxt = I18N().formatRoundWithDay
      ? I18N().formatRoundWithDay(row.round, true)
      : String(row.round || '');
    var winExtra = (parseInt(row.state, 10) === 3 && row.win_amount)
      ? ' / +' + fmtMoney(row.win_amount)
      : '';
    return ''
      + '<li class="m-mgmt-hist-row">'
      + '<div class="m-mgmt-hist-main">' + esc(modeLabel(row.mode)) + '</div>'
      + '<div class="m-mgmt-hist-amt">' + esc(fmtMoney(row.amount)) + winExtra + '</div>'
      + '<div class="m-mgmt-hist-sub">' + esc(roundTxt)
      + (row.created_at ? ' · ' + esc(row.created_at) : '')
      + '</div>'
      + '<div class="m-mgmt-hist-state ' + st.cls + '">' + esc(I18N().t(st.key) || '') + '</div>'
      + '</li>';
  }

  function betResultMeta(st) {
    st = parseInt(st, 10) || 0;
    if (st === 1) return { cls: 'is-wait', key: 'mgmtStateWait' };
    if (st === 2) return { cls: 'is-lose', key: 'mgmtBetMiss' };
    if (st === 3) return { cls: 'is-win', key: 'mgmtBetHit' };
    if (st === 4) return { cls: 'is-cancel', key: 'mgmtStateCancel' };
    return { cls: '', key: '' };
  }

  function betsLiveModeHtml(mode) {
    var m = parseInt(mode, 10) || 0;
    var t = function (k) { return I18N().t(k); };
    var P = t('markP') || 'P';
    var B = t('markB') || 'B';
    var pSpan = '<span class="m-bets-mark m-bets-mark--p">' + esc(P) + '</span>';
    var bSpan = '<span class="m-bets-mark m-bets-mark--b">' + esc(B) + '</span>';
    var r1 = esc(t('room1Title'));
    var r2 = esc(t('room2Title'));
    var r3 = esc(t('room3Title'));
    var r4 = esc(t('room4Title'));
    // Combo: 제1번방P+제2번방P
    var combo = {
      5: r1 + pSpan + '+' + r2 + pSpan,
      6: r1 + bSpan + '+' + r2 + pSpan,
      7: r1 + pSpan + '+' + r2 + bSpan,
      8: r1 + bSpan + '+' + r2 + bSpan,
      13: r3 + pSpan + '+' + r4 + pSpan,
      14: r3 + bSpan + '+' + r4 + pSpan,
      15: r3 + pSpan + '+' + r4 + bSpan,
      16: r3 + bSpan + '+' + r4 + bSpan,
    };
    if (combo[m]) return combo[m];
    var single = {
      1: r1 + pSpan,
      2: r1 + bSpan,
      3: r2 + pSpan,
      4: r2 + bSpan,
      9: r3 + pSpan,
      10: r3 + bSpan,
      11: r4 + pSpan,
      12: r4 + bSpan,
    };
    if (single[m]) return single[m];
    return esc(modeLabel(m));
  }

  /** HTML for 선택 column (number modes → yellow 3D ball). */
  function betsLivePickHtml(row) {
    var m = parseInt(row.mode, 10) || 0;
    var ratio = Number(row.ratio);
    var rTxt = ratio > 0 ? ' [' + (Math.round(ratio * 100) / 100).toString() + ']' : '';
    if (m >= 30 && m <= 39) {
      var n = m - 30;
      return '<span class="m-bets-pick-inner">'
        + '<span class="m-bets-pb-ball" aria-label="' + n + '">' + n + '</span>'
        + '<span class="m-bets-pick-ratio">' + esc(rTxt) + '</span>'
        + '</span>';
    }
    return '<span class="m-bets-pick-inner">'
      + betsLiveModeHtml(m)
      + (rTxt ? '<span class="m-bets-pick-ratio">' + esc(rTxt) + '</span>' : '')
      + '</span>';
  }

  function stopBetsLivePoll() {
    if (state.liveTimer) {
      clearInterval(state.liveTimer);
      state.liveTimer = null;
    }
  }

  function renderBetsLiveTable(rows) {
    var html = ''
      + '<div class="m-bets-table-wrap"><table class="m-bets-table"><thead><tr>'
      + '<th>' + esc(I18N().t('mgmtBetColRound')) + '</th>'
      + '<th>' + esc(I18N().t('mgmtBetColPick')) + '</th>'
      + '<th>' + esc(I18N().t('mgmtBetColAmount')) + '</th>'
      + '<th>' + esc(I18N().t('mgmtBetColWin')) + '</th>'
      + '<th>' + esc(I18N().t('mgmtBetColResult')) + '</th>'
      + '</tr></thead><tbody>';
    if (!rows || !rows.length) {
      html += '<tr><td colspan="5" class="m-charge-empty">' + esc(I18N().t('mgmtHistEmpty')) + '</td></tr>';
    } else {
      var prevRound = null;
      var stripeDark = false;
      for (var i = 0; i < rows.length; i++) {
        var r = rows[i] || {};
        var round = parseInt(r.round, 10) || 0;
        // Number only: 1625971(204) — no trailing "회차"
        var roundTxt = I18N().formatRoundWithDay
          ? I18N().formatRoundWithDay(round, false)
          : String(round);
        var st = betResultMeta(r.state);
        var winAmt = parseInt(r.state, 10) === 3 ? (Number(r.win_amount) || 0) : 0;
        if (prevRound != null && prevRound !== round) {
          stripeDark = !stripeDark;
        }
        var trCls = stripeDark ? 'm-bets-row--dark' : 'm-bets-row--light';
        if (prevRound != null && prevRound !== round) trCls += ' m-bets-sep';
        html += '<tr class="' + trCls + '">'
          + '<td class="m-bets-round" data-round="' + round + '">' + esc(roundTxt) + '</td>'
          + '<td class="m-bets-pick">' + betsLivePickHtml(r) + '</td>'
          + '<td class="m-bets-amt">' + esc(fmtMoney(r.amount)) + '</td>'
          + '<td class="m-bets-win">' + esc(fmtMoney(winAmt)) + '</td>'
          + '<td><span class="m-bets-badge ' + st.cls + '">' + esc(I18N().t(st.key) || '-') + '</span></td>'
          + '</tr>';
        prevRound = round;
      }
    }
    html += '</tbody></table></div>';
    return html;
  }

  function loadBetsLive(silent) {
    var body = $('betsLivePageBody');
    if (!body) return;
    if (!silent) {
      body.innerHTML = '<p class="m-mgmt-loading">' + esc(I18N().t('mgmtLoading')) + '</p>';
    }
    if (typeof hooks.api !== 'function') {
      body.innerHTML = renderBetsLiveTable([]);
      return;
    }
    hooks.api('history', { qs: '&limit=50' }).then(function (r) {
      if (state.panel !== 'betsLive') return;
      var json = (r && r.json) || {};
      var rows = (json.status === 'success' && Array.isArray(json.data)) ? json.data : [];
      body.innerHTML = renderBetsLiveTable(rows);
    }, function () {
      if (state.panel !== 'betsLive') return;
      if (!silent) body.innerHTML = renderBetsLiveTable([]);
    });
  }

  function openBetsLive() {
    var mgmtPanel = $('mgmtPanel');
    if (mgmtPanel) mgmtPanel.hidden = true;
    showGrid(true);
    state.panel = 'betsLive';
    if (typeof hooks.onNavigate === 'function') hooks.onNavigate('betsLive');
    stopBetsLivePoll();
    loadBetsLive(false);
    state.liveTimer = setInterval(function () {
      var page = $('pageBetsLive');
      if (state.panel !== 'betsLive' || (page && page.hidden)) {
        stopBetsLivePoll();
        return;
      }
      loadBetsLive(true);
    }, 5000);
  }

  function closeBetsLivePage() {
    stopBetsLivePoll();
    if (state.panel === 'betsLive') state.panel = null;
    if (typeof hooks.onNavigate === 'function') hooks.onNavigate('mgmt');
  }

  function openHistory(kind) {
    var titleKey = kind === 'wins' ? 'mgmtWins' : 'mgmtBetsAll';
    state.panel = 'hist:' + kind;
    openPanel(I18N().t(titleKey), '<p class="m-mgmt-loading">' + esc(I18N().t('mgmtLoading')) + '</p>');
    if (typeof hooks.api !== 'function') return;
    hooks.api('history', { qs: '&limit=50' }).then(function (r) {
      if (state.panel !== 'hist:' + kind) return;
      var json = (r && r.json) || {};
      if (json.status !== 'success') {
        openPanel(I18N().t(titleKey), '<p class="m-mgmt-empty">' + esc(I18N().t('mgmtLoadFail')) + '</p>');
        return;
      }
      var rows = (json.data || []).slice();
      if (kind === 'wins') {
        rows = rows.filter(function (x) { return parseInt(x.state, 10) === 3; });
      }
      if (!rows.length) {
        openPanel(I18N().t(titleKey), '<p class="m-mgmt-empty">' + esc(I18N().t('mgmtHistEmpty')) + '</p>');
        return;
      }
      var html = '<ul class="m-mgmt-hist">';
      for (var i = 0; i < rows.length; i++) html += histRowHtml(rows[i] || {});
      html += '</ul>';
      openPanel(I18N().t(titleKey), html);
    }, function () {
      if (state.panel !== 'hist:' + kind) return;
      openPanel(I18N().t(titleKey), '<p class="m-mgmt-empty">' + esc(I18N().t('mgmtLoadFail')) + '</p>');
    });
  }

  function onMenu(action) {
    if (action === 'charge') return openCharge();
    if (action === 'exchange') return openExchange();
    if (action === 'point') return openPointConvert();
    if (action === 'betsLive') return openBetsLive();
    if (action === 'betsAll') return openHistory('all');
    if (action === 'wins') return openHistory('wins');
    if (action === 'inquiry') return openSoon('mgmtInquiry');
    if (action === 'notice') return openSoon('mgmtNotice');
    if (action === 'logout') {
      if (typeof hooks.logout === 'function') hooks.logout();
      return;
    }
  }

  function bind() {
    $all('[data-mgmt]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        onMenu(btn.getAttribute('data-mgmt'));
      });
    });
    var back = $('mgmtPanelBack');
    if (back) back.addEventListener('click', closePanel);
    var chargeBack = $('chargePageBack');
    var chargeClose = $('chargePageClose');
    if (chargeBack) chargeBack.addEventListener('click', closeChargePage);
    if (chargeClose) chargeClose.addEventListener('click', closeChargePage);
    var exchangeBack = $('exchangePageBack');
    var exchangeClose = $('exchangePageClose');
    if (exchangeBack) exchangeBack.addEventListener('click', closeExchangePage);
    if (exchangeClose) exchangeClose.addEventListener('click', closeExchangePage);
    var pointBack = $('pointPageBack');
    var pointClose = $('pointPageClose');
    if (pointBack) pointBack.addEventListener('click', closePointPage);
    if (pointClose) pointClose.addEventListener('click', closePointPage);
    var betsLiveBack = $('betsLivePageBack');
    var betsLiveClose = $('betsLivePageClose');
    if (betsLiveBack) betsLiveBack.addEventListener('click', closeBetsLivePage);
    if (betsLiveClose) betsLiveClose.addEventListener('click', closeBetsLivePage);
  }

  function sync(member) {
    if (member) {
      state.member = {
        uid: member.uid || member.name || (state.member && state.member.uid) || '',
        name: member.name || (state.member && state.member.name) || '',
        balance: member.balance != null ? member.balance : (state.member && state.member.balance) || 0,
        point: member.point != null ? member.point : (state.member && state.member.point) || 0,
      };
    }
    renderProfile();
  }

  function applyI18n() {
    var root = $('pageMgmt');
    if (root) I18N().applyStatic(root);
    var chargeRoot = $('pageCharge');
    if (chargeRoot) I18N().applyStatic(chargeRoot);
    var exchangeRoot = $('pageExchange');
    if (exchangeRoot) I18N().applyStatic(exchangeRoot);
    var pointRoot = $('pagePoint');
    if (pointRoot) I18N().applyStatic(pointRoot);
    var betsLiveRoot = $('pageBetsLive');
    if (betsLiveRoot) I18N().applyStatic(betsLiveRoot);
    renderProfile();
    if (state.panel === 'point') openPointConvert();
    else if (state.panel === 'charge') openCharge();
    else if (state.panel === 'exchange') openExchange();
    else if (state.panel === 'betsLive') openBetsLive();
    else if (state.panel === 'soon') {
      /* leave soon panel; title already set */
    } else if (state.panel && String(state.panel).indexOf('hist:') === 0) {
      openHistory(String(state.panel).slice(5));
    } else {
      closePanel();
    }
  }

  function show() {
    // Returning to mgmt grid (nav / back) — leave charge/exchange/point/betsLive
    stopBetsLivePoll();
    if (
      state.panel === 'charge'
      || state.panel === 'exchange'
      || state.panel === 'point'
      || state.panel === 'betsLive'
    ) {
      state.panel = null;
    }
    closePanel();
    renderProfile();
  }

  window.PBGM_Mgmt = {
    bind: bind,
    sync: sync,
    show: show,
    applyI18n: applyI18n,
    stopLive: stopBetsLivePoll,
    onToast: function (fn) { hooks.toast = fn; },
    onApi: function (fn) { hooks.api = fn; },
    onLogout: function (fn) { hooks.logout = fn; },
    onBalance: function (fn) { hooks.onBalance = fn; },
    onNavigate: function (fn) { hooks.onNavigate = fn; },
  };
})();
