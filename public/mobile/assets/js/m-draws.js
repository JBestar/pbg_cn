/**
 * Mobile draw-history list (cabinet resultList equivalent, self-contained).
 * Does not touch public/assets/js/resultList.js.
 */
(function () {
  'use strict';

  var MAX_ROWS = 10;
  var lastNewestRound = null;
  var cache = { draws: [], nextRound: 0, nextDateLabel: '', nextDrawnAt: '' };

  function I18N() { return window.PBGM_I18N; }

  function escHtml(s) {
    return String(s == null ? '' : s)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function markLabel(isP) {
    var t = I18N() && I18N().t;
    if (!t) return isP ? 'P' : 'B';
    return isP ? t('markP') : t('markB');
  }

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
    var i18n = I18N();
    var dl = escHtml(i18n.formatDateLabel(d.drawn_at || undefined));
    var rl = escHtml(i18n.formatRoundLabel(r));
    var pbRaw = d.powerball;
    var sk = String(d.pick_sprite_key != null ? d.pick_sprite_key : 'oouus');
    if (!/^[oeumsb]{5}$/.test(sk)) sk = 'oouus';
    var pbNum = (pbRaw != null && pbRaw !== '') ? String(parseInt(pbRaw, 10)) : '';
    if (pbNum !== '' && (isNaN(pbNum) || parseInt(pbNum, 10) < 0)) pbNum = '';
    var pbBadge = pbNum !== ''
      ? '<span class="pb-num" aria-label="powerball">' + escHtml(pbNum) + '</span>'
      : '';
    return '<li class="m-draw-row" data-round="' + r + '">'
      + '<div class="m-draw-num">' + dl + '<br>' + rl + '</div>'
      + '<div class="m-draw-rs rs rs-css">' + rsCssFromKey(sk) + pbBadge + '</div>'
      + '</li>';
  }

  function waitingRowHtml(nr, drawnAt) {
    nr = parseInt(nr, 10) || 0;
    var i18n = I18N();
    var dl = escHtml(i18n.formatDateLabel(drawnAt || undefined));
    var rl = escHtml(i18n.formatRoundLabel(nr));
    return '<li class="m-draw-row m-draw-row--wait" data-round="' + nr + '">'
      + '<div class="m-draw-num">' + dl + '<br>' + rl + '</div>'
      + '<div class="m-draw-rs rs rs-css ready"><div class="rs-disk rs-ready">'
      + '<span class="rs-ready-txt">' + escHtml(i18n.t('drawsReady')) + '</span>'
      + '</div></div>'
      + '</li>';
  }

  function render(draws, nextRound, nextDateLabel, nextDrawnAt) {
    var list = document.getElementById('mDrawList');
    if (!list) return;

    if (arguments.length > 0 && draws !== undefined) {
      cache.draws = draws || [];
      cache.nextRound = parseInt(nextRound, 10) || 0;
      cache.nextDrawnAt = nextDrawnAt || '';
      cache.nextDateLabel = String(nextDateLabel || '');
    }

    draws = cache.draws || [];
    nextRound = cache.nextRound || 0;
    nextDrawnAt = cache.nextDrawnAt || '';

    var newestCompleted = draws.length ? (parseInt(draws[0].round, 10) || 0) : 0;
    var showWaiting = nextRound > 0 && nextRound > newestCompleted;
    var maxCompleted = showWaiting ? (MAX_ROWS - 1) : MAX_ROWS;
    if (draws.length > maxCompleted) draws = draws.slice(0, maxCompleted);

    var html = '';
    if (showWaiting) html += waitingRowHtml(nextRound, nextDrawnAt);
    for (var i = 0; i < draws.length; i++) html += completedRowHtml(draws[i] || {});
    if (!html) {
      html = '<li class="m-draw-empty">' + escHtml(I18N().t('drawsEmpty')) + '</li>';
    }
    list.innerHTML = html;

    if (newestCompleted > 0) lastNewestRound = newestCompleted;
  }

  function rerender() { render(); }

  window.PBGM_Draws = {
    render: render,
    rerender: rerender,
    getLatest: function () {
      return (cache.draws && cache.draws.length) ? cache.draws[0] : null;
    },
  };
})();
