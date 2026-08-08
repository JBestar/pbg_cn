/**
 * Dev i18n: default Korean.
 * I → Chinese / English toggle; Ctrl+I → Korean.
 */
(function () {
  var PACKS = {
    ko: {
      lang: 'ko',
      htmlLang: 'ko',
      currency: 'u',
      title: '파워볼 게임기',
      ui: {
        countdownLabel: '개봉까지',
        roundPrefix: '제',
        roundSuffix: '회',
        draftAmountPrefix: '금액：',
        draftNone: '게임을 선택해주세요',
        loginTitle: '로그인',
        loginHint: '매장 아이디와 비밀번호를 입력하세요',
        loginLang: '언어',
        loginId: '아이디',
        loginPwd: '비밀번호',
        loginBtn: '로그인',
        loginNeed: '아이디와 비밀번호를 입력하세요',
        loginFail: '로그인 실패',
        loginOk: '로그인',
        loginNetwork: '서버에 연결할 수 없습니다',
        loginSession: '세션이 만료되었습니다. 다시 로그인하세요',
        loginPartial: '로그인됨 (일부 데이터 로드 실패)',
        ownerPickTab: '개봉기록',
        patternTitle: '파워볼 홀/짝 패턴 분석',
        patternTitleUo: '파워볼 언더/오버 패턴 분석',
        patternTitleSumOe: '일반볼 홀/짝 패턴 분석',
        patternTitleSumUo: '일반볼 언더/오버 패턴 분석',
        patternLoading: '불러오는 중…',
        patternFail: '불러오기 실패',
        patternEmpty: '패턴 데이터 없음',
        hintIdle: '키보드로 항목·금액 선택 후 Z로 배팅 (J: 금액 초기화)',
        odds: '배당',
        betHistory: '배팅 기록',
        thRound: '회차',
        thMode: '항목',
        thAmount: '금액',
        thPayout: '당첨금',
        thResult: '결과',
        betEmpty: '기록 없음',
        userInfo: '사용자 정보',
        balance: '잔액',
        machine: '기기',
        miniTitle: '파워볼 애니메이션',
        periodSuffix: '회',
        langSwitched: '언어: 한국어',
      },
      modes: {
        1: '파워볼 홀',
        2: '파워볼 짝',
        3: '파워볼 언더',
        4: '파워볼 오버',
        5: '파워볼 홀+언더',
        6: '파워볼 짝+언더',
        7: '파워볼 홀+오버',
        8: '파워볼 짝+오버',
        9: '일반볼 홀',
        10: '일반볼 짝',
        11: '일반볼 언더',
        12: '일반볼 오버',
        13: '일반볼 홀+언더',
        14: '일반볼 짝+언더',
        15: '일반볼 홀+오버',
        16: '일반볼 짝+오버',
      },
      states: { 1: '대기', 2: '낙첨', 3: '당첨', 4: '취소' },
      pattern: { odd: '홀', even: '짝', under: '언더', over: '오버' },
      msg: {
        selectAmount: '금액을 선택하세요',
        selectMode: '배팅 항목을 선택하세요',
        betOk: '배팅 완료',
        betFail: '배팅 실패',
        cancelOk: '대기 배팅을 취소했습니다',
        amountReset: '금액이 초기화되었습니다',
        modeCleared: '선택이 해제되었습니다',
        CLOSED: '현재 회차는 배팅이 마감되었습니다',
        BALANCE: '잔액이 부족합니다',
        NO_AMOUNT: '금액을 선택하세요',
        ROUND: '회차가 변경되었습니다. 다시 시도하세요',
        INVALID_MODE: '유효하지 않은 배팅 항목입니다',
        MIN_BET: '최소 배팅 금액보다 작습니다',
        MAX_BET: '최대 배팅 금액을 초과했습니다',
        MACHINE_NOT_FOUND: '기기가 존재하지 않습니다',
        NO_CANCEL: '취소할 대기 배팅이 없습니다',
        NEED_CREDENTIALS: '아이디와 비밀번호를 입력하세요',
        BAD_CREDENTIALS: '아이디 또는 비밀번호가 올바르지 않습니다',
        INACTIVE: '비활성 계정입니다',
        NOT_STORE: '매장 계정으로 로그인하세요',
        NETWORK: '서버에 연결할 수 없습니다',
        BAD_RESPONSE: '서버 응답이 올바르지 않습니다',
        AUTH: '로그인이 필요합니다',
      },
    },
    zh: {
      lang: 'zh',
      htmlLang: 'zh-CN',
      currency: 'u',
      title: '功率球游戏机',
      ui: {
        countdownLabel: '距开奖',
        roundPrefix: '第',
        roundSuffix: '期',
        draftAmountPrefix: '金额：',
        draftNone: '请选择游戏',
        loginTitle: '登录',
        loginHint: '请输入门店账号和密码',
        loginLang: '语言',
        loginId: '账号',
        loginPwd: '密码',
        loginBtn: '登录',
        loginNeed: '请输入账号和密码',
        loginFail: '登录失败',
        loginOk: '登录',
        loginNetwork: '无法连接服务器',
        loginSession: '会话已过期，请重新登录',
        loginPartial: '已登录（部分数据加载失败）',
        ownerPickTab: '开奖记录',
        patternTitle: '功率球 单/双 图案分析',
        patternTitleUo: '功率球大小图案分析',
        patternTitleSumOe: '普通球 单/双 图案分析',
        patternTitleSumUo: '普通球大小图案分析',
        patternLoading: '加载中…',
        patternFail: '加载失败',
        patternEmpty: '暂无图案数据',
        hintIdle: '请用键盘选择项目与金额后按 Z 投注（J：金额清零）',
        odds: '赔率',
        betHistory: '投注记录',
        thRound: '期号',
        thMode: '项目',
        thAmount: '金额',
        thPayout: '派彩',
        thResult: '结果',
        betEmpty: '暂无记录',
        userInfo: '用户信息',
        balance: '余额',
        machine: '机器',
        miniTitle: '功率球动画',
        periodSuffix: '期',
        langSwitched: '语言：中文',
      },
      modes: {
        1: '功率球 单',
        2: '功率球 双',
        3: '功率球 小',
        4: '功率球 大',
        5: '功率球 单+小',
        6: '功率球 双+小',
        7: '功率球 单+大',
        8: '功率球 双+大',
        9: '普通球 单',
        10: '普通球 双',
        11: '普通球 小',
        12: '普通球 大',
        13: '普通球 单+小',
        14: '普通球 双+小',
        15: '普通球 单+大',
        16: '普通球 双+大',
      },
      states: { 1: '等待', 2: '未中', 3: '已中', 4: '取消' },
      pattern: { odd: '单', even: '双', under: '小', over: '大' },
      msg: {
        selectAmount: '请选择金额',
        selectMode: '请选择投注项目',
        betOk: '投注成功',
        betFail: '投注失败',
        cancelOk: '已取消本期等待投注',
        amountReset: '金额已清零',
        modeCleared: '已清除选定项目',
        CLOSED: '当前期已封盘',
        BALANCE: '余额不足',
        NO_AMOUNT: '请选择金额',
        ROUND: '期号已变更，请重试',
        INVALID_MODE: '无效投注项目',
        MIN_BET: '低于最小投注',
        MAX_BET: '超过最大投注',
        MACHINE_NOT_FOUND: '机器不存在',
        NO_CANCEL: '没有可取消的投注',
        NEED_CREDENTIALS: '请输入账号和密码',
        BAD_CREDENTIALS: '账号或密码不正确',
        INACTIVE: '账号已停用',
        NOT_STORE: '请使用门店账号登录',
        NETWORK: '无法连接服务器',
        BAD_RESPONSE: '服务器响应无效',
        AUTH: '需要登录',
      },
    },
    en: {
      lang: 'en',
      htmlLang: 'en',
      currency: 'u',
      title: 'Powerball Terminal',
      ui: {
        countdownLabel: 'Until draw',
        roundPrefix: 'Round',
        roundSuffix: '',
        draftAmountPrefix: 'Amount: ',
        draftNone: 'Please select a game',
        loginTitle: 'Login',
        loginHint: 'Enter store ID and password',
        loginLang: 'Language',
        loginId: 'ID',
        loginPwd: 'Password',
        loginBtn: 'Login',
        loginNeed: 'Enter ID and password',
        loginFail: 'Login failed',
        loginOk: 'Login',
        loginNetwork: 'Cannot reach the server',
        loginSession: 'Session expired. Please log in again',
        loginPartial: 'Logged in (some data failed to load)',
        ownerPickTab: 'Draw history',
        patternTitle: 'Powerball Odd/Even Pattern',
        patternTitleUo: 'Powerball Under/Over Pattern',
        patternTitleSumOe: 'Number Sum Odd/Even Pattern',
        patternTitleSumUo: 'Number Sum Under/Over Pattern',
        patternLoading: 'Loading…',
        patternFail: 'Failed to load',
        patternEmpty: 'No pattern data',
        hintIdle: 'Select mode & amount, then Z to bet (J: clear amount)',
        odds: 'Odds',
        betHistory: 'Bet history',
        thRound: 'Round',
        thMode: 'Mode',
        thAmount: 'Amount',
        thPayout: 'Payout',
        thResult: 'Result',
        betEmpty: 'No records',
        userInfo: 'User',
        balance: 'Balance',
        machine: 'Machine',
        miniTitle: 'Powerball animation',
        periodSuffix: '',
        langSwitched: 'Language: English',
      },
      modes: {
        1: 'PB Odd',
        2: 'PB Even',
        3: 'PB Under',
        4: 'PB Over',
        5: 'PB Odd+Under',
        6: 'PB Even+Under',
        7: 'PB Odd+Over',
        8: 'PB Even+Over',
        9: 'Sum Odd',
        10: 'Sum Even',
        11: 'Sum Under',
        12: 'Sum Over',
        13: 'Sum Odd+Under',
        14: 'Sum Even+Under',
        15: 'Sum Odd+Over',
        16: 'Sum Even+Over',
      },
      states: { 1: 'Wait', 2: 'Lose', 3: 'Win', 4: 'Cancel' },
      pattern: { odd: 'Odd', even: 'Even', under: 'Under', over: 'Over' },
      msg: {
        selectAmount: 'Select an amount',
        selectMode: 'Select a bet mode',
        betOk: 'Bet placed',
        betFail: 'Bet failed',
        cancelOk: 'Waiting bets cancelled',
        amountReset: 'Amount cleared',
        modeCleared: 'Selection cleared',
        CLOSED: 'Betting closed for this round',
        BALANCE: 'Insufficient balance',
        NO_AMOUNT: 'Select an amount',
        ROUND: 'Round changed, please retry',
        INVALID_MODE: 'Invalid bet mode',
        MIN_BET: 'Below minimum bet',
        MAX_BET: 'Above maximum bet',
        MACHINE_NOT_FOUND: 'Machine not found',
        NO_CANCEL: 'No waiting bets to cancel',
        NEED_CREDENTIALS: 'Enter ID and password',
        BAD_CREDENTIALS: 'Incorrect ID or password',
        INACTIVE: 'Account is inactive',
        NOT_STORE: 'Please log in with a store account',
        NETWORK: 'Cannot reach the server',
        BAD_RESPONSE: 'Invalid server response',
        AUTH: 'Login required',
      },
    },
  };

  var STORAGE_KEY = 'pbg_lang';
  var current = 'ko';
  try {
    var saved = localStorage.getItem(STORAGE_KEY);
    if (saved === 'ko' || saved === 'zh' || saved === 'en') current = saved;
  } catch (e) { /* ignore */ }

  function pack() {
    return PACKS[current] || PACKS.ko;
  }

  function syncFlat() {
    var p = pack();
    I18N.currency = p.currency;
    I18N.modes = p.modes;
    I18N.msg = p.msg;
    I18N.ui = p.ui;
    I18N.states = p.states;
    I18N.pattern = p.pattern;
    I18N.lang = p.lang;
  }

  function applyStatic() {
    var p = pack();
    document.documentElement.lang = p.htmlLang;
    document.title = p.title;
    document.querySelectorAll('[data-i18n]').forEach(function (el) {
      var key = el.getAttribute('data-i18n');
      if (key && p.ui[key] != null) el.textContent = p.ui[key];
    });
    document.querySelectorAll('[data-i18n-attr]').forEach(function (el) {
      var spec = el.getAttribute('data-i18n-attr');
      if (!spec) return;
      spec.split(';').forEach(function (part) {
        var bits = part.split(':');
        if (bits.length !== 2) return;
        var attr = bits[0].trim();
        var key = bits[1].trim();
        if (attr && key && p.ui[key] != null) el.setAttribute(attr, p.ui[key]);
      });
    });
  }

  function setLang(lang) {
    if (lang !== 'ko' && lang !== 'zh' && lang !== 'en') return;
    current = lang;
    try { localStorage.setItem(STORAGE_KEY, lang); } catch (e) { /* ignore */ }
    syncFlat();
    applyStatic();
    if (typeof window.PBG_onLangChange === 'function') {
      window.PBG_onLangChange(lang);
    }
  }

  function cycleLangFromI() {
    if (current === 'ko') setLang('zh');
    else if (current === 'zh') setLang('en');
    else setLang('zh');
  }

  function formatDateLabel(drawnAt) {
    var p = pack();
    var d = drawnAt ? new Date(String(drawnAt).replace(/-/g, '/')) : new Date();
    if (isNaN(d.getTime())) d = new Date();
    var m = d.getMonth() + 1;
    var day = d.getDate();
    if (p.lang === 'en') return m + '/' + day;
    if (p.lang === 'ko') {
      return String(m).padStart(2, '0') + '월' + String(day).padStart(2, '0') + '일';
    }
    return String(m).padStart(2, '0') + '月' + String(day).padStart(2, '0') + '日';
  }

  function formatRoundLabel(round) {
    var p = pack();
    var r = String(round);
    if (p.lang === 'en') return r;
    return r + p.ui.periodSuffix;
  }

  var I18N = {
    keyMode: {
      KeyA: 1, KeyB: 2, KeyC: 3, KeyD: 4,
      KeyE: 9, KeyF: 10, KeyG: 11, KeyH: 12,
      KeyO: 5, KeyP: 7, KeyQ: 6, KeyR: 8,
      KeyS: 13, KeyT: 15, KeyU: 14, KeyV: 16,
    },
    getLang: function () { return current; },
    setLang: setLang,
    cycleLangFromI: cycleLangFromI,
    pack: pack,
    applyStatic: applyStatic,
    formatDateLabel: formatDateLabel,
    formatRoundLabel: formatRoundLabel,
    currency: PACKS.ko.currency,
    modes: PACKS.ko.modes,
    msg: PACKS.ko.msg,
    ui: PACKS.ko.ui,
    states: PACKS.ko.states,
    pattern: PACKS.ko.pattern,
    lang: 'ko',
  };

  window.PBG_I18N = I18N;
  syncFlat();

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
      applyStatic();
    });
  } else {
    applyStatic();
  }
})();
