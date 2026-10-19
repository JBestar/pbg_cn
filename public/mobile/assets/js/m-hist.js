/**
 * Betting history: current-round slips plus settled hit/miss, cancel before close.
 */
(function () {
  'use strict';

  var state = {
    rows: [],
    canBet: true,
    busy: false,
    loading: false,
    seq: 0,
  };

  var hooks = {
    api: null,
    toast: null,
    onBalance: null,
  };

  function I18N() { return window.PBGM_I18N; }
  function Bet() { return window.PBGM_Bet; }
  function $(id) { return document.getElementById(id); }

  function esc(s) {
    return String(s == null ? '' : s)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function fmtMoney(n) {
    var lang = I18N().getLang();
    var loc = lang === 'zh' ? 'zh-CN' : (lang === 'en' ? 'en-US' : 'ko-KR');
    return (Number(n) || 0).toLocaleString(loc, { maximumFractionDigits: 0 }) + 'u';
  }

  function toast(msg) {
    if (typeof hooks.toast === 'function') hooks.toast(msg);
  }

  function laneOf(mode) {
    mode = mode | 0;
    if (mode >= 9 && mode <= 16) return 'nb';
    return 'pb';
  }

  function pickText(row) {
    var bet = Bet();
    if (bet && typeof bet.labelFor === 'function') {
      var label = bet.labelFor(row.mode);
      if (label) return label;
    }
    return row.label || '';
  }

  function stateMeta(st) {
    if (st === 3) return { cls: 'is-hit', key: 'mgmtBetHit' };
    if (st === 2) return { cls: 'is-miss', key: 'mgmtBetMiss' };
    if (st === 4) return { cls: 'is-cancel', key: 'mgmtStateCancel' };
    return { cls: 'is-wait', key: 'mgmtStateWait' };
  }

  function render() {
    var body = $('histBody');
    var btn = $('histCancelBtn');
    if (btn) {
      btn.hidden = !state.canBet;
      btn.disabled = state.busy;
    }
    if (!body || state.loading) return;
    var rows = state.rows || [];
    if (!rows.length) {
      body.innerHTML = '<p class="m-hist-empty">' + esc(I18N().t('mgmtHistEmpty')) + '</p>';
      return;
    }
    var html = '';
    var lastRound = null;
    rows.forEach(function (r) {
      if (r.round !== lastRound) {
        if (lastRound != null) html += '</section>';
        lastRound = r.round;
        var title = I18N().formatRoundWithDay(r.round, true);
        html += '<section class="m-hist-round"><h3 class="m-hist-round-h">' + esc(title) + '</h3>';
      }
      var lane = laneOf(r.mode);
      var meta = stateMeta(r.state | 0);
      html += '<div class="m-hist-row">'
        + '<span class="m-hist-pick is-' + lane + '">' + esc(pickText(r)) + '</span>'
        + '<span class="m-hist-amt">' + esc(fmtMoney(r.amount)) + '</span>'
        + '<span class="m-hist-st ' + meta.cls + '">' + esc(I18N().t(meta.key)) + '</span>'
        + '</div>';
    });
    if (lastRound != null) html += '</section>';
    body.innerHTML = html;
  }

  function load() {
    var body = $('histBody');
    if (!body) return;
    if (typeof hooks.api !== 'function') {
      render();
      return;
    }
    var seq = ++state.seq;
    state.loading = true;
    body.innerHTML = '<p class="m-mgmt-loading">' + esc(I18N().t('mgmtLoading')) + '</p>';
    hooks.api('history', { qs: '&limit=80' }).then(function (r) {
      if (seq !== state.seq) return;
      state.loading = false;
      var json = (r && r.json) || {};
      state.rows = (json.status === 'success' && Array.isArray(json.data)) ? json.data : [];
      render();
    }, function () {
      if (seq !== state.seq) return;
      state.loading = false;
      state.rows = [];
      body.innerHTML = '<p class="m-hist-empty">' + esc(I18N().t('mgmtLoadFail')) + '</p>';
    });
  }

  function cancelOpen() {
    if (state.busy || !state.canBet) return;
    if (typeof hooks.api !== 'function') return;
    state.busy = true;
    render();
    hooks.api('cancel', { body: {} }).then(function (r) {
      state.busy = false;
      var json = (r && r.json) || {};
      if (json.status !== 'success') {
        var code = json.code || '';
        toast((code && I18N().msg(code)) || json.message || I18N().t('histCancelFail'));
        render();
        return;
      }
      var d = json.data || {};
      if (typeof hooks.onBalance === 'function') {
        hooks.onBalance({ balance: d.balance, point: d.point });
      }
      toast(I18N().msg('cancelOk') || I18N().t('histCancelOk'));
      load();
    }, function () {
      state.busy = false;
      toast(I18N().t('histCancelFail'));
      render();
    });
  }

  function bind() {
    var btn = $('histCancelBtn');
    if (btn && btn.dataset.bound !== '1') {
      btn.dataset.bound = '1';
      btn.addEventListener('click', cancelOpen);
    }
  }

  window.PBGM_Hist = {
    bind: bind,
    show: function () { load(); },
    sync: function (payload) {
      payload = payload || {};
      if (payload.canBet != null) state.canBet = !!payload.canBet;
      render();
    },
    applyI18n: function () { render(); },
    onApi: function (fn) { hooks.api = fn; },
    onToast: function (fn) { hooks.toast = fn; },
    onBalance: function (fn) { hooks.onBalance = fn; },
  };
})();
