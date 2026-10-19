/**
 * Betting history: the commit-era live bet list (5s refresh), with a per-slip
 * cancel button on waiting bets of the open round until betting closes.
 */
(function () {
  'use strict';

  var POLL_MS = 5000;

  var state = {
    rows: [],
    loaded: false,
    canBet: true,
    round: 0,
    busyId: 0,
    seq: 0,
    timer: null,
  };

  var hooks = {
    api: null,
    toast: null,
    onBalance: null,
    close: null,
  };

  function I18N() { return window.PBGM_I18N; }
  function Mgmt() { return window.PBGM_Mgmt; }
  function $(id) { return document.getElementById(id); }

  function esc(s) {
    return String(s == null ? '' : s)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function toast(msg) {
    if (typeof hooks.toast === 'function') hooks.toast(msg);
  }

  function isVisible() {
    var page = $('pageHist');
    return !!page && !page.hidden;
  }

  function render() {
    var body = $('histBody');
    if (!body || !state.loaded) return;
    var mgmt = Mgmt();
    if (!mgmt || typeof mgmt.betsLiveTableHtml !== 'function') return;
    body.innerHTML = mgmt.betsLiveTableHtml(state.rows, {
      cancelRound: state.canBet ? state.round : 0,
      busyId: state.busyId,
    });
  }

  function load(silent) {
    var body = $('histBody');
    if (!body) return;
    if (!silent) {
      state.loaded = false;
      body.innerHTML = '<p class="m-mgmt-loading">' + esc(I18N().t('mgmtLoading')) + '</p>';
    }
    if (typeof hooks.api !== 'function') {
      state.rows = [];
      state.loaded = true;
      render();
      return;
    }
    var seq = ++state.seq;
    hooks.api('history', { qs: '&limit=50&hide_cancel=1' }).then(function (r) {
      if (seq !== state.seq) return;
      var json = (r && r.json) || {};
      var rows = (json.status === 'success' && Array.isArray(json.data)) ? json.data : [];
      state.rows = rows.filter(function (r) { return parseInt(r && r.state, 10) !== 4; });
      state.loaded = true;
      render();
    }, function () {
      if (seq !== state.seq || silent) return;
      state.rows = [];
      state.loaded = true;
      render();
    });
  }

  function stopPoll() {
    if (state.timer) {
      clearInterval(state.timer);
      state.timer = null;
    }
  }

  function startPoll() {
    stopPoll();
    state.timer = setInterval(function () {
      if (!isVisible()) {
        stopPoll();
        return;
      }
      if (!state.busyId) load(true);
    }, POLL_MS);
  }

  function cancelOne(betId) {
    if (!betId || state.busyId || !state.canBet) return;
    if (typeof hooks.api !== 'function') return;
    state.busyId = betId;
    render();
    hooks.api('cancel', { body: { bet_id: betId } }).then(function (r) {
      state.busyId = 0;
      var json = (r && r.json) || {};
      if (json.status !== 'success') {
        var code = json.code || '';
        toast((code && I18N().msg(code)) || json.message || I18N().t('histCancelFail'));
        load(true);
        return;
      }
      var d = json.data || {};
      if (typeof hooks.onBalance === 'function') {
        hooks.onBalance({ balance: d.balance, point: d.point });
      }
      toast(I18N().t('histCancelOneOk'));
      load(true);
    }, function () {
      state.busyId = 0;
      toast(I18N().t('histCancelFail'));
      render();
    });
  }

  function close() {
    stopPoll();
    if (typeof hooks.close === 'function') hooks.close();
  }

  function bind() {
    var body = $('histBody');
    if (body && body.dataset.bound !== '1') {
      body.dataset.bound = '1';
      body.addEventListener('click', function (ev) {
        var btn = ev.target.closest ? ev.target.closest('.m-bets-cancel') : null;
        if (!btn || btn.disabled) return;
        cancelOne(parseInt(btn.getAttribute('data-bet-id'), 10) || 0);
      });
    }
    ['histPageBack', 'histPageClose'].forEach(function (id) {
      var el = $(id);
      if (el && el.dataset.bound !== '1') {
        el.dataset.bound = '1';
        el.addEventListener('click', close);
      }
    });
  }

  window.PBGM_Hist = {
    bind: bind,
    show: function () {
      load(false);
      startPoll();
    },
    refresh: function () { load(true); },
    sync: function (payload) {
      payload = payload || {};
      var canBet = payload.canBet != null ? !!payload.canBet : state.canBet;
      var round = payload.round != null ? (parseInt(payload.round, 10) || 0) : state.round;
      if (canBet === state.canBet && round === state.round) return;
      state.canBet = canBet;
      state.round = round;
      render();
    },
    applyI18n: function () { render(); },
    onApi: function (fn) { hooks.api = fn; },
    onToast: function (fn) { hooks.toast = fn; },
    onBalance: function (fn) { hooks.onBalance = fn; },
    onClose: function (fn) { hooks.close = fn; },
  };
})();
