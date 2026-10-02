/**
 * Mobile betting board. Uses existing api/bet modes 1-16 and 30-39.
 */
(function () {
  'use strict';

  var AMOUNTS = [10, 50, 100, 500, 1000];

  var state = {
    mode: null,
    amount: 0,
    round: 0,
    remain: 0,
    canBet: true,
    odds: {},
    member: { uid: '', name: '', balance: 0, point: 0 },
    lastDraw: null,
    busy: false,
  };

  var hooks = {
    placeBet: null,
    toast: null,
    onBalance: null,
  };

  function I18N() { return window.PBGM_I18N; }
  function $(id) { return document.getElementById(id); }
  function $all(sel, root) {
    return Array.prototype.slice.call((root || document).querySelectorAll(sel));
  }

  function fmtMoney(n) {
    var lang = I18N().getLang();
    var loc = lang === 'zh' ? 'zh-CN' : (lang === 'en' ? 'en-US' : 'ko-KR');
    return (Number(n) || 0).toLocaleString(loc, { maximumFractionDigits: 0 }) + 'u';
  }

  function fmtOdds(v) {
    if (v == null || v === '') return '—';
    var n = Number(v);
    if (!isFinite(n)) return '—';
    return (Math.round(n * 100) / 100).toString();
  }

  function markPair(a, b) {
    return I18N().t(a) + '+' + I18N().t(b);
  }

  function toast(msg) {
    if (typeof hooks.toast === 'function') hooks.toast(msg);
  }

  function renderOdds() {
    $all('[data-bet-mode]').forEach(function (btn) {
      var mode = parseInt(btn.getAttribute('data-bet-mode'), 10);
      var el = btn.querySelector('[data-role="odds"]');
      if (el) el.textContent = fmtOdds(state.odds[mode]);
    });
  }

  function renderSelection() {
    $all('[data-bet-mode]').forEach(function (btn) {
      var mode = parseInt(btn.getAttribute('data-bet-mode'), 10);
      var active = state.mode === mode;
      btn.classList.toggle('is-active', active);
      var amt = btn.querySelector('[data-role="amt"]');
      if (amt) amt.textContent = active && state.amount > 0 ? fmtMoney(state.amount) : '';
    });
    var draft = $('betDraftAmount');
    if (draft) {
      if (state.amount > 0) {
        draft.textContent = fmtMoney(state.amount);
        draft.hidden = false;
      } else {
        draft.textContent = '';
        draft.hidden = true;
      }
    }
    var place = $('betPlaceBtn');
    if (place) place.disabled = state.busy || !state.canBet;
  }

  function renderWallet() {
    var m = state.member || {};
    var name = $('betUserName');
    var bal = $('betUserBalance');
    var pt = $('betUserPoint');
    if (name) name.textContent = (m.name || m.uid || '-') + (I18N().t('betHonorific') || '');
    if (bal) bal.textContent = fmtMoney(m.balance);
    if (pt) pt.textContent = (Number(m.point) || 0).toLocaleString();
  }

  function renderLive() {
    var roundEl = $('betLiveRound');
    var countEl = $('betLiveCount');
    if (roundEl) {
      roundEl.textContent = state.round
        ? I18N().formatRoundWithDay(state.round, false)
        : '-';
    }
    if (!countEl) return;
    if (!state.canBet) {
      countEl.textContent = I18N().t('betClosed');
      countEl.classList.add('is-closed');
      return;
    }
    countEl.classList.remove('is-closed');
    var sec = Math.max(0, state.remain | 0);
    var m = Math.floor(sec / 60);
    var s = sec % 60;
    countEl.textContent = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
  }

  function markSpan(isP) {
    var cls = isP ? 'm-bet-mark-p' : 'm-bet-mark-b';
    var txt = isP ? I18N().t('markP') : I18N().t('markB');
    return '<span class="' + cls + '">' + txt + '</span>';
  }

  function roomChipHtml(roomN, isP) {
    var room = I18N().t('room' + roomN + 'Title') || ('#' + roomN);
    return '<span class="m-bet-chip ' + (isP ? 'is-p' : 'is-b') + '">'
      + room + ' ' + markSpan(isP)
      + '</span>';
  }

  function pbChipHtml(pb) {
    var n = parseInt(pb, 10);
    if (!isFinite(n) || n < 0) return '';
    return '<span class="m-bet-chip m-bet-chip-pb">'
      + '<span class="m-bet-pb-num">' + n + '</span>'
      + '</span>';
  }

  function renderPrev() {
    var title = $('betPrevTitle');
    var chips = $('betPrevChips');
    if (!title || !chips) return;
    var d = state.lastDraw;
    if (!d || !d.round) {
      title.textContent = I18N().t('betNoResult');
      chips.innerHTML = '<span class="m-bet-chip-empty">' + I18N().t('betNoResult') + '</span>';
      return;
    }
    title.textContent = I18N().formatRoundWithDay(d.round, true);
    var sk = String(d.pick_sprite_key || 'oouus');
    if (!/^[oeumsb]{5}$/.test(sk)) sk = 'oouus';
    /* pick_sprite_key: [pb_oe][sum_oe][pb_uo][sum_uo][size]
       room1=pb_oe, room2=pb_uo, room3=sum_oe, room4=sum_uo
       oe: o=P e=B | uo: u=P o=B */
    var room1P = sk.charAt(0) === 'o';
    var room3P = sk.charAt(1) === 'o';
    var room2P = sk.charAt(2) === 'u';
    var room4P = sk.charAt(3) === 'u';
    chips.innerHTML = ''
      + roomChipHtml(1, room1P)
      + roomChipHtml(2, room2P)
      + roomChipHtml(3, room3P)
      + roomChipHtml(4, room4P)
      + pbChipHtml(d.powerball);
  }

  function comboMarkHtml(leftP, rightP) {
    return markSpan(leftP) + '+' + markSpan(rightP);
  }

  function refreshMarks() {
    $all('[data-bet-mode]').forEach(function (btn) {
      var kind = btn.getAttribute('data-kind');
      var mark = btn.querySelector('[data-role="mark"]');
      if (!mark) return;
      if (kind === 'p') mark.textContent = I18N().t('markP');
      else if (kind === 'b') mark.textContent = I18N().t('markB');
      else if (kind === 'pp') mark.innerHTML = comboMarkHtml(true, true);
      else if (kind === 'pb') mark.innerHTML = comboMarkHtml(true, false);
      else if (kind === 'bp') mark.innerHTML = comboMarkHtml(false, true);
      else if (kind === 'bb') mark.innerHTML = comboMarkHtml(false, false);
    });
  }

  function applyI18nStatic() {
    var root = $('pageBet');
    if (!root) return;
    I18N().applyStatic(root);
    refreshMarks();
    renderPrev();
    renderLive();
    renderWallet();
    renderOdds();
    renderSelection();
  }

  function selectMode(mode) {
    mode = parseInt(mode, 10);
    if (!mode) return;
    if (state.mode === mode) {
      /* keep selection; amount still accumulates via chips */
    } else {
      state.mode = mode;
    }
    renderSelection();
  }

  function addAmount(n) {
    n = parseInt(n, 10) || 0;
    if (n <= 0) return;
    if (!state.mode) {
      toast(I18N().msg('selectMode') || I18N().t('selectMode'));
      return;
    }
    state.amount += n;
    renderSelection();
  }

  function resetDraft(silent) {
    state.mode = null;
    state.amount = 0;
    renderSelection();
    if (!silent) toast(I18N().msg('amountReset'));
  }

  function placeBet() {
    if (state.busy) return;
    if (!state.mode) {
      toast(I18N().msg('selectMode'));
      return;
    }
    if (!state.amount || state.amount <= 0) {
      toast(I18N().msg('selectAmount') || I18N().msg('NO_AMOUNT'));
      return;
    }
    if (!state.canBet) {
      toast(I18N().msg('CLOSED'));
      return;
    }
    if (typeof hooks.placeBet !== 'function') return;

    state.busy = true;
    renderSelection();
    Promise.resolve(hooks.placeBet({
      mode: state.mode,
      amount: state.amount,
      round: state.round,
    })).then(function (r) {
      state.busy = false;
      if (!r || !r.ok) {
        renderSelection();
        toast((r && r.message) || I18N().msg('betFail'));
        return;
      }
      if (r.balance != null || r.point != null) {
        state.member.balance = r.balance != null ? r.balance : state.member.balance;
        state.member.point = r.point != null ? r.point : state.member.point;
        renderWallet();
        if (typeof hooks.onBalance === 'function') {
          hooks.onBalance({ balance: state.member.balance, point: state.member.point });
        }
      }
      resetDraft(true);
      toast(I18N().msg('betOk'));
    }, function () {
      state.busy = false;
      renderSelection();
      toast(I18N().msg('betFail'));
    });
  }

  function sync(payload) {
    payload = payload || {};
    if (payload.round != null) state.round = payload.round;
    if (payload.remain != null) state.remain = payload.remain;
    if (payload.canBet != null) state.canBet = !!payload.canBet;
    if (payload.odds) state.odds = payload.odds;
    if (payload.member) {
      state.member = {
        uid: payload.member.uid || state.member.uid,
        name: payload.member.name || state.member.name,
        balance: payload.member.balance != null ? payload.member.balance : state.member.balance,
        point: payload.member.point != null ? payload.member.point : state.member.point,
      };
    }
    if (payload.lastDraw !== undefined) state.lastDraw = payload.lastDraw;
    renderLive();
    renderWallet();
    renderOdds();
    renderPrev();
    renderSelection();
  }

  function bind() {
    var root = $('pageBet');
    if (!root || root.dataset.bound === '1') return;
    root.dataset.bound = '1';

    root.addEventListener('click', function (ev) {
      var opt = ev.target.closest('[data-bet-mode]');
      if (opt && root.contains(opt)) {
        selectMode(opt.getAttribute('data-bet-mode'));
        return;
      }
      var amt = ev.target.closest('[data-bet-amount]');
      if (amt && root.contains(amt)) {
        addAmount(amt.getAttribute('data-bet-amount'));
        return;
      }
    });

    var resetBtn = $('betResetBtn');
    var placeBtn = $('betPlaceBtn');
    if (resetBtn) resetBtn.addEventListener('click', function () { resetDraft(false); });
    if (placeBtn) placeBtn.addEventListener('click', placeBet);

    applyI18nStatic();
  }

  window.PBGM_Bet = {
    bind: bind,
    sync: sync,
    applyI18n: applyI18nStatic,
    reset: function () { resetDraft(true); },
    onPlaceBet: function (fn) { hooks.placeBet = fn; },
    onToast: function (fn) { hooks.toast = fn; },
    onBalance: function (fn) { hooks.onBalance = fn; },
    AMOUNTS: AMOUNTS,
  };
})();
