(function () {
  'use strict';

  var I18N = window.PBGM_I18N;
  var Nav = window.PBGM_Nav;
  var Draws = window.PBGM_Draws;
  var Bet = window.PBGM_Bet;
  var Mgmt = window.PBGM_Mgmt;
  var TOKEN_KEY = 'pbg_m_token';
  var REQUEST_TIMEOUT_MS = 10000;
  var API_BASE = resolveApiBase();
  var MINI_W = 830;
  var MINI_H = 273;

  var state = {
    token: readToken(),
    busy: false,
    lastErr: null,
    member: null,
    powerballBase: '',
    remainSeconds: 999,
    round: 0,
    canBet: true,
    odds: {},
    lastFetchAt: 0,
    lastFetchRound: 0,
    pollTimer: null,
    homeReady: false,
  };

  function $(id) { return document.getElementById(id); }

  function resolveApiBase() {
    if (window.PBG_API_BASE) return String(window.PBG_API_BASE).replace(/\/$/, '');
    var m = location.pathname.match(/^(.*)\/public\/mobile(?:\/|$)/);
    if (m) return location.origin + m[1] + '/api/index.php';
    return location.origin + '/api/index.php';
  }

  function readToken() {
    try { return localStorage.getItem(TOKEN_KEY) || ''; } catch (e) { return ''; }
  }

  function setToken(tok) {
    state.token = tok || '';
    try {
      if (tok) localStorage.setItem(TOKEN_KEY, tok);
      else localStorage.removeItem(TOKEN_KEY);
    } catch (e) { /* storage unavailable */ }
  }

  function api(action, opts) {
    opts = opts || {};
    var url = API_BASE + '?action=' + encodeURIComponent(action) + (opts.qs || '');
    var init = { method: 'GET', headers: {}, cache: 'no-store', credentials: 'omit' };
    if (opts.auth !== false && state.token) init.headers['X-PBG-Token'] = state.token;
    if (opts.body) {
      init.method = 'POST';
      init.headers['Content-Type'] = 'application/json';
      init.body = JSON.stringify(opts.body);
    }

    var controller = (typeof AbortController !== 'undefined') ? new AbortController() : null;
    if (controller) init.signal = controller.signal;
    var timedOut = false;
    var timer = setTimeout(function () {
      timedOut = true;
      if (controller) controller.abort();
    }, REQUEST_TIMEOUT_MS);

    var request = fetch(url, init).then(function (res) {
      return res.text().then(function (text) {
        var json = null;
        try { json = JSON.parse(text); } catch (e) { json = null; }
        if (!json || typeof json !== 'object') {
          return { httpStatus: res.status, json: { status: 'fail', code: 'BAD_RESPONSE' } };
        }
        return { httpStatus: res.status, json: json };
      });
    }, function () {
      return { httpStatus: 0, json: { status: 'fail', code: timedOut ? 'TIMEOUT' : 'NETWORK' } };
    });

    var timeout = new Promise(function (resolve) {
      setTimeout(function () {
        resolve({ httpStatus: 0, json: { status: 'fail', code: 'TIMEOUT' } });
      }, REQUEST_TIMEOUT_MS + 50);
    });

    return Promise.race([request, timeout]).then(function (r) {
      clearTimeout(timer);
      // Only bounce an already-running home session; restoreSession handles AUTH itself
      if (state.homeReady && action !== 'login' && action !== 'logout' && isAuthFailure(r) && state.token) {
        forceLogoutSession({ key: 'loginSession' });
      }
      return r;
    });
  }

  function isAuthFailure(r) {
    return r.httpStatus === 401 || (r.json && r.json.code === 'AUTH');
  }

  /* ---------- UI helpers ---------- */

  function toast(msg, ms) {
    var el = $('mToast');
    if (!el) return;
    el.textContent = msg || '';
    el.hidden = !msg;
    clearTimeout(toast._t);
    if (msg) {
      toast._t = setTimeout(function () { el.hidden = true; }, ms || 1800);
    }
  }

  function numberLocale() {
    var lang = I18N.getLang();
    if (lang === 'zh') return 'zh-CN';
    if (lang === 'en') return 'en-US';
    return 'ko-KR';
  }

  function fmtMoney(n) {
    return (Number(n) || 0).toLocaleString(numberLocale(), { maximumFractionDigits: 0 }) + 'u';
  }

  function fmtPoint(n) {
    return (Number(n) || 0).toLocaleString(numberLocale(), { maximumFractionDigits: 2 });
  }

  function fmtCountdown(sec) {
    sec = Math.max(0, sec | 0);
    var m = Math.floor(sec / 60);
    var s = sec % 60;
    return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
  }

  function showLoginShell(show) {
    $('loginShell').hidden = !show;
    $('appShell').hidden = show;
    Nav.show(!show);
  }

  function showPage(name) {
    $('pageHome').hidden = name !== 'home';
    $('pageBet').hidden = name !== 'bet';
    $('pageMgmt').hidden = name !== 'mgmt';
    var pageCharge = $('pageCharge');
    if (pageCharge) pageCharge.hidden = name !== 'charge';
    var pageExchange = $('pageExchange');
    if (pageExchange) pageExchange.hidden = name !== 'exchange';
    var pagePoint = $('pagePoint');
    if (pagePoint) pagePoint.hidden = name !== 'point';
    var pageBetsLive = $('pageBetsLive');
    if (pageBetsLive) pageBetsLive.hidden = name !== 'betsLive';
    var pageBetsAll = $('pageBetsAll');
    if (pageBetsAll) pageBetsAll.hidden = name !== 'betsAll';
  }

  function renderError() {
    var box = $('loginErr');
    var e = state.lastErr;
    if (!e) {
      box.hidden = true;
      $('loginErrText').textContent = '';
      return;
    }
    var text = (e.code && I18N.msg(e.code)) || (e.key && I18N.t(e.key)) || e.raw || I18N.t('loginFail');
    $('loginErrText').textContent = text;
    box.hidden = false;
  }

  function setError(err) {
    state.lastErr = err || null;
    renderError();
  }

  function setBusy(busy) {
    state.busy = busy;
    var btn = $('loginBtn');
    btn.disabled = busy;
    btn.classList.toggle('is-busy', busy);
    btn.setAttribute('aria-busy', busy ? 'true' : 'false');
  }

  function setPwdVisible(visible) {
    var input = $('loginPwd');
    var btn = $('togglePwd');
    input.type = visible ? 'text' : 'password';
    btn.setAttribute('aria-pressed', visible ? 'true' : 'false');
    btn.setAttribute('aria-label', I18N.t(visible ? 'ariaHidePwd' : 'ariaShowPwd'));
  }

  function applyPatternTitles() {
    var map = [
      ['patternTitleText', 1],
      ['patternTitleUoText', 2],
      ['patternTitleSumOeText', 3],
      ['patternTitleSumUoText', 4],
    ];
    map.forEach(function (pair) {
      var el = $(pair[0]);
      if (el) el.innerHTML = I18N.formatPatternTitleHtml(pair[1]);
    });
  }

  function scaleMini() {
    var wrap = $('miniWrap');
    var scale = $('miniScale');
    if (!wrap || !scale) return;
    var w = wrap.clientWidth || window.innerWidth || MINI_W;
    var s = w / MINI_W;
    scale.style.transform = 'scale(' + s + ')';
    wrap.style.height = Math.round(MINI_H * s) + 'px';
  }

  function updateHeaderUser(d) {
    var m = (d && d.member) || (d && d.machine) || state.member || {};
    state.member = {
      uid: m.uid || m.code || (state.member && state.member.uid) || '',
      name: m.name || (state.member && state.member.name) || '',
      balance: m.balance != null ? m.balance : (state.member && state.member.balance) || 0,
      point: m.point != null ? m.point : (state.member && state.member.point) || 0,
    };
    $('headerUserName').textContent = state.member.name || state.member.uid || '-';
    $('headerBalance').textContent = fmtMoney(state.member.balance);
    $('headerPoint').textContent = fmtPoint(state.member.point);
    if (Mgmt && typeof Mgmt.sync === 'function') Mgmt.sync(state.member);
    syncBetBoard();
  }

  function syncBetBoard() {
    if (!Bet || typeof Bet.sync !== 'function') return;
    Bet.sync({
      round: state.round,
      remain: state.remainSeconds,
      canBet: state.canBet,
      odds: state.odds,
      member: state.member,
      lastDraw: (Draws && typeof Draws.getLatest === 'function') ? Draws.getLatest() : null,
    });
  }

  /* ---------- home data ---------- */

  function ensureMiniFrame() {
    var frame = $('miniFrame');
    if (!frame || !state.powerballBase || frame.dataset.loaded === '1') return;
    frame.src = state.powerballBase.replace(/\/?$/, '/') + '?view=powerballMiniView&openMini=1';
    frame.dataset.loaded = '1';
  }

  function fillPatternContent(elId, action) {
    var content = $(elId);
    if (!content) return Promise.resolve();
    var lang = I18N.getLang();
    content.innerHTML = '<div style="padding:12px;color:#969696;text-align:center;">'
      + I18N.t('patternLoading') + '</div>';
    return api(action, {
      qs: '&lang=' + encodeURIComponent(lang) + '&mode=latestLog&roundCnt=300',
    }).then(function (r) {
      if (r.json.status !== 'success') {
        content.innerHTML = '<div style="padding:12px;color:#969696;text-align:center;">'
          + I18N.t('patternFail') + '</div>';
        return;
      }
      content.innerHTML = r.json.content || '';
    });
  }

  function refreshPatternBox() {
    applyPatternTitles();
    return Promise.all([
      fillPatternContent('patternContent', 'pattern_pb_oddeven'),
      fillPatternContent('patternContentUo', 'pattern_pb_underover'),
      fillPatternContent('patternContentSumOe', 'pattern_sum_oddeven'),
      fillPatternContent('patternContentSumUo', 'pattern_sum_underover'),
    ]);
  }

  function refreshDraws() {
    return api('draws', { qs: '&limit=20' }).then(function (r) {
      if (r.json.status !== 'success') return;
      var newest = r.json.data && r.json.data.length ? Number(r.json.data[0].round) : 0;
      if (newest) state.lastFetchRound = newest;
      if (Draws && typeof Draws.render === 'function') {
        Draws.render(
          r.json.data || [],
          r.json.next_round,
          r.json.next_date_label || '',
          null
        );
      }
      syncBetBoard();
    });
  }

  function maybeFetchDraw(force) {
    var now = Date.now();
    if (!force && now - state.lastFetchAt < 4000) return Promise.resolve();
    state.lastFetchAt = now;
    var qs = force ? '&force=1' : '';
    return api('fetch_draw', { qs: qs }).then(function (r) {
      if (r.json.status !== 'success') return;
      var draw = r.json.data && r.json.data.draw;
      var round = draw && draw.round ? draw.round : 0;
      if (round && round !== state.lastFetchRound) {
        state.lastFetchRound = round;
        return Promise.all([refreshDraws(), refreshPatternBox()]);
      }
    }).catch(function (err) {
      console.warn('fetch_draw', err);
    });
  }

  function refreshStatus() {
    if (!state.token) return Promise.resolve();
    return api('status').then(function (r) {
      if (r.json.status !== 'success') return;
      var d = r.json.data;
      var prevRemain = state.remainSeconds;
      state.remainSeconds = (d.round && d.round.remain_seconds) | 0;
      state.powerballBase = d.powerball_base_url || state.powerballBase || '';
      state.odds = d.odds || state.odds || {};
      if (d.round) {
        state.round = d.round.round | 0;
        state.canBet = !!d.round.can_bet;
      }

      updateHeaderUser(d);
      if (d.round) {
        $('roundNo').textContent = d.round.round;
        $('countdown').textContent = fmtCountdown(d.round.remain_seconds);
        var rawTime = String(d.round.server_time || '').replace('T', ' ');
        $('serverDateDay').textContent = rawTime.slice(0, 10) || '——————';
        $('serverDateTime').textContent = rawTime.length >= 16 ? rawTime.slice(11, 16) : '--:--';
      }
      ensureMiniFrame();
      syncBetBoard();

      var crossedZero = prevRemain > 0 && state.remainSeconds === 0;
      var nearDraw = state.remainSeconds <= 5;
      if (crossedZero || nearDraw) {
        return maybeFetchDraw(crossedZero);
      }
    });
  }

  function tick() {
    if (!state.token || !state.homeReady) return;
    refreshStatus().then(function () {
      if (state.remainSeconds > 5) {
        return api('settle');
      }
    }).catch(function (err) {
      console.warn(err);
    });
  }

  function startPolling() {
    stopPolling();
    state.pollTimer = setInterval(tick, 1000);
  }

  function stopPolling() {
    if (state.pollTimer) {
      clearInterval(state.pollTimer);
      state.pollTimer = null;
    }
  }

  function bootHomeData() {
    state.homeReady = true;
    scaleMini();
    applyPatternTitles();
    return refreshStatus()
      .then(function () {
        return Promise.all([refreshDraws(), refreshPatternBox()]);
      })
      .then(function () {
        startPolling();
      })
      .catch(function (err) {
        console.warn(err);
        startPolling();
      });
  }

  /* ---------- session / login ---------- */

  function enterHome(member) {
    state.member = member;
    updateHeaderUser({ member: member });
    setError(null);
    $('loginPwd').value = '';
    setPwdVisible(false);
    showLoginShell(false);
    Nav.setActive('home');
    showPage('home');
    bootHomeData();
  }

  function enterLogin(err) {
    stopPolling();
    state.homeReady = false;
    state.member = null;
    state.powerballBase = '';
    state.odds = {};
    state.round = 0;
    if (Bet && typeof Bet.reset === 'function') Bet.reset();
    var frame = $('miniFrame');
    if (frame) {
      frame.src = 'about:blank';
      delete frame.dataset.loaded;
    }
    setError(err || null);
    showLoginShell(true);
    showPage('home');
  }

  function forceLogoutSession(err) {
    setToken('');
    enterLogin(err || { key: 'loginSession' });
  }

  function doLogin() {
    if (state.busy) return;
    var uid = ($('loginUid').value || '').trim();
    var pwd = $('loginPwd').value || '';
    if (!uid || !pwd) {
      setError({ key: 'loginNeed' });
      (uid ? $('loginPwd') : $('loginUid')).focus();
      return;
    }

    setError(null);
    setBusy(true);
    api('login', {
      auth: false,
      body: { uid: uid, pwd: pwd, client: 'mobile', machine: '' },
    }).then(function (r) {
      var json = r.json;
      if (json.status !== 'success') {
        setError({ code: json.code || '', raw: json.message || '' });
        return;
      }
      if (!json.data || !json.data.token || !json.data.member) {
        setError({ code: 'BAD_RESPONSE' });
        return;
      }
      setToken(json.data.token);
      enterHome(json.data.member);
    }).then(function () {
      setBusy(false);
    }, function (e) {
      console.warn(e);
      setBusy(false);
      setError({ key: 'loginFail' });
    });
  }

  function doLogout() {
    if (!window.confirm(I18N.t('logoutConfirm'))) return;
    stopPolling();
    var hadToken = !!state.token;
    var finish = function () {
      setToken('');
      enterLogin(null);
      try { $('loginPwd').focus(); } catch (e) { /* ignore */ }
    };
    if (!hadToken) {
      finish();
      return;
    }
    api('logout', { body: {} }).then(finish, finish);
  }

  function restoreSession() {
    if (!state.token) {
      enterLogin(null);
      return Promise.resolve();
    }
    setBusy(true);
    return api('me').then(function (r) {
      if (r.json.status === 'success' && r.json.data) {
        enterHome(r.json.data);
        return;
      }
      if (isAuthFailure(r)) {
        setToken('');
        enterLogin({ key: 'loginSession' });
        return;
      }
      enterLogin({ code: r.json.code || 'NETWORK', raw: r.json.message || '' });
    }).then(function () {
      setBusy(false);
    }, function (e) {
      console.warn(e);
      setBusy(false);
      enterLogin({ key: 'loginFail' });
    });
  }

  /* ---------- boot ---------- */

  function boot() {
    I18N.applyStatic();
    applyPatternTitles();

    var langSel = $('loginLang');
    langSel.value = I18N.getLang();
    langSel.addEventListener('change', function () {
      I18N.setLang(langSel.value);
    });

    I18N.onChange(function (lang) {
      if (langSel.value !== lang) langSel.value = lang;
      setPwdVisible($('loginPwd').type === 'text');
      renderError();
      updateHeaderUser({ member: state.member });
      applyPatternTitles();
      if (Draws && typeof Draws.rerender === 'function') Draws.rerender();
      if (Bet && typeof Bet.applyI18n === 'function') Bet.applyI18n();
      if (Mgmt && typeof Mgmt.applyI18n === 'function') Mgmt.applyI18n();
      if (state.homeReady && state.token) {
        refreshPatternBox();
      }
      var frame = $('miniFrame');
      if (frame) frame.setAttribute('title', I18N.t('miniTitle'));
    });

    $('loginForm').addEventListener('submit', function (e) {
      e.preventDefault();
      doLogin();
    });

    $('loginUid').addEventListener('keydown', function (e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        $('loginPwd').focus();
      }
    });

    $('togglePwd').addEventListener('click', function () {
      var input = $('loginPwd');
      setPwdVisible(input.type === 'password');
      input.focus();
    });

    if (Bet) {
      Bet.bind();
      Bet.onToast(toast);
      Bet.onBalance(function (bal) {
        if (!state.member) state.member = {};
        if (bal.balance != null) state.member.balance = bal.balance;
        if (bal.point != null) state.member.point = bal.point;
        updateHeaderUser({ member: state.member });
      });
      Bet.onPlaceBet(function (payload) {
        return api('bet', {
          body: {
            mode: payload.mode,
            amount: payload.amount,
            round: payload.round,
            machine: '',
          },
        }).then(function (r) {
          var json = r.json || {};
          if (json.status !== 'success') {
            var code = json.code || '';
            return {
              ok: false,
              message: (code && I18N.msg(code)) || json.message || I18N.msg('betFail'),
            };
          }
          return {
            ok: true,
            balance: json.data && json.data.balance,
            point: json.data && json.data.point,
          };
        });
      });
    }

    if (Mgmt) {
      Mgmt.bind();
      Mgmt.onToast(toast);
      Mgmt.onApi(api);
      Mgmt.onLogout(doLogout);
      Mgmt.onBalance(function (bal) {
        if (!state.member) state.member = {};
        if (bal.balance != null) state.member.balance = bal.balance;
        if (bal.point != null) state.member.point = bal.point;
        updateHeaderUser({ member: state.member });
      });
      Mgmt.onNavigate(function (page) {
        showPage(page);
        if (page === 'mgmt') {
          Nav.setActive('mgmt');
          Mgmt.sync(state.member);
          Mgmt.show();
        }
      });
    }

    Nav.bind();
    Nav.onPage(function (page) {
      showPage(page);
      if (page !== 'betsLive' && Mgmt && typeof Mgmt.stopLive === 'function') {
        Mgmt.stopLive();
      }
      if (page === 'home') scaleMini();
      if (page === 'bet') syncBetBoard();
      if (page === 'mgmt' && Mgmt && typeof Mgmt.show === 'function') {
        Mgmt.sync(state.member);
        Mgmt.show();
      }
    });
    Nav.onLang(function () {
      I18N.cycleLang();
      toast(I18N.t('langSwitched'));
    });
    Nav.onLogout(doLogout);

    window.addEventListener('resize', scaleMini);
    window.addEventListener('orientationchange', function () {
      setTimeout(scaleMini, 120);
    });

    setPwdVisible(false);
    restoreSession();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
