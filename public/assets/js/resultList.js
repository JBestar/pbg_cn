/**
 * powerball chatRoom resultList — i18n-aware draw history
 */
window.PBG_OwnerPick = (function () {
  var lastNewestRound = null;
  var cache = { draws: [], nextRound: 0, nextDrawnAt: '' };

  function escHtml(s) {
    return String(s == null ? '' : s)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
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
    var lang = window.PBG_I18N && window.PBG_I18N.lang;
    if (lang === 'zh') return '暂无开奖记录';
    if (lang === 'en') return 'No draw history';
    return '개봉기록 없음';
  }

  function completedRowHtml(d) {
    d = d || {};
    var r = parseInt(d.round, 10) || 0;
    var dl = escHtml(dateLabel(d.drawn_at, d.date_label));
    var pbRaw = d.powerball;
    var sumRaw = d.ball_sum;
    var rawSk = String(d.pick_sprite_key != null ? d.pick_sprite_key : 'oouus');
    var sk = rawSk;
    if (!/^[oeumsb]{5}$/.test(sk)) {
      sk = 'oouus';
    }
    return '<li id="pick-' + r + '" regdate="0" class="" style="display:list-item;"'
      + ' data-pick-sprite-key="' + escHtml(sk) + '"'
      + ' data-powerball="' + escHtml(String(pbRaw != null ? pbRaw : '')) + '"'
      + ' data-ball-sum="' + escHtml(String(sumRaw != null ? sumRaw : '')) + '"'
      + ' data-drawn-at="' + escHtml(String(d.drawn_at || '')) + '">'
      + '<div class="num">' + dl + '<br>' + escHtml(roundLabel(r)) + '</div>'
      + '<div class="rs ' + escHtml(sk) + '"></div>'
      + '<div class="blank"></div>'
      + '<div class="pick pass"></div>'
      + '</li>';
  }

  function waitingRowHtml(nr, drawnAt, dateFallback) {
    nr = parseInt(nr, 10) || 0;
    var dl = escHtml(dateLabel(drawnAt, dateFallback));
    return '<li id="pick-' + nr + '" class="" style="display:list-item;">'
      + '<div class="num">' + dl + '<br>' + escHtml(roundLabel(nr)) + '</div>'
      + '<div class="rs ready"></div>'
      + '<div class="blank"></div>'
      + '<div class="pick"></div>'
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

  /**
   * @param {Array} draws newest-first
   * @param {number} nextRound
   * @param {string} nextDateLabel fallback
   * @param {string} [nextDrawnAt]
   */
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
    var maxCompleted = showWaiting ? 19 : 20;
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
      var done = list.querySelector('li .pick.pass');
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
