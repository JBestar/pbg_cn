/**
 * Mobile betting board. Uses existing api/bet modes 1-16 and 30-39.
 */
(function () {
  'use strict';

  var AMOUNTS = [5, 10, 50, 100, 500, 1000];

  /* Same mapping as api pbg_mode_meta: combo target = [left room][right room] */
  var MODE_INFO = {
    1: { rooms: [1], marks: 'P' },
    2: { rooms: [1], marks: 'B' },
    3: { rooms: [2], marks: 'P' },
    4: { rooms: [2], marks: 'B' },
    9: { rooms: [3], marks: 'P' },
    10: { rooms: [3], marks: 'B' },
    11: { rooms: [4], marks: 'P' },
    12: { rooms: [4], marks: 'B' },
    5: { rooms: [1, 2], marks: 'PP' },
    6: { rooms: [1, 2], marks: 'BP' },
    7: { rooms: [1, 2], marks: 'PB' },
    8: { rooms: [1, 2], marks: 'BB' },
    13: { rooms: [3, 4], marks: 'PP' },
    14: { rooms: [3, 4], marks: 'BP' },
    15: { rooms: [3, 4], marks: 'PB' },
    16: { rooms: [3, 4], marks: 'BB' },
  };

  /* After a successful bet, ignore an older (smaller) status total for this long */
  var TOTAL_HOLD_MS = 2500;

  /* Keep the closed mini view up after betting reopens so the last ball can finish */
  var VEIL_HOLD_MS = 4000;

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
    roundTotal: 0,
    totalHoldUntil: 0,
    veilOn: false,
    veilTimer: 0,
  };

  var hooks = {
    placeBet: null,
    toast: null,
    onBalance: null,
    onDraw: null,
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
    fitOptionFont();
  }

  var PICK_KEY = {
    1: ['betPbLabel', 'betOdd'],
    2: ['betPbLabel', 'betEven'],
    3: ['betPbLabel', 'betUnder'],
    4: ['betPbLabel', 'betOver'],
    5: ['betPbLabel', 'betOdd', 'betUnder'],
    6: ['betPbLabel', 'betEven', 'betUnder'],
    7: ['betPbLabel', 'betOdd', 'betOver'],
    8: ['betPbLabel', 'betEven', 'betOver'],
    9: ['betSumLabel', 'betOdd'],
    10: ['betSumLabel', 'betEven'],
    11: ['betSumLabel', 'betUnder'],
    12: ['betSumLabel', 'betOver'],
    13: ['betSumLabel', 'betOdd', 'betUnder'],
    14: ['betSumLabel', 'betEven', 'betUnder'],
    15: ['betSumLabel', 'betOdd', 'betOver'],
    16: ['betSumLabel', 'betEven', 'betOver'],
  };

  function modeLabel(mode) {
    var t = function (k) { return I18N().t(k); };
    if (mode >= 30 && mode <= 39) {
      return t('betPbLabel') + ' ' + (mode - 30);
    }
    var pair = PICK_KEY[mode];
    if (pair && pair.length === 3) return t(pair[0]) + ' ' + t(pair[1]) + '+' + t(pair[2]);
    if (pair) return t(pair[0]) + ' ' + t(pair[1]);
    var info = MODE_INFO[mode];
    if (!info) return '';
    var parts = [];
    for (var i = 0; i < info.rooms.length; i++) {
      var mk = info.marks.charAt(i) === 'P' ? t('markP') : t('markB');
      parts.push(mk);
    }
    return parts.join('+');
  }

  function renderSelection() {
    $all('[data-bet-mode]').forEach(function (btn) {
      var mode = parseInt(btn.getAttribute('data-bet-mode'), 10);
      var active = state.mode === mode;
      btn.classList.toggle('is-active', active);
      var amt = btn.querySelector('[data-role="amt"]');
      if (amt) amt.textContent = active && state.amount > 0 ? fmtMoney(state.amount) : '';
    });
    var label = $('betDraftLabel');
    if (label) {
      var money = fmtMoney(state.amount);
      label.textContent = state.amount > 0 ? (money + ' ' + I18N().t('betGoWord')) : money;
    }
    var place = $('betPlaceBtn');
    if (place) place.disabled = state.busy || !state.canBet;
    scheduleFit();
  }

  function renderTotal() {
    var el = $('betRoundTotal');
    if (el) el.textContent = fmtMoney(state.roundTotal);
    scheduleFit();
  }

  function renderWallet() {
    var m = state.member || {};
    var name = $('betUserName');
    var bal = $('betUserBalance');
    var pt = $('betUserPoint');
    if (name) name.textContent = (m.name || m.uid || '-') + (I18N().t('betHonorific') || '');
    if (bal) bal.textContent = fmtMoney(m.balance);
    if (pt) pt.textContent = (Number(m.point) || 0).toLocaleString();
    scheduleFit();
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
      renderVeil();
      scheduleFit();
      return;
    }
    countEl.classList.remove('is-closed');
    var sec = Math.max(0, state.remain | 0);
    var m = Math.floor(sec / 60);
    var s = sec % 60;
    countEl.textContent = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
    renderVeil();
    scheduleFit();
  }

  function renderVeil() {
    var veil = $('betVeil');
    if (!veil) return;
    var closed = !state.canBet && state.round > 0;
    if (closed) {
      clearTimeout(state.veilTimer);
      veil.hidden = false;
      veil.classList.remove('is-leave');
      state.veilOn = true;
      if (typeof hooks.onDraw === 'function') hooks.onDraw();
      return;
    }
    if (!state.veilOn) return;
    state.veilOn = false;
    clearTimeout(state.veilTimer);
    state.veilTimer = setTimeout(function () {
      if (state.veilOn) return;
      veil.classList.add('is-leave');
      state.veilTimer = setTimeout(function () {
        if (!state.veilOn) veil.hidden = true;
      }, 700);
    }, VEIL_HOLD_MS);
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

  /* Chinese results: 单/双/小/大 only. Lane color tells powerball from normal ball. */
  function zhResultChip(lane, kind, isP) {
    var txt;
    if (kind === 'oe') txt = isP ? I18N().t('betOdd') : I18N().t('betEven');
    else txt = isP ? I18N().t('betUnder') : I18N().t('betOver');
    return '<span class="m-bet-chip is-lane-' + lane + '">' + txt + '</span>';
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
    var zh = I18N().getLang() === 'zh';
    chips.innerHTML = ''
      + (zh ? zhResultChip('pb', 'oe', room1P) : roomChipHtml(1, room1P))
      + (zh ? zhResultChip('pb', 'uo', room2P) : roomChipHtml(2, room2P))
      + (zh ? zhResultChip('nb', 'oe', room3P) : roomChipHtml(3, room3P))
      + (zh ? zhResultChip('nb', 'uo', room4P) : roomChipHtml(4, room4P))
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
      if (kind === 'odd') mark.textContent = I18N().t('betOdd');
      else if (kind === 'even') mark.textContent = I18N().t('betEven');
      else if (kind === 'under') mark.textContent = I18N().t('betUnder');
      else if (kind === 'over') mark.textContent = I18N().t('betOver');
      else if (kind === 'ou') mark.textContent = I18N().t('betOdd') + '+' + I18N().t('betUnder');
      else if (kind === 'eu') mark.textContent = I18N().t('betEven') + '+' + I18N().t('betUnder');
      else if (kind === 'ob') mark.textContent = I18N().t('betOdd') + '+' + I18N().t('betOver');
      else if (kind === 'eb') mark.textContent = I18N().t('betEven') + '+' + I18N().t('betOver');
      else if (kind === 'p') mark.textContent = I18N().t('markP');
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
    renderTotal();
    renderSelection();
  }

  function selectMode(mode) {
    mode = parseInt(mode, 10);
    if (!mode || state.busy) return;
    if (state.mode === mode) {
      /* tapping the selected option again cancels the draft */
      resetDraft(true);
      return;
    }
    /* a new pick starts from zero so stakes do not carry across markets */
    state.mode = mode;
    state.amount = 0;
    renderSelection();
  }

  function addAmount(n) {
    n = parseInt(n, 10) || 0;
    if (n <= 0 || state.busy) return;
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

    var sentRound = state.round;
    state.busy = true;
    renderSelection();
    Promise.resolve(hooks.placeBet({
      mode: state.mode,
      amount: state.amount,
      round: sentRound,
    })).then(function (r) {
      state.busy = false;
      if (!r || !r.ok) {
        /* a draft for a finished round can never be placed */
        if (state.round !== sentRound) resetDraft(true);
        else renderSelection();
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
      if (r.roundTotal != null && (r.round == null || r.round === state.round)) {
        state.roundTotal = Number(r.roundTotal) || 0;
        state.totalHoldUntil = Date.now() + TOTAL_HOLD_MS;
        renderTotal();
      }
      resetDraft(true);
      toast(I18N().msg('betOk'));
    }, function () {
      state.busy = false;
      if (state.round !== sentRound) resetDraft(true);
      else renderSelection();
      toast(I18N().msg('betFail'));
    });
  }

  function sync(payload) {
    payload = payload || {};
    if (payload.round != null) {
      var nextRound = parseInt(payload.round, 10) || 0;
      if (state.round && nextRound && nextRound !== state.round) {
        state.totalHoldUntil = 0;
        if (!state.busy) {
          state.mode = null;
          state.amount = 0;
        }
      }
      state.round = nextRound;
    }
    if (payload.roundTotal != null) {
      var total = Number(payload.roundTotal) || 0;
      var holding = Date.now() < state.totalHoldUntil && total < state.roundTotal;
      if (!holding) state.roundTotal = total;
    }
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
    renderTotal();
    renderSelection();
    scheduleFit();
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

    var placeBtn = $('betPlaceBtn');
    if (placeBtn) placeBtn.addEventListener('click', placeBet);

    applyI18nStatic();
    fitAmountFont();
    window.addEventListener('resize', fitAmountFont);
    window.addEventListener('orientationchange', function () {
      setTimeout(fitAmountFont, 120);
    });
  }

  function fitTextSize(probe, text, maxSize, maxW, maxH) {
    var size = Math.max(8, Math.min(maxSize, maxH));
    probe.textContent = text || '0';
    while (size > 8) {
      probe.style.fontSize = size + 'px';
      if (probe.offsetWidth <= maxW && probe.offsetHeight <= maxH) break;
      size -= 1;
    }
    return size;
  }

  function fitOptionFont() {
    var buttons = document.querySelectorAll('#pageBet .m-bet-board .m-bet-opt');
    if (!buttons.length) return;
    var h = Infinity;
    var w = Infinity;
    var i;
    for (i = 0; i < buttons.length; i++) {
      if (buttons[i].clientHeight > 0 && buttons[i].clientHeight < h) h = buttons[i].clientHeight;
      if (buttons[i].clientWidth > 0 && buttons[i].clientWidth < w) w = buttons[i].clientWidth;
    }
    if (!(h < Infinity) || !(w > 8)) return;
    var sample = buttons[0];
    var cs = window.getComputedStyle(sample);
    var innerH = h - (parseFloat(cs.paddingTop) || 0) - (parseFloat(cs.paddingBottom) || 0);
    var innerW = w - (parseFloat(cs.paddingLeft) || 0) - (parseFloat(cs.paddingRight) || 0) - 2;
    if (innerH < 18 || innerW < 8) return;
    var avail = innerH;
    var markH = Math.floor(avail * 0.46);
    var oddsH = Math.floor(avail * 0.27);
    var amtH = Math.max(8, avail - markH - oddsH);
    var probe = document.createElement('span');
    probe.style.position = 'absolute';
    probe.style.left = '-9999px';
    probe.style.whiteSpace = 'nowrap';
    probe.style.lineHeight = '1';
    probe.style.fontFamily = cs.fontFamily;
    probe.style.fontWeight = '900';
    document.body.appendChild(probe);
    var widestOdds = '—';
    var oddsNodes = document.querySelectorAll('#pageBet .m-bet-board [data-role="odds"]');
    for (i = 0; i < oddsNodes.length; i++) {
      var t = (oddsNodes[i].textContent || '').trim();
      if (t && t.length > widestOdds.length) widestOdds = t;
    }
    var widestMark = '홀';
    var markNodes = document.querySelectorAll('#pageBet .m-bet-board [data-role="mark"]');
    probe.style.fontSize = '16px';
    var widestPx = 0;
    for (i = 0; i < markNodes.length; i++) {
      var mt = (markNodes[i].textContent || '').trim() || '0';
      probe.textContent = mt;
      if (probe.offsetWidth > widestPx) {
        widestPx = probe.offsetWidth;
        widestMark = mt;
      }
    }
    var markSize = fitTextSize(probe, widestMark, markH, innerW, markH);
    probe.style.fontWeight = '700';
    var oddsSize = fitTextSize(probe, widestOdds, oddsH, innerW, oddsH);
    probe.style.fontWeight = '800';
    var amtSize = fitTextSize(probe, '1,000u', amtH, innerW, amtH);
    document.body.removeChild(probe);
    var marks = document.querySelectorAll('#pageBet .m-bet-board [data-role="mark"]');
    var amts = document.querySelectorAll('#pageBet .m-bet-board [data-role="amt"]');
    for (i = 0; i < marks.length; i++) marks[i].style.fontSize = markSize + 'px';
    for (i = 0; i < oddsNodes.length; i++) oddsNodes[i].style.fontSize = oddsSize + 'px';
    for (i = 0; i < amts.length; i++) amts[i].style.fontSize = amtSize + 'px';
  }

  var fitQueued = false;

  function scheduleFit() {
    if (fitQueued) return;
    fitQueued = true;
    requestAnimationFrame(function () {
      fitQueued = false;
      fitAmountFont();
    });
  }

  function shrinkFont(el, size, maxW) {
    if (!el) return size;
    el.style.fontSize = size + 'px';
    var guard = 0;
    while (size > 8 && el.scrollWidth > maxW + 1 && guard < 48) {
      size -= 1;
      el.style.fontSize = size + 'px';
      guard += 1;
    }
    return size;
  }

  function fitChromeFont() {
    var live = document.querySelector('#pageBet .m-bet-live');
    var count = $('betLiveCount');
    var meta = live ? live.querySelector('.m-bet-live-meta') : null;
    if (live && count && live.clientHeight > 12) {
      var lcs = window.getComputedStyle(live);
      var liveH = live.clientHeight - (parseFloat(lcs.paddingTop) || 0) - (parseFloat(lcs.paddingBottom) || 0);
      var countSize = Math.max(8, Math.floor(liveH * 0.78));
      var countRoom = Math.max(24, live.clientWidth * 0.42);
      countSize = shrinkFont(count, countSize, countRoom);
      if (meta) {
        var metaSize = Math.max(8, Math.floor(countSize * 0.62));
        var metaRoom = Math.max(24, live.clientWidth - count.offsetWidth - 16);
        shrinkFont(meta, metaSize, metaRoom);
      }
    }

    var title = $('betPrevTitle');
    var chips = $('betPrevChips');
    var prev = document.querySelector('#pageBet .m-bet-prev');
    if (prev && title && chips && prev.clientHeight > 16) {
      var titleSize = Math.max(8, Math.floor(prev.clientHeight * 0.22));
      shrinkFont(title, titleSize, title.clientWidth);
      if (chips.clientHeight > 8) {
        var chipNodes = chips.querySelectorAll('.m-bet-chip, .m-bet-chip-empty');
        var chipSize = Math.max(8, chips.clientHeight);
        var n;
        var guard = 0;
        var chipFit = false;
        while (chipSize > 7 && guard < 48) {
          for (n = 0; n < chipNodes.length; n++) chipNodes[n].style.fontSize = chipSize + 'px';
          var tall = false;
          for (n = 0; n < chipNodes.length; n++) {
            if (chipNodes[n].offsetHeight + 3 > chips.clientHeight) tall = true;
          }
          if (!tall && chips.scrollWidth <= chips.clientWidth + 1) {
            chipFit = true;
            break;
          }
          chipSize -= 1;
          guard += 1;
        }
        if (!chipFit) {
          for (n = 0; n < chipNodes.length; n++) chipNodes[n].style.fontSize = chipSize + 'px';
        }
      }
    }

    var total = document.querySelector('#pageBet .m-bet-total');
    if (total && total.clientWidth > 8 && total.clientHeight > 8) {
      var maxW = total.clientWidth * 0.9;
      var maxH = total.clientHeight * 0.9;
      var totalSize = Math.max(8, Math.floor(maxH));
      var totalGuard = 0;
      var totalKids = total.children;
      while (totalSize > 6 && totalGuard < 80) {
        total.style.fontSize = totalSize + 'px';
        var textW = 0;
        var textH = 0;
        var ti;
        for (ti = 0; ti < totalKids.length; ti++) {
          textW += totalKids[ti].scrollWidth;
          if (totalKids[ti].offsetHeight > textH) textH = totalKids[ti].offsetHeight;
        }
        if (totalKids.length > 1) textW += (totalSize * 0.15) * (totalKids.length - 1);
        if (textW <= maxW + 1 && textH <= maxH + 1) break;
        totalSize -= 1;
        totalGuard += 1;
      }
      total.style.fontSize = totalSize + 'px';
    }

    var wallet = $('betWallet');
    if (wallet && wallet.clientHeight > 10) {
      var wcs = window.getComputedStyle(wallet);
      var walletH = wallet.clientHeight - (parseFloat(wcs.paddingTop) || 0) - (parseFloat(wcs.paddingBottom) || 0) - 2;
      var walletSize = Math.max(8, Math.floor(walletH * 0.9));
      var walletGuard = 0;
      wallet.style.fontSize = walletSize + 'px';
      while (walletSize > 6 && wallet.scrollWidth > wallet.clientWidth + 1 && walletGuard < 60) {
        walletSize -= 1;
        wallet.style.fontSize = walletSize + 'px';
        walletGuard += 1;
      }
    }

    var go = $('betPlaceBtn');
    var goAmt = $('betDraftLabel');
    if (go && goAmt && go.clientHeight > 10) {
      var goSize = Math.max(8, Math.floor(go.clientHeight * 0.62));
      shrinkFont(goAmt, goSize, go.clientWidth - 12);
    }
  }

  function fitAmountFont() {
    var btn = document.querySelector('#pageBet .m-bet-amt');
    if (btn && btn.clientWidth > 8) {
      var cs = window.getComputedStyle(btn);
      var inner = btn.clientWidth - (parseFloat(cs.paddingLeft) || 0) - (parseFloat(cs.paddingRight) || 0) - 2;
      if (inner > 8) {
        var probe = document.createElement('span');
        probe.textContent = '1000u';
        probe.style.position = 'absolute';
        probe.style.left = '-9999px';
        probe.style.whiteSpace = 'nowrap';
        probe.style.fontFamily = cs.fontFamily;
        probe.style.fontWeight = '800';
        probe.style.letterSpacing = cs.letterSpacing;
        document.body.appendChild(probe);
        var size = 8;
        var s;
        for (s = 48; s >= 8; s--) {
          probe.style.fontSize = s + 'px';
          if (probe.offsetWidth <= inner) {
            size = s;
            break;
          }
        }
        document.body.removeChild(probe);
        var line = btn.clientHeight - (parseFloat(cs.paddingTop) || 0) - (parseFloat(cs.paddingBottom) || 0) - 2;
        if (line > 8 && size > line) size = Math.floor(line);
        var nodes = document.querySelectorAll('#pageBet .m-bet-amt');
        for (var i = 0; i < nodes.length; i++) nodes[i].style.fontSize = size + 'px';
      }
    }
    fitOptionFont();
    fitChromeFont();
  }

  window.PBGM_Bet = {
    bind: bind,
    sync: sync,
    fitAmountFont: fitAmountFont,
    applyI18n: applyI18nStatic,
    reset: function () {
      state.round = 0;
      state.roundTotal = 0;
      state.totalHoldUntil = 0;
      renderTotal();
      resetDraft(true);
    },
    onPlaceBet: function (fn) { hooks.placeBet = fn; },
    onToast: function (fn) { hooks.toast = fn; },
    onBalance: function (fn) { hooks.onBalance = fn; },
    onDraw: function (fn) { hooks.onDraw = fn; },
    labelFor: modeLabel,
    AMOUNTS: AMOUNTS,
  };
})();
