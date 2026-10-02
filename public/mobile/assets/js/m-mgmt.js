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
    state.panel = 'point';
    var pt = Number(state.member && state.member.point) || 0;
    var html = ''
      + '<div class="m-mgmt-convert">'
      + '<p>' + esc(I18N().t('mgmtPointConvertHelp')) + '</p>'
      + '<p>' + esc(I18N().t('mgmtPoint')) + ': <strong id="mgmtConvertPoint">' + esc(fmtPoint(pt)) + 'u</strong></p>'
      + '<div class="m-mgmt-convert-actions">'
      + '<button type="button" class="m-mgmt-btn m-mgmt-btn--ghost" id="mgmtConvertCancel">' + esc(I18N().t('mgmtCancel')) + '</button>'
      + '<button type="button" class="m-mgmt-btn m-mgmt-btn--ok" id="mgmtConvertOk"' + (pt <= 0 ? ' disabled' : '') + '>'
      + esc(I18N().t('mgmtConvertDo')) + '</button>'
      + '</div></div>';
    openPanel(I18N().t('mgmtPointConvert'), html);
    var cancel = $('mgmtConvertCancel');
    var ok = $('mgmtConvertOk');
    if (cancel) cancel.addEventListener('click', closePanel);
    if (ok) ok.addEventListener('click', doPointConvert);
  }

  function doPointConvert() {
    if (state.busy) return;
    var pt = Number(state.member && state.member.point) || 0;
    if (pt <= 0) {
      toast(I18N().msg('NO_POINT') || I18N().t('mgmtNoPoint'));
      return;
    }
    if (!window.confirm(I18N().t('mgmtPointConvertConfirm'))) return;
    if (typeof hooks.api !== 'function') return;
    state.busy = true;
    var ok = $('mgmtConvertOk');
    if (ok) ok.disabled = true;
    hooks.api('point_convert', { body: {} }).then(function (r) {
      state.busy = false;
      var json = (r && r.json) || {};
      if (json.status !== 'success') {
        var code = json.code || '';
        toast((code && I18N().msg(code)) || json.message || I18N().t('mgmtConvertFail'));
        if (ok) ok.disabled = false;
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
      closePanel();
    }, function () {
      state.busy = false;
      if (ok) ok.disabled = false;
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

  function openHistory(kind) {
    var titleKey = kind === 'live' ? 'mgmtBetsLive' : (kind === 'wins' ? 'mgmtWins' : 'mgmtBetsAll');
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
      if (kind === 'live') {
        rows = rows.filter(function (x) { return parseInt(x.state, 10) === 1; });
      } else if (kind === 'wins') {
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
    if (action === 'exchange') return openSoon('mgmtExchange');
    if (action === 'point') return openPointConvert();
    if (action === 'betsLive') return openHistory('live');
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
    renderProfile();
    if (state.panel === 'point') openPointConvert();
    else if (state.panel === 'charge') openCharge();
    else if (state.panel === 'soon') {
      /* leave soon panel; title already set */
    } else if (state.panel && String(state.panel).indexOf('hist:') === 0) {
      openHistory(String(state.panel).slice(5));
    } else {
      closePanel();
    }
  }

  function show() {
    if (state.panel !== 'charge') closePanel();
    renderProfile();
  }

  window.PBGM_Mgmt = {
    bind: bind,
    sync: sync,
    show: show,
    applyI18n: applyI18n,
    onToast: function (fn) { hooks.toast = fn; },
    onApi: function (fn) { hooks.api = fn; },
    onLogout: function (fn) { hooks.logout = fn; },
    onBalance: function (fn) { hooks.onBalance = fn; },
    onNavigate: function (fn) { hooks.onNavigate = fn; },
  };
})();
