/**
 * Mobile-only i18n. Storage key is separate from the cabinet (pbg_lang).
 */
(function () {
  var PACKS = {
    ko: {
      htmlLang: 'ko',
      title: '파워볼 모바일',
      ui: {
        brandSub: 'MOBILE',
        ariaLang: '언어 선택',
        ariaId: '아이디',
        ariaPwd: '비밀번호',
        phId: '아이디',
        phPwd: '비밀번호',
        ariaShowPwd: '비밀번호 보기',
        ariaHidePwd: '비밀번호 숨기기',
        ariaLogin: '로그인',
        btnLogin: '로그인',
        btnLogout: '로그아웃',
        loggedIn: '로그인되었습니다',
        loginNeed: '아이디와 비밀번호를 입력하세요',
        loginFail: '로그인 실패',
        loginSession: '세션이 만료되었습니다. 다시 로그인하세요',
        countdownLabel: '개봉까지',
        roundPrefix: '제',
        roundSuffix: '회',
        ownerPickTab: '개봉기록',
        patternTitleSuffix: '패턴 분석',
        room1Title: '제1번방',
        room2Title: '제2번방',
        room3Title: '제3번방',
        room4Title: '제4번방',
        markP: 'P',
        markB: 'B',
        patternLoading: '불러오는 중…',
        patternFail: '불러오기 실패',
        drawsEmpty: '개봉기록 없음',
        drawsReady: '대기',
        miniTitle: '파워볼 애니메이션',
        navHome: '홈화면',
        navBet: '베팅하기',
        navMgmt: '세부관리',
        navLang: '언어변경',
        navLogout: '나가기',
        pageBetTitle: '베팅하기',
        pageBetSoon: '베팅 화면은 준비 중입니다.',
        pageMgmtTitle: '세부관리',
        pageMgmtSoon: '세부관리 화면은 준비 중입니다.',
        mgmtUserId: '유저 아이디',
        mgmtTotalMoney: '토탈 금액',
        mgmtPoint: '포인트',
        mgmtCharge: '충전신청',
        mgmtExchange: '환전신청',
        mgmtPointConvert: '마일리지 전환',
        mgmtBetsLive: '실시간 베팅리스트',
        mgmtBetsAll: '배팅내역 상세보기',
        mgmtWins: '당첨내역 확인하기',
        mgmtInquiry: '1대1 상담문의',
        mgmtNotice: '공지사항',
        mgmtLogout: '로그아웃',
        mgmtSoon: '준비 중입니다.',
        mgmtLoading: '불러오는 중…',
        mgmtLoadFail: '불러오기 실패',
        mgmtHistEmpty: '내역이 없습니다',
        mgmtCancel: '취소',
        mgmtConvertDo: '전환하기',
        mgmtConvertOk: '포인트가 잔액으로 전환되었습니다',
        mgmtConvertFail: '전환에 실패했습니다',
        mgmtNoPoint: '전환할 포인트가 없습니다',
        mgmtPointConvertHelp: '보유 포인트를 잔액(머니)으로 전환합니다.',
        mgmtPointConvertConfirm: '포인트를 잔액으로 전환할까요?',
        mgmtPointHave: '적립포인트',
        mgmtPointAsk: '신청포인트',
        mgmtPointFull: '전액',
        mgmtPointSubmit: '신청완료',
        mgmtPointColAmt: '전환포인트',
        mgmtPointColProc: '처리일시',
        mgmtPointNeedAmount: '신청포인트를 입력하세요',
        mgmtStateWait: '대기',
        mgmtStateLose: '낙첨',
        mgmtStateWin: '당첨',
        mgmtStateCancel: '취소',
        mgmtChargeAmount: '신청금액',
        mgmtChargeId: '충전 id',
        mgmtChargeAccount: '충전계좌요청',
        mgmtChargeHist: '신청내역',
        mgmtChargeColAmount: '충전금',
        mgmtChargeColState: '상태',
        mgmtChargeColReq: '신청시간',
        mgmtChargeColProc: '처리일시',
        mgmtChargeWait: '대기',
        mgmtChargeOk: '확인',
        mgmtChargeRefuse: '거절',
        mgmtChargeNeedAmount: '금액을 선택하세요',
        mgmtChargeConfirm: '충전신청 하시겠습니까?',
        mgmtChargeOkMsg: '충전신청이 접수되었습니다',
        mgmtChargeFail: '충전신청 실패',
        mgmtAccountConfirm: '총판에 충전계좌를 요청할까요?',
        mgmtAccountOk: '충전계좌요청을 보냈습니다',
        mgmtAccountFail: '계좌요청 실패',
        mgmtExchangeAmount: '신청금액',
        mgmtExchangeId: '환전 id',
        mgmtExchangeDo: '환전신청',
        mgmtExchangeHist: '신청내역',
        mgmtExchangeColAmount: '환전금',
        mgmtExchangeColState: '상태',
        mgmtExchangeColReq: '신청시간',
        mgmtExchangeColProc: '처리일시',
        mgmtExchangeNeedAmount: '금액을 선택하세요',
        mgmtExchangeConfirm: '환전신청 하시겠습니까?',
        mgmtExchangeOkMsg: '환전신청이 접수되었습니다',
        mgmtExchangeFail: '환전신청 실패',
        logoutConfirm: '로그아웃 하시겠습니까?',
        langSwitched: '언어: 한국어',
        balance: '잔액',
        point: '포인트',
        betLiveRound: '진행회차',
        betPrevRound: '직전회차',
        betRoomCombo12: '제1번방 + 제2번방',
        betRoomCombo34: '제3번방 + 제4번방',
        betNumberGame: '숫자 맞추기',
        betPbLabel: '파워볼',
        betSumLabel: '일반볼',
        betOdd: '홀',
        betEven: '짝',
        betUnder: '언더',
        betOver: '오버',
        betReset: '초기화',
        betPlace: '베팅하기',
        betDraft: '배팅금액',
        betClosed: '배팅 마감',
        betHonorific: '님',
        betHoldMoney: '보유머니',
        betNoResult: '결과 대기',
        betRoundSuffix: '회차',
      },
      msg: {
        NEED_CREDENTIALS: '아이디와 비밀번호를 입력하세요',
        BAD_CREDENTIALS: '아이디 또는 비밀번호가 올바르지 않습니다',
        INACTIVE: '비활성 계정입니다',
        NOT_STORE: '매장 계정으로 로그인하세요',
        NOT_MOBILE: '모바일 계정으로 로그인하세요',
        NETWORK: '서버에 연결할 수 없습니다',
        TIMEOUT: '서버 응답이 지연되고 있습니다. 잠시 후 다시 시도하세요',
        BAD_RESPONSE: '서버 응답이 올바르지 않습니다',
        AUTH: '로그인이 필요합니다',
        selectAmount: '금액을 선택하세요',
        selectMode: '배팅 항목을 선택하세요',
        betOk: '배팅 완료',
        betFail: '배팅 실패',
        amountReset: '금액이 초기화되었습니다',
        CLOSED: '현재 회차는 배팅이 마감되었습니다',
        BALANCE: '잔액이 부족합니다',
        NO_AMOUNT: '금액을 선택하세요',
        ROUND: '회차가 변경되었습니다. 다시 시도하세요',
        INVALID_MODE: '유효하지 않은 배팅 항목입니다',
        MIN_BET: '최소 배팅 금액보다 작습니다',
        MAX_BET: '최대 배팅 금액을 초과했습니다',
        NO_POINT: '전환할 포인트가 없습니다',
        NO_AGENCY: '상위 총판이 없습니다',
        CHARGE_PENDING: '대기 중인 충전신청이 있습니다',
        EXCHANGE_PENDING: '대기 중인 환전신청이 있습니다',
      },
    },
    zh: {
      htmlLang: 'zh-CN',
      title: '功率球 手机版',
      ui: {
        brandSub: 'MOBILE',
        ariaLang: '选择语言',
        ariaId: '账号',
        ariaPwd: '密码',
        phId: '账号',
        phPwd: '密码',
        ariaShowPwd: '显示密码',
        ariaHidePwd: '隐藏密码',
        ariaLogin: '登录',
        btnLogin: '登录',
        btnLogout: '退出',
        loggedIn: '登录成功',
        loginNeed: '请输入账号和密码',
        loginFail: '登录失败',
        loginSession: '会话已过期，请重新登录',
        countdownLabel: '距开奖',
        roundPrefix: '第',
        roundSuffix: '期',
        ownerPickTab: '开奖记录',
        patternTitleSuffix: '图案分析',
        room1Title: '第1号房',
        room2Title: '第2号房',
        room3Title: '第3号房',
        room4Title: '第4号房',
        markP: 'P',
        markB: 'B',
        patternLoading: '加载中…',
        patternFail: '加载失败',
        drawsEmpty: '暂无开奖记录',
        drawsReady: '等待',
        miniTitle: '功率球动画',
        navHome: '首页',
        navBet: '投注',
        navMgmt: '详细管理',
        navLang: '语言变更',
        navLogout: '退出',
        pageBetTitle: '投注',
        pageBetSoon: '投注页面即将推出。',
        pageMgmtTitle: '详细管理',
        pageMgmtSoon: '详细管理页面即将推出。',
        mgmtUserId: '用户账号',
        mgmtTotalMoney: '总金额',
        mgmtPoint: '积分',
        mgmtCharge: '充值申请',
        mgmtExchange: '兑换申请',
        mgmtPointConvert: '积分转换',
        mgmtBetsLive: '实时投注列表',
        mgmtBetsAll: '投注明细',
        mgmtWins: '中奖记录',
        mgmtInquiry: '1对1咨询',
        mgmtNotice: '公告',
        mgmtLogout: '退出登录',
        mgmtSoon: '即将推出。',
        mgmtLoading: '加载中…',
        mgmtLoadFail: '加载失败',
        mgmtHistEmpty: '暂无记录',
        mgmtCancel: '取消',
        mgmtConvertDo: '转换',
        mgmtConvertOk: '积分已转换为余额',
        mgmtConvertFail: '转换失败',
        mgmtNoPoint: '没有可转换的积分',
        mgmtPointConvertHelp: '将持有积分转换为余额。',
        mgmtPointConvertConfirm: '确定将积分转换为余额吗？',
        mgmtPointHave: '累计积分',
        mgmtPointAsk: '申请积分',
        mgmtPointFull: '全额',
        mgmtPointSubmit: '申请完成',
        mgmtPointColAmt: '转换积分',
        mgmtPointColProc: '处理时间',
        mgmtPointNeedAmount: '请输入申请积分',
        mgmtStateWait: '等待',
        mgmtStateLose: '未中',
        mgmtStateWin: '已中',
        mgmtStateCancel: '取消',
        mgmtChargeAmount: '申请金额',
        mgmtChargeId: '充值ID',
        mgmtChargeAccount: '充值账户请求',
        mgmtChargeHist: '申请记录',
        mgmtChargeColAmount: '充值金额',
        mgmtChargeColState: '状态',
        mgmtChargeColReq: '申请时间',
        mgmtChargeColProc: '处理时间',
        mgmtChargeWait: '等待',
        mgmtChargeOk: '确认',
        mgmtChargeRefuse: '拒绝',
        mgmtChargeNeedAmount: '请选择金额',
        mgmtChargeConfirm: '确定申请充值吗？',
        mgmtChargeOkMsg: '充值申请已提交',
        mgmtChargeFail: '充值申请失败',
        mgmtAccountConfirm: '向总代理请求充值账户吗？',
        mgmtAccountOk: '已发送充值账户请求',
        mgmtAccountFail: '账户请求失败',
        mgmtExchangeAmount: '申请金额',
        mgmtExchangeId: '兑换ID',
        mgmtExchangeDo: '兑换申请',
        mgmtExchangeHist: '申请记录',
        mgmtExchangeColAmount: '兑换金额',
        mgmtExchangeColState: '状态',
        mgmtExchangeColReq: '申请时间',
        mgmtExchangeColProc: '处理时间',
        mgmtExchangeNeedAmount: '请选择金额',
        mgmtExchangeConfirm: '确定申请兑换吗？',
        mgmtExchangeOkMsg: '兑换申请已提交',
        mgmtExchangeFail: '兑换申请失败',
        logoutConfirm: '确定要退出吗？',
        langSwitched: '语言: 中文',
        balance: '余额',
        point: '积分',
        betLiveRound: '进行期次',
        betPrevRound: '上期',
        betRoomCombo12: '第1号房 + 第2号房',
        betRoomCombo34: '第3号房 + 第4号房',
        betNumberGame: '猜数字',
        betPbLabel: '功率球',
        betSumLabel: '普通球',
        betOdd: '单',
        betEven: '双',
        betUnder: '小',
        betOver: '大',
        betReset: '重置',
        betPlace: '投注',
        betDraft: '投注金额',
        betClosed: '已封盘',
        betHonorific: '',
        betHoldMoney: '余额',
        betNoResult: '等待开奖',
        betRoundSuffix: '期',
      },
      msg: {
        NEED_CREDENTIALS: '请输入账号和密码',
        BAD_CREDENTIALS: '账号或密码不正确',
        INACTIVE: '账号已停用',
        NOT_STORE: '请使用门店账号登录',
        NOT_MOBILE: '请使用手机账号登录',
        NETWORK: '无法连接服务器',
        TIMEOUT: '服务器响应超时，请稍后再试',
        BAD_RESPONSE: '服务器响应无效',
        AUTH: '需要登录',
        selectAmount: '请选择金额',
        selectMode: '请选择投注项',
        betOk: '投注成功',
        betFail: '投注失败',
        amountReset: '金额已重置',
        CLOSED: '当前期已封盘',
        BALANCE: '余额不足',
        NO_AMOUNT: '请选择金额',
        ROUND: '期号已变更，请重试',
        INVALID_MODE: '无效投注项目',
        MIN_BET: '低于最小投注',
        MAX_BET: '超过最大投注',
        NO_POINT: '没有可转换的积分',
        NO_AGENCY: '没有上级总代理',
        CHARGE_PENDING: '已有待处理的充值申请',
        EXCHANGE_PENDING: '已有待处理的兑换申请',
      },
    },
    en: {
      htmlLang: 'en',
      title: 'Powerball Mobile',
      ui: {
        brandSub: 'MOBILE',
        ariaLang: 'Select language',
        ariaId: 'ID',
        ariaPwd: 'Password',
        phId: 'ID',
        phPwd: 'Password',
        ariaShowPwd: 'Show password',
        ariaHidePwd: 'Hide password',
        ariaLogin: 'Login',
        btnLogin: 'Login',
        btnLogout: 'Logout',
        loggedIn: 'Logged in',
        loginNeed: 'Enter ID and password',
        loginFail: 'Login failed',
        loginSession: 'Session expired. Please log in again',
        countdownLabel: 'Until draw',
        roundPrefix: 'Round',
        roundSuffix: '',
        ownerPickTab: 'Draw history',
        patternTitleSuffix: 'Pattern',
        room1Title: 'Room 1',
        room2Title: 'Room 2',
        room3Title: 'Room 3',
        room4Title: 'Room 4',
        markP: 'P',
        markB: 'B',
        patternLoading: 'Loading…',
        patternFail: 'Failed to load',
        drawsEmpty: 'No draw history',
        drawsReady: 'Wait',
        miniTitle: 'Powerball animation',
        navHome: 'Home',
        navBet: 'Bet',
        navMgmt: 'Management',
        navLang: 'Language',
        navLogout: 'Logout',
        pageBetTitle: 'Betting',
        pageBetSoon: 'Betting screen coming soon.',
        pageMgmtTitle: 'Management',
        pageMgmtSoon: 'Management screen coming soon.',
        mgmtUserId: 'User ID',
        mgmtTotalMoney: 'Total',
        mgmtPoint: 'Points',
        mgmtCharge: 'Deposit',
        mgmtExchange: 'Withdraw',
        mgmtPointConvert: 'Convert points',
        mgmtBetsLive: 'Live bets',
        mgmtBetsAll: 'Bet history',
        mgmtWins: 'Winning bets',
        mgmtInquiry: '1:1 Support',
        mgmtNotice: 'Notices',
        mgmtLogout: 'Log out',
        mgmtSoon: 'Coming soon.',
        mgmtLoading: 'Loading…',
        mgmtLoadFail: 'Failed to load',
        mgmtHistEmpty: 'No records',
        mgmtCancel: 'Cancel',
        mgmtConvertDo: 'Convert',
        mgmtConvertOk: 'Points converted to balance',
        mgmtConvertFail: 'Conversion failed',
        mgmtNoPoint: 'No points to convert',
        mgmtPointConvertHelp: 'Convert your points into balance.',
        mgmtPointConvertConfirm: 'Convert points to balance now?',
        mgmtPointHave: 'Points',
        mgmtPointAsk: 'Amount',
        mgmtPointFull: 'All',
        mgmtPointSubmit: 'Submit',
        mgmtPointColAmt: 'Converted',
        mgmtPointColProc: 'Processed',
        mgmtPointNeedAmount: 'Enter points to convert',
        mgmtStateWait: 'Pending',
        mgmtStateLose: 'Lost',
        mgmtStateWin: 'Won',
        mgmtStateCancel: 'Canceled',
        mgmtChargeAmount: 'Amount',
        mgmtChargeId: 'Charge ID',
        mgmtChargeAccount: 'Request bank account',
        mgmtChargeHist: 'Request history',
        mgmtChargeColAmount: 'Amount',
        mgmtChargeColState: 'Status',
        mgmtChargeColReq: 'Requested',
        mgmtChargeColProc: 'Processed',
        mgmtChargeWait: 'Pending',
        mgmtChargeOk: 'Approved',
        mgmtChargeRefuse: 'Rejected',
        mgmtChargeNeedAmount: 'Select an amount',
        mgmtChargeConfirm: 'Submit charge request?',
        mgmtChargeOkMsg: 'Charge request submitted',
        mgmtChargeFail: 'Charge request failed',
        mgmtAccountConfirm: 'Request bank account from agency?',
        mgmtAccountOk: 'Account request sent',
        mgmtAccountFail: 'Account request failed',
        mgmtExchangeAmount: 'Amount',
        mgmtExchangeId: 'Exchange ID',
        mgmtExchangeDo: 'Request exchange',
        mgmtExchangeHist: 'Request history',
        mgmtExchangeColAmount: 'Amount',
        mgmtExchangeColState: 'Status',
        mgmtExchangeColReq: 'Requested',
        mgmtExchangeColProc: 'Processed',
        mgmtExchangeNeedAmount: 'Select an amount',
        mgmtExchangeConfirm: 'Submit exchange request?',
        mgmtExchangeOkMsg: 'Exchange request submitted',
        mgmtExchangeFail: 'Exchange request failed',
        logoutConfirm: 'Log out now?',
        langSwitched: 'Language: English',
        balance: 'Balance',
        point: 'Points',
        betLiveRound: 'Live round',
        betPrevRound: 'Previous',
        betRoomCombo12: 'Room 1 + Room 2',
        betRoomCombo34: 'Room 3 + Room 4',
        betNumberGame: 'Pick a number',
        betPbLabel: 'Powerball',
        betSumLabel: 'Sum',
        betOdd: 'Odd',
        betEven: 'Even',
        betUnder: 'Under',
        betOver: 'Over',
        betReset: 'Reset',
        betPlace: 'Place bet',
        betDraft: 'Bet amount',
        betClosed: 'Betting closed',
        betHonorific: '',
        betHoldMoney: 'Balance',
        betNoResult: 'Awaiting result',
        betRoundSuffix: '',
      },
      msg: {
        NEED_CREDENTIALS: 'Enter ID and password',
        BAD_CREDENTIALS: 'Incorrect ID or password',
        INACTIVE: 'Account is inactive',
        NOT_STORE: 'Please log in with a store account',
        NOT_MOBILE: 'Please log in with a mobile account',
        NETWORK: 'Cannot reach the server',
        TIMEOUT: 'Server is not responding. Please try again later',
        BAD_RESPONSE: 'Invalid server response',
        AUTH: 'Login required',
        selectAmount: 'Select an amount',
        selectMode: 'Select a bet option',
        betOk: 'Bet placed',
        betFail: 'Bet failed',
        amountReset: 'Amount cleared',
        CLOSED: 'Betting is closed for this round',
        BALANCE: 'Insufficient balance',
        NO_AMOUNT: 'Select an amount',
        ROUND: 'Round changed. Please try again',
        INVALID_MODE: 'Invalid bet option',
        MIN_BET: 'Below minimum bet',
        MAX_BET: 'Above maximum bet',
        NO_POINT: 'No points to convert',
        NO_AGENCY: 'No parent agency',
        CHARGE_PENDING: 'A charge request is already pending',
        EXCHANGE_PENDING: 'An exchange request is already pending',
      },
    },
  };

  var STORAGE_KEY = 'pbg_m_lang';
  var current = 'ko';
  try {
    var saved = localStorage.getItem(STORAGE_KEY);
    if (PACKS[saved]) current = saved;
  } catch (e) { /* storage unavailable (private mode) */ }

  var listeners = [];

  function pack() {
    return PACKS[current] || PACKS.ko;
  }

  function t(key) {
    var v = pack().ui[key];
    if (v == null) v = PACKS.ko.ui[key];
    return v == null ? '' : v;
  }

  function msg(code) {
    if (!code) return '';
    var v = pack().msg[code];
    return v == null ? '' : v;
  }

  function formatDateLabel(drawnAt) {
    var d = drawnAt ? new Date(String(drawnAt).replace(/-/g, '/')) : new Date();
    if (isNaN(d.getTime())) d = new Date();
    var m = d.getMonth() + 1;
    var day = d.getDate();
    if (current === 'en') return m + '/' + day;
    if (current === 'zh') return m + '月' + day + '日';
    return String(m).padStart(2, '0') + '월' + String(day).padStart(2, '0') + '일';
  }

  function formatRoundLabel(round) {
    var r = parseInt(round, 10) || 0;
    if (current === 'en') return t('roundPrefix') + ' ' + r;
    return t('roundPrefix') + ' ' + r + (t('roundSuffix') ? ' ' + t('roundSuffix') : '');
  }

  /** Day slot within 288 rounds/day (5-min). 0 → 288. */
  function dayRoundOf(round) {
    var r = parseInt(round, 10) || 0;
    if (r <= 0) return 0;
    var d = r % 288;
    return d === 0 ? 288 : d;
  }

  /** e.g. 1626490(147)회차 */
  function formatRoundWithDay(round, withSuffix) {
    var r = parseInt(round, 10) || 0;
    if (r <= 0) return '-';
    var body = r + '(' + dayRoundOf(r) + ')';
    if (withSuffix === false) return body;
    var suffix = t('betRoundSuffix');
    if (!suffix) return body;
    return body + suffix;
  }

  function formatPatternTitleHtml(roomN) {
    var room = t('room' + roomN + 'Title') || ('#' + roomN);
    var suffix = t('patternTitleSuffix');
    return room + ' <span class="mk-p">' + t('markP') + '</span>/<span class="mk-b">' + t('markB') + '</span>'
      + (suffix ? ' ' + suffix : '');
  }

  function applyStatic(root) {
    var p = pack();
    var scope = root || document;
    document.documentElement.lang = p.htmlLang;
    document.title = p.title;
    scope.querySelectorAll('[data-i18n]').forEach(function (el) {
      el.textContent = t(el.getAttribute('data-i18n'));
    });
    scope.querySelectorAll('[data-i18n-ph]').forEach(function (el) {
      el.setAttribute('placeholder', t(el.getAttribute('data-i18n-ph')));
    });
    scope.querySelectorAll('[data-i18n-aria]').forEach(function (el) {
      el.setAttribute('aria-label', t(el.getAttribute('data-i18n-aria')));
    });
    scope.querySelectorAll('[data-i18n-title]').forEach(function (el) {
      el.setAttribute('title', t(el.getAttribute('data-i18n-title')));
    });
  }

  function setLang(lang) {
    if (!PACKS[lang] || lang === current) return;
    current = lang;
    try { localStorage.setItem(STORAGE_KEY, lang); } catch (e) { /* ignore */ }
    applyStatic();
    listeners.forEach(function (fn) {
      try { fn(lang); } catch (e) { console.warn(e); }
    });
  }

  function cycleLang() {
    if (current === 'ko') setLang('zh');
    else if (current === 'zh') setLang('en');
    else setLang('ko');
  }

  window.PBGM_I18N = {
    getLang: function () { return current; },
    setLang: setLang,
    cycleLang: cycleLang,
    t: t,
    msg: msg,
    applyStatic: applyStatic,
    formatDateLabel: formatDateLabel,
    formatRoundLabel: formatRoundLabel,
    dayRoundOf: dayRoundOf,
    formatRoundWithDay: formatRoundWithDay,
    formatPatternTitleHtml: formatPatternTitleHtml,
    onChange: function (fn) { if (typeof fn === 'function') listeners.push(fn); },
  };
})();
