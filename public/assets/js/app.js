(function () {
  const I18N = window.PBG_I18N;
  const params = new URLSearchParams(location.search);
  const MACHINE = params.get('machine') || params.get('mid') || 'm01';
  const API_BASE = resolveApiBase();
  const TOKEN_KEY = 'pbg_token';

  const state = {
    token: localStorage.getItem(TOKEN_KEY) || '',
    mode: null,
    amount: 0,
    round: 0,
    canBet: true,
    balance: 0,
    odds: {},
    powerballBase: '',
    busy: false,
    remainSeconds: 999,
    lastFetchAt: 0,
    lastFetchRound: 0,
    lastBets: [],
    loggedIn: false,
  };

  function resolveApiBase() {
    if (window.PBG_API_BASE) return window.PBG_API_BASE.replace(/\/$/, '');
    const path = location.pathname.replace(/\/public\/.*$/, '').replace(/\/$/, '');
    return location.origin + path + '/api/index.php';
  }

  function $(id) { return document.getElementById(id); }

  function setToken(tok) {
    state.token = tok || '';
    if (tok) localStorage.setItem(TOKEN_KEY, tok);
    else localStorage.removeItem(TOKEN_KEY);
  }

  function showLogin(show) {
    const el = $('loginOverlay');
    if (!el) return;
    if (show) el.classList.remove('is-hidden');
    else el.classList.add('is-hidden');
  }

  function toast(msg, ms) {
    const el = $('toast');
    el.textContent = msg;
    el.hidden = false;
    clearTimeout(toast._t);
    toast._t = setTimeout(function () { el.hidden = true; }, ms || 2200);
  }

  function fmtMoney(n) {
    return Number(n || 0).toLocaleString('zh-CN', { maximumFractionDigits: 0 }) + 'u';
  }

  function fmtCountdown(sec) {
    sec = Math.max(0, sec | 0);
    const m = Math.floor(sec / 60);
    const s = sec % 60;
    return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
  }

  var lastUrgentSec = -1;
  var TICK_SFX_URL = 'assets/sounds/clock-tick.wav';

  /** Clock tick: normal speed for 10..6s, 2x for 5..1s. */
  function playTickSfx(playbackRate) {
    try {
      var a = new Audio(TICK_SFX_URL);
      a.playbackRate = playbackRate || 1;
      // Keep pitch natural-ish when speeding up (where supported)
      if (typeof a.preservesPitch === 'boolean') a.preservesPitch = false;
      a.volume = 0.85;
      var p = a.play();
      if (p && typeof p.catch === 'function') p.catch(function () {});
    } catch (err) {
      /* ignore */
    }
  }

  function updateSegCountdown(sec) {
    var el = $('countdown');
    if (!el) return;
    sec = Math.max(0, sec | 0);
    var m = Math.floor(sec / 60);
    var s = sec % 60;
    var chars = String(m).padStart(2, '0') + String(s).padStart(2, '0');
    var digits = el.querySelectorAll('.seg-digit');
    for (var i = 0; i < digits.length && i < 4; i++) {
      digits[i].setAttribute('data-d', chars.charAt(i));
    }
    el.setAttribute('aria-label', fmtCountdown(sec));

    var urgent = sec > 0 && sec <= 10;
    el.classList.toggle('is-urgent', urgent);
    if (urgent) {
      if (sec !== lastUrgentSec) {
        lastUrgentSec = sec;
        // 10~6: normal, 5~1: 2x
        var rate = sec <= 5 ? 2 : 1;
        playTickSfx(rate);
        el.classList.remove('is-urgent');
        void el.offsetWidth;
        el.classList.add('is-urgent');
      }
    } else {
      lastUrgentSec = -1;
    }
  }

  var lastDraftAmount = 0;
  var COIN_SFX_URL = 'assets/sounds/coin-insert.wav';
  var BET_SFX_URL = 'assets/sounds/bet-place.wav';

  function playSfx(url, volume) {
    try {
      var a = new Audio(url);
      a.volume = volume == null ? 0.9 : volume;
      var p = a.play();
      if (p && typeof p.catch === 'function') p.catch(function () {});
    } catch (err) {
      /* ignore */
    }
  }

  /** Play coin.wav on each fare/amount insert (allows overlap). */
  function playCoinSfx() {
    playSfx(COIN_SFX_URL, 0.9);
  }

  /** Play betting.wav when bet key is pressed. */
  function playBetSfx() {
    playSfx(BET_SFX_URL, 0.9);
  }

  function playAmountSound(prev, next) {
    try {
      if (next > prev) {
        playCoinSfx();
      }
    } catch (err) {
      /* ignore audio errors */
    }
  }

  function t() {
    return I18N.ui || {};
  }

  /** betting_icos.png: 160 cell, 16 gap → stride 176; sheet 2448×160 */
  var BET_ICO_CELL = 160;
  var BET_ICO_STRIDE = 176;
  var BET_ICO_SHEET_W = 2448;
  var BET_ICO = { NORMAL: 10, POWER: 11, UNDER: 12, OVER: 13 };

  /**
   * mode → sprite indices (홀=볼3, 짝=볼2, 언더/오버 단독=화살표1, 조합=볼+화살표)
   * 숫자 단독(0~9)은 idx 0~9 한 개.
   */
  function modeIconIndices(mode) {
    var m = Number(mode);
    if (!m && m !== 0) return [];
    /* 파워볼 숫자 0~9 → mode 30~39, 스프라이트 idx 0~9 */
    if (m >= 30 && m <= 39) return [m - 30];

    var ball = (m >= 1 && m <= 8) ? BET_ICO.POWER
      : (m >= 9 && m <= 16) ? BET_ICO.NORMAL
      : null;
    if (ball == null) return [];

    var spec = {
      1: { n: 3 }, 2: { n: 2 },
      3: { n: 1, uo: BET_ICO.UNDER }, 4: { n: 1, uo: BET_ICO.OVER },
      5: { n: 3, uo: BET_ICO.UNDER }, 6: { n: 2, uo: BET_ICO.UNDER },
      7: { n: 3, uo: BET_ICO.OVER }, 8: { n: 2, uo: BET_ICO.OVER },
      9: { n: 3 }, 10: { n: 2 },
      11: { n: 1, uo: BET_ICO.UNDER }, 12: { n: 1, uo: BET_ICO.OVER },
      13: { n: 3, uo: BET_ICO.UNDER }, 14: { n: 2, uo: BET_ICO.UNDER },
      15: { n: 3, uo: BET_ICO.OVER }, 16: { n: 2, uo: BET_ICO.OVER },
    }[m];
    if (!spec) return [];

    var out = [];
    var i;
    var n = spec.n || 0;
    for (i = 0; i < n; i++) out.push(ball);
    if (spec.uo != null) out.push(spec.uo);
    return out;
  }

  function paintBetIcon(el, idx, size) {
    var scale = size / BET_ICO_CELL;
    el.style.width = size + 'px';
    el.style.height = size + 'px';
    el.style.backgroundSize = (BET_ICO_SHEET_W * scale) + 'px ' + (BET_ICO_CELL * scale) + 'px';
    el.style.backgroundPosition = (-idx * BET_ICO_STRIDE * scale) + 'px 0';
    el.classList.toggle('is-normal', idx === BET_ICO.NORMAL);
  }

  function layoutModeIcons(indices) {
    var box = $('modeIcons');
    var slot = $('hudZoneMode') || (box && box.closest ? box.closest('.hud-zone-mode') : null);
    var labelEl = $('modeLabel');
    var ui = t();
    if (!box) return;

    box.innerHTML = '';
    if (!indices || !indices.length) {
      box.hidden = true;
      if (slot) slot.classList.remove('has-icons');
      if (labelEl) {
        labelEl.hidden = false;
        labelEl.textContent = ui.draftNone || '게임을 선택해주세요';
      }
      return;
    }

    box.hidden = false;
    if (slot) slot.classList.add('has-icons');
    if (labelEl) labelEl.hidden = true;

    /* 세로 = 칸 높이 기준, 간격 0; 4개일 때는 높이의 약 83%로 축소 */
    var maxH = Math.max(32, Math.floor((slot && slot.clientHeight) || box.clientHeight || 120));
    var maxW = Math.max(32, Math.floor((slot && slot.clientWidth) || box.clientWidth || 400));
    var n = indices.length;
    var size = Math.min(maxH, BET_ICO_CELL);
    if (n === 4) {
      size = Math.floor(size * 0.83);
    }
    if (n * size > maxW) {
      size = Math.floor(maxW / n);
    }
    size = Math.max(24, Math.min(size, maxH, BET_ICO_CELL));
    box.style.gap = '0';

    indices.forEach(function (idx) {
      var ico = document.createElement('i');
      ico.className = 'bet-ico';
      ico.setAttribute('aria-hidden', 'true');
      paintBetIcon(ico, idx, size);
      box.appendChild(ico);
    });
  }

  function updateModeSprite() {
    var indices = state.mode ? modeIconIndices(state.mode) : [];
    layoutModeIcons(indices);
    var box = $('modeIcons');
    if (box && indices.length) {
      box.title = I18N.modes[state.mode] || ('#' + state.mode);
    } else if (box) {
      box.removeAttribute('title');
    }
  }

  function updateOddsMeter() {
    var valEl = $('oddsValue');
    if (!valEl) return;

    if (!state.mode || !state.odds || state.odds[state.mode] == null) {
      valEl.textContent = '—';
      return;
    }

    var odds = Number(state.odds[state.mode]) || 0;
    valEl.textContent = odds.toFixed(2);
  }

  function updateDraft() {
    const amountEl = $('draftAmount');
    const nextText = fmtMoney(state.amount);
    const prevAmt = lastDraftAmount;
    const nextAmt = Number(state.amount) || 0;
    const changed = nextAmt !== prevAmt;
    if (amountEl) {
      amountEl.textContent = nextText;
      if (changed) {
        amountEl.classList.remove('is-pop');
        void amountEl.offsetWidth;
        amountEl.classList.add('is-pop');
        playAmountSound(prevAmt, nextAmt);
      }
    }
    lastDraftAmount = nextAmt;
    updateModeSprite();
    updateOddsMeter();
  }

  function updateHeaderUser(d) {
    var nick = ((d.machine && d.machine.name) || (d.member && d.member.name) || '').trim();
    var uid = (d.machine && d.machine.code) || (d.member && d.member.uid) || '';
    var bal = (d.machine && d.machine.balance != null)
      ? d.machine.balance
      : (d.member && d.member.balance);
    var point = (d.machine && d.machine.point != null)
      ? d.machine.point
      : (d.member && d.member.point);
    var nameEl = $('headerUserName');
    var balEl = $('headerBalance');
    var pointEl = $('headerPoint');
    if (nameEl) {
      var base = nick || uid || '';
      var honor = (t().nameHonorific != null) ? t().nameHonorific : '님';
      nameEl.textContent = base ? (base + honor) : '—';
    }
    if (balEl) balEl.textContent = fmtMoney(bal);
    if (pointEl) {
      var pNum = (point != null && point !== '')
        ? Number(point || 0).toLocaleString('zh-CN', { maximumFractionDigits: 0 })
        : '0';
      pointEl.textContent = 'p: ' + pNum;
    }
  }

  function showLoginErr(msg) {
    const err = $('loginErr');
    if (!err) return;
    err.textContent = msg || (t().loginFail || '로그인 실패');
    err.hidden = false;
  }

  function loginFailMessage(json) {
    const msg = (I18N && I18N.msg) ? I18N.msg : {};
    const ui = t();
    const code = json && json.code ? String(json.code) : '';
    if (code && msg[code]) return msg[code];
    if (json && json.message) return json.message;
    return ui.loginFail || '로그인 실패';
  }

  async function api(action, opts) {
    opts = opts || {};
    const url = API_BASE + '?action=' + encodeURIComponent(action) + (opts.qs || '');
    const init = { method: opts.method || 'GET', headers: {} };
    if (state.token) init.headers['X-PBG-Token'] = state.token;
    if (opts.body) {
      init.method = 'POST';
      init.headers['Content-Type'] = 'application/json';
      init.body = JSON.stringify(Object.assign({ machine: MACHINE, token: state.token }, opts.body));
    }
    let res;
    try {
      res = await fetch(url, init);
    } catch (e) {
      return { status: 'fail', code: 'NETWORK', message: (t().loginNetwork || '서버에 연결할 수 없습니다') };
    }
    let json;
    try {
      json = await res.json();
    } catch (e) {
      return { status: 'fail', code: 'BAD_RESPONSE', message: (t().loginNetwork || '서버 응답이 올바르지 않습니다') };
    }
    // Session expiry only — never treat login credential errors as forced logout
    if (action !== 'login' && json && (json.code === 'AUTH' || res.status === 401)) {
      const wasLoggedIn = state.loggedIn || !!state.token;
      setToken('');
      state.loggedIn = false;
      showLogin(true);
      if (wasLoggedIn) {
        showLoginErr(json.message || (t().loginSession || '세션이 만료되었습니다. 다시 로그인하세요'));
      }
    }
    return json;
  }

  async function doLogin() {
    const uid = ($('loginUid').value || '').trim();
    const pwd = $('loginPwd').value || '';
    const btn = $('loginBtn');
    const err = $('loginErr');
    if (err) err.hidden = true;
    if (!uid || !pwd) {
      showLoginErr(t().loginNeed || '아이디와 비밀번호를 입력하세요');
      return;
    }
    if (btn) btn.disabled = true;
    try {
      const json = await api('login', { body: { uid: uid, pwd: pwd, machine: MACHINE } });
      if (!json || json.status !== 'success') {
        showLoginErr(loginFailMessage(json || {}));
        return;
      }
      if (!json.data || !json.data.token) {
        showLoginErr(t().loginFail || '로그인 실패');
        return;
      }
      setToken(json.data.token);
      state.loggedIn = true;
      showLogin(false);
      toast((t().loginOk || '로그인') + ': ' + (json.data.member.name || json.data.member.uid));
      try {
        await afterLogin();
      } catch (e) {
        console.warn(e);
        toast(t().loginPartial || '로그인됨 (일부 데이터 로드 실패)');
      }
    } catch (e) {
      console.warn(e);
      showLoginErr((t().loginNetwork || '서버에 연결할 수 없습니다') + (e && e.message ? ': ' + e.message : ''));
    } finally {
      if (btn) btn.disabled = false;
    }
  }

  async function afterLogin() {
    await tick();
    await Promise.all([refreshDraws(), refreshPatternBox()]);
  }

  async function refreshStatus() {
    if (!state.token) return;
    const json = await api('status');
    if (json.status !== 'success') return;
    const d = json.data;
    state.loggedIn = true;
    state.balance = d.machine.balance;
    state.round = d.round.round;
    state.canBet = !!d.round.can_bet;
    state.odds = d.odds || {};
    state.powerballBase = d.powerball_base_url || '';
    const prevRemain = state.remainSeconds;
    state.remainSeconds = d.round.remain_seconds | 0;

    updateHeaderUser(d);
    $('roundNo').textContent = d.round.round;
    updateSegCountdown(d.round.remain_seconds);
    var rawTime = (d.round.server_time || '').replace('T', ' ');
    var dayEl = $('serverDateDay');
    var timeEl = $('serverDateTime');
    if (dayEl || timeEl) {
      var dayPart = rawTime.slice(0, 10) || '——————';
      var timePart = rawTime.length >= 16 ? rawTime.slice(11, 16) : '--:--';
      if (dayEl) dayEl.textContent = dayPart;
      if (timeEl) timeEl.textContent = timePart;
    } else if ($('serverDate')) {
      $('serverDate').textContent = rawTime.slice(0, 16);
    }

    const frame = $('miniFrame');
    if (state.powerballBase && !frame.dataset.loaded) {
      frame.src = state.powerballBase.replace(/\/?$/, '/') + '?view=powerballMiniView&openMini=1';
      frame.dataset.loaded = '1';
    }
    updateDraft();

    const crossedZero = prevRemain > 0 && state.remainSeconds === 0;
    const nearDraw = state.remainSeconds <= 5;
    if (crossedZero || nearDraw) {
      await maybeFetchDraw(crossedZero);
    }
  }

  async function maybeFetchDraw(force) {
    const now = Date.now();
    if (!force && now - state.lastFetchAt < 4000) return;
    state.lastFetchAt = now;
    try {
      const qs = force ? '&force=1' : '';
      const json = await api('fetch_draw', { qs: qs });
      if (json.status !== 'success') return;
      const draw = json.data && json.data.draw;
      const round = draw && draw.round ? draw.round : 0;
      if (round && round !== state.lastFetchRound) {
        state.lastFetchRound = round;
        await Promise.all([refreshDraws(), refreshPatternBox(), refreshBets()]);
      }
    } catch (err) {
      console.warn('fetch_draw', err);
    }
  }

  async function refreshDraws() {
    const json = await api('draws', { qs: '&limit=20' });
    if (json.status !== 'success') return;
    const newest = json.data && json.data.length ? Number(json.data[0].round) : 0;
    if (newest) {
      state.lastFetchRound = newest;
    }
    if (window.PBG_OwnerPick && typeof window.PBG_OwnerPick.render === 'function') {
      window.PBG_OwnerPick.render(
        json.data || [],
        json.next_round,
        json.next_date_label || '',
        null
      );
    }
  }

  async function fillPatternContent(elId, action) {
    const content = $(elId);
    if (!content) return;
    const ui = t();
    try {
      const lang = I18N.getLang ? I18N.getLang() : 'ko';
      /* latestLog: 날짜 제한 없이 최근 회차만 — 고정 폭 안 최신 열 표시 */
      const json = await api(action, {
        qs: '&lang=' + encodeURIComponent(lang)
          + '&mode=latestLog'
          + '&roundCnt=300',
      });
      if (json.status !== 'success') {
        content.innerHTML = '<div style="padding:12px;color:#969696;text-align:center;">'
          + (ui.patternFail || '') + '</div>';
        return;
      }
      content.innerHTML = json.content || '';
    } catch (err) {
      console.warn(action, err);
    }
  }

  async function refreshPatternBox() {
    await Promise.all([
      fillPatternContent('patternContent', 'pattern_pb_oddeven'),
      fillPatternContent('patternContentUo', 'pattern_pb_underover'),
      fillPatternContent('patternContentSumOe', 'pattern_sum_oddeven'),
      fillPatternContent('patternContentSumUo', 'pattern_sum_underover'),
    ]);
  }

  function renderBetRows(rows) {
    const body = $('betBody');
    const ui = t();
    if (!rows || !rows.length) {
      body.innerHTML = '<tr><td colspan="5" class="bet-empty">' + (ui.betEmpty || '') + '</td></tr>';
      return;
    }
    /* cab-bot bet area ≈ 10 data rows (no scroll) */
    if (rows.length > 10) {
      rows = rows.slice(0, 10);
    }
    let stripe = 0;
    let prevRound = null;
    body.innerHTML = rows.map(function (b) {
      const round = Number(b.round) || 0;
      if (prevRound !== null && round !== prevRound) {
        stripe ^= 1;
      }
      prevRound = round;
      const cls = b.state === 1 ? 'state-wait' : b.state === 3 ? 'state-win' : b.state === 2 ? 'state-lose' : 'state-cancel';
      const label = I18N.modes[b.mode] || b.label || '';
      const stateLabel = (I18N.states && I18N.states[b.state]) || b.state_label || '';
      return '<tr class="round-stripe-' + stripe + '">' +
        '<td>' + b.round + '</td>' +
        '<td title="' + String(label).replace(/"/g, '&quot;') + '">' + label + '</td>' +
        '<td>' + fmtMoney(b.amount) + '</td>' +
        '<td>' + fmtMoney(b.win_amount) + '</td>' +
        '<td><span class="' + cls + '">' + stateLabel + '</span></td>' +
        '</tr>';
    }).join('');
  }

  async function refreshBets() {
    const json = await api('history', { qs: '&limit=10' });
    if (json.status !== 'success') return;
    state.lastBets = json.data || [];
    renderBetRows(state.lastBets);
  }

  function apiErrorMsg(json, fallbackKey) {
    const msg = I18N.msg || {};
    const code = json && json.code ? String(json.code) : '';
    if (code && msg[code]) return msg[code];
    // Fallback: map known Chinese server strings → current lang
    const cnMap = {
      '当前期已封盘': 'CLOSED',
      '余额不足': 'BALANCE',
      '请选择金额': 'NO_AMOUNT',
      '期号已变更，请重试': 'ROUND',
      '无效投注项目': 'INVALID_MODE',
      '低于最小投注': 'MIN_BET',
      '超过最大投注': 'MAX_BET',
      '机器不存在': 'MACHINE_NOT_FOUND',
      '没有可取消的投注': 'NO_CANCEL',
    };
    const mapped = json && json.message ? cnMap[json.message] : '';
    if (mapped && msg[mapped]) return msg[mapped];
    return msg[fallbackKey] || (json && json.message) || msg.betFail || '';
  }

  async function placeBet() {
    if (state.busy) return;
    playBetSfx();
    if (!state.mode) {
      toast(I18N.msg.selectMode);
      return;
    }
    if (!state.amount || state.amount <= 0) {
      toast(I18N.msg.selectAmount);
      return;
    }
    state.busy = true;
    try {
      const json = await api('bet', {
        body: { mode: state.mode, amount: state.amount, round: state.round },
      });
      if (json.status !== 'success') {
        toast(apiErrorMsg(json, 'betFail'));
        return;
      }
      toast(I18N.msg.betOk);
      state.amount = 0;
      state.mode = null;
      updateDraft();
      await Promise.all([refreshStatus(), refreshBets()]);
    } finally {
      state.busy = false;
    }
  }

  async function cancelBets() {
    if (state.busy) return;
    state.mode = null;
    updateDraft();
    state.busy = true;
    try {
      const json = await api('cancel', { body: {} });
      if (json.status !== 'success') {
        toast(apiErrorMsg(json, 'modeCleared'));
        return;
      }
      toast(I18N.msg.cancelOk + ' ' + fmtMoney(json.data.refunded));
      await Promise.all([refreshStatus(), refreshBets()]);
    } finally {
      state.busy = false;
    }
  }

  function onKey(e) {
    if (!state.loggedIn || !state.token) return;
    const code = e.code;
    const ctrl = !!(e.ctrlKey || e.metaKey);

    if (code === 'KeyI') {
      e.preventDefault();
      if (ctrl) {
        I18N.setLang('ko');
      } else {
        I18N.cycleLangFromI();
      }
      toast(t().langSwitched || '');
      return;
    }

    if (I18N.keyMode[code]) {
      e.preventDefault();
      state.mode = I18N.keyMode[code];
      updateDraft();
      return;
    }
    if (code === 'KeyW') {
      e.preventDefault();
      state.amount += 10;
      updateDraft();
      return;
    }
    if (code === 'KeyX') {
      e.preventDefault();
      state.amount += 100;
      updateDraft();
      return;
    }
    if (code === 'KeyY') {
      e.preventDefault();
      state.amount += 500;
      updateDraft();
      return;
    }
    if (code === 'KeyJ') {
      e.preventDefault();
      if (state.amount !== 0) {
        state.amount = 0;
        updateDraft();
        toast((I18N.msg && I18N.msg.amountReset) || '금액이 초기화되었습니다');
      }
      return;
    }
    if (code === 'KeyZ') {
      e.preventDefault();
      placeBet();
      return;
    }
    if (code === 'Escape') {
      e.preventDefault();
      cancelBets();
    }
  }

  if (window.pbgElectron && window.pbgElectron.onKey) {
    window.pbgElectron.onKey(function (payload) {
      onKey({
        code: payload.code,
        ctrlKey: !!payload.ctrlKey,
        metaKey: !!payload.metaKey,
        preventDefault: function () {},
      });
    });
  }
  window.addEventListener('keydown', onKey);

  window.PBG_onLangChange = function (lang) {
    updateDraft();
    renderBetRows(state.lastBets);
    if (window.PBG_OwnerPick && typeof window.PBG_OwnerPick.rerender === 'function') {
      window.PBG_OwnerPick.rerender();
    }
    refreshPatternBox();
    const sel = $('loginLang');
    if (sel && lang) sel.value = lang;
  };

  async function tick() {
    try {
      if (!state.token) return;
      await refreshStatus();
      if (state.remainSeconds > 5) {
        await api('settle');
      }
      await refreshBets();
    } catch (err) {
      console.warn(err);
    }
  }

  async function boot() {
    if (I18N.applyStatic) I18N.applyStatic();
    updateDraft();
    var iconsBox = $('modeIcons');
    var zoneMode = $('hudZoneMode') || iconsBox;
    if (zoneMode && typeof ResizeObserver !== 'undefined') {
      var roTimer = null;
      new ResizeObserver(function () {
        if (roTimer) clearTimeout(roTimer);
        roTimer = setTimeout(function () {
          if (state.mode) updateModeSprite();
        }, 50);
      }).observe(zoneMode);
    }
    requestAnimationFrame(function () {
      if (state.mode) updateModeSprite();
    });
    const langSel = $('loginLang');
    if (langSel && I18N.getLang) {
      langSel.value = I18N.getLang();
      langSel.addEventListener('change', function () {
        if (I18N.setLang) I18N.setLang(langSel.value);
      });
    }
    $('loginBtn').addEventListener('click', doLogin);
    $('loginPwd').addEventListener('keydown', function (e) {
      if (e.key === 'Enter') doLogin();
    });
    $('loginUid').addEventListener('keydown', function (e) {
      if (e.key === 'Enter') $('loginPwd').focus();
    });

    if (state.token) {
      const me = await api('me');
      if (me.status === 'success') {
        state.loggedIn = true;
        showLogin(false);
        await afterLogin();
        setInterval(tick, 1000);
        return;
      }
    }
    showLogin(true);
    setInterval(function () {
      if (state.token && state.loggedIn) tick();
    }, 1000);
  }

  boot();
})();
