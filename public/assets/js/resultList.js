/**
 * powerball chatRoom resultList — CSS 4분원 (스프라이트 대체) + pb-num
 */
window.PBG_OwnerPick = (function () {
  var lastNewestRound = null;
  var cache = { draws: [], nextRound: 0, nextDrawnAt: '' };
  /** MID list area 944px / row 60px → 15 rows total */
  var MAX_ROWS = 15;

  function escHtml(s) {
    return String(s == null ? '' : s)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function lang() {
    return (window.PBG_I18N && window.PBG_I18N.lang) || 'ko';
  }

  /** 홀·언더 → P/蓝, 짝·오버 → B/红 */
  function markLabel(isP) {
    var L = lang();
    if (L === 'zh') return isP ? '蓝' : '红';
    return isP ? 'P' : 'B';
  }

  function dateLabel(drawnAt, fallback) {
    if (window.PBG_I18N && typeof window.PBG_I18N.formatDateLabel === 'function') {
      return window.PBG_I18N.formatDateLabel(drawnAt || undefined);
    }
    return String(fallback || '');
  }

  function roundLabel(round) {
    if (window.PBG_I18N && typeof window.PBG_I18N.formatRoundLabel === 'function') {
      return window.PBG_I18N.formatRoundLabel(round);
    }
    return String(round) + '회';
  }

  function emptyText() {
    var L = lang();
    if (L === 'zh') return '暂无开奖记录';
    if (L === 'en') return 'No draw history';
    return '개봉기록 없음';
  }

  function readyText() {
    var L = lang();
    if (L === 'zh') return '等待';
    if (L === 'en') return 'Wait';
    return '대기';
  }

  /**
   * pick_sprite_key 5글자: [pb_oe][sum_oe][pb_uo][sum_uo][size]
   * oe: o=홀/P, e=짝/B | uo: u=언더/P, o=오버/B
   */
  function rsCssFromKey(sk) {
    sk = String(sk || 'oouus');
    if (!/^[oeumsb]{5}$/.test(sk)) sk = 'oouus';
    function oe(ch) {
      var isP = ch === 'o';
      return { cls: isP ? 'p' : 'b', t: markLabel(isP) };
    }
    function uo(ch) {
      var isP = ch === 'u';
      return { cls: isP ? 'p' : 'b', t: markLabel(isP) };
    }
    var tl = oe(sk.charAt(0));
    var tr = oe(sk.charAt(1));
    var bl = uo(sk.charAt(2));
    var br = uo(sk.charAt(3));
    return ''
      + '<div class="rs-disk" aria-hidden="true">'
      + '<span class="rs-q tl ' + tl.cls + '">' + escHtml(tl.t) + '</span>'
      + '<span class="rs-q tr ' + tr.cls + '">' + escHtml(tr.t) + '</span>'
      + '<span class="rs-q bl ' + bl.cls + '">' + escHtml(bl.t) + '</span>'
      + '<span class="rs-q br ' + br.cls + '">' + escHtml(br.t) + '</span>'
      + '</div>';
  }

  function completedRowHtml(d) {
    d = d || {};
    var r = parseInt(d.round, 10) || 0;
    var dl = escHtml(dateLabel(d.drawn_at, d.date_label));
    var pbRaw = d.powerball;
    var sumRaw = d.ball_sum;
    var rawSk = String(d.pick_sprite_key != null ? d.pick_sprite_key : 'oouus');
    var sk = rawSk;
    if (!/^[oeumsb]{5}$/.test(sk)) sk = 'oouus';
    var pbNum = (pbRaw != null && pbRaw !== '') ? String(parseInt(pbRaw, 10)) : '';
    if (pbNum !== '' && (isNaN(pbNum) || parseInt(pbNum, 10) < 0)) pbNum = '';
    var pbBadge = pbNum !== ''
      ? '<span class="pb-num" aria-label="powerball">' + escHtml(pbNum) + '</span>'
      : '';
    return '<li id="pick-' + r + '" regdate="0" class="" style="display:list-item;"'
      + ' data-pick-sprite-key="' + escHtml(sk) + '"'
      + ' data-powerball="' + escHtml(String(pbRaw != null ? pbRaw : '')) + '"'
      + ' data-ball-sum="' + escHtml(String(sumRaw != null ? sumRaw : '')) + '"'
      + ' data-drawn-at="' + escHtml(String(d.drawn_at || '')) + '">'
      + '<div class="num">' + dl + '<br>' + escHtml(roundLabel(r)) + '</div>'
      + '<div class="rs rs-css ' + escHtml(sk) + '">' + rsCssFromKey(sk) + pbBadge + '</div>'
      + '</li>';
  }

  function waitingRowHtml(nr, drawnAt, dateFallback) {
    nr = parseInt(nr, 10) || 0;
    var dl = escHtml(dateLabel(drawnAt, dateFallback));
    return '<li id="pick-' + nr + '" class="" style="display:list-item;">'
      + '<div class="num">' + dl + '<br>' + escHtml(roundLabel(nr)) + '</div>'
      + '<div class="rs rs-css ready"><div class="rs-disk rs-ready">'
      + '<span class="rs-ready-txt">' + escHtml(readyText()) + '</span>'
      + '</div></div>'
      + '</li>';
  }

  function scheduleAnimatePush(row) {
    if (!row) return;
    requestAnimationFrame(function () {
      requestAnimationFrame(function () {
        setTimeout(function () {
          row.classList.add('animate-push');
          function clearAnim(ev) {
            if (ev && ev.animationName && ev.animationName !== 'slideDownPush') return;
            row.removeEventListener('animationend', clearAnim);
            row.classList.remove('animate-push');
          }
          row.addEventListener('animationend', clearAnim);
          setTimeout(function () {
            row.classList.remove('animate-push');
          }, 650);
        }, 0);
      });
    });
  }

  function render(draws, nextRound, nextDateLabel, nextDrawnAt) {
    var list = document.getElementById('resultList');
    if (!list) return;

    if (arguments.length > 0 && draws !== undefined) {
      cache.draws = draws || [];
      cache.nextRound = parseInt(nextRound, 10) || 0;
      cache.nextDrawnAt = nextDrawnAt || '';
      cache.nextDateLabel = String(nextDateLabel || '');
    }

    draws = cache.draws || [];
    nextRound = cache.nextRound || 0;
    nextDateLabel = cache.nextDateLabel || '';
    nextDrawnAt = cache.nextDrawnAt || '';

    var newestCompleted = 0;
    if (draws.length > 0) {
      newestCompleted = parseInt(draws[0].round, 10) || 0;
    }

    var shouldAnimate = lastNewestRound !== null
      && newestCompleted > 0
      && newestCompleted !== lastNewestRound;

    var showWaiting = nextRound > 0 && nextRound > newestCompleted;
    var maxCompleted = showWaiting ? (MAX_ROWS - 1) : MAX_ROWS;
    if (draws.length > maxCompleted) {
      draws = draws.slice(0, maxCompleted);
    }

    var html = '';
    if (showWaiting) {
      html += waitingRowHtml(nextRound, nextDrawnAt, nextDateLabel);
    }
    for (var i = 0; i < draws.length; i++) {
      html += completedRowHtml(draws[i] || {});
    }
    if (html === '') {
      html = '<li class="resultList-empty">' + escHtml(emptyText()) + '</li>';
    }

    list.innerHTML = html;

    if (shouldAnimate) {
      var done = list.querySelector('li .rs:not(.ready)');
      var row = done ? done.closest('li') : list.querySelector('li');
      scheduleAnimatePush(row);
    }

    if (newestCompleted > 0) {
      lastNewestRound = newestCompleted;
    }
  }

  function rerender() {
    render();
  }

  return { render: render, rerender: rerender };
})();
