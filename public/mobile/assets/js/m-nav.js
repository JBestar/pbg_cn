/**
 * Mobile bottom navigation bar.
 * Icons are isolated in .m-nav-ico so SVG files / Font Awesome can replace them later.
 */
(function () {
  'use strict';

  var ITEMS = [
    { id: 'home', i18n: 'navHome', page: 'home' },
    { id: 'bet', i18n: 'navBet', page: 'bet' },
    { id: 'mgmt', i18n: 'navMgmt', page: 'mgmt' },
    { id: 'lang', i18n: 'navLang', page: null, action: 'lang' },
    { id: 'logout', i18n: 'navLogout', page: null, action: 'logout' },
  ];

  var activeId = 'home';
  var handlers = { page: null, lang: null, logout: null };

  function $(sel, root) {
    return (root || document).querySelector(sel);
  }

  function $all(sel, root) {
    return Array.prototype.slice.call((root || document).querySelectorAll(sel));
  }

  function setActive(id) {
    var pageItem = ITEMS.filter(function (it) { return it.page; }).some(function (it) { return it.id === id; });
    if (pageItem) activeId = id;
    $all('.m-nav-item').forEach(function (btn) {
      var isActive = btn.getAttribute('data-nav') === activeId;
      btn.classList.toggle('is-active', isActive);
      btn.setAttribute('aria-current', isActive ? 'page' : 'false');
    });
  }

  function onClick(ev) {
    var btn = ev.currentTarget;
    var id = btn.getAttribute('data-nav');
    var item = ITEMS.filter(function (it) { return it.id === id; })[0];
    if (!item) return;

    if (item.action === 'lang') {
      if (typeof handlers.lang === 'function') handlers.lang();
      return;
    }
    if (item.action === 'logout') {
      if (typeof handlers.logout === 'function') handlers.logout();
      return;
    }
    setActive(id);
    if (typeof handlers.page === 'function') handlers.page(item.page, id);
  }

  function bind() {
    $all('.m-nav-item').forEach(function (btn) {
      btn.addEventListener('click', onClick);
    });
    setActive(activeId);
  }

  function show(visible) {
    var nav = $('nav.m-nav');
    if (!nav) return;
    nav.hidden = !visible;
    document.body.classList.toggle('m-has-nav', !!visible);
  }

  window.PBGM_Nav = {
    bind: bind,
    setActive: setActive,
    show: show,
    getActive: function () { return activeId; },
    onPage: function (fn) { handlers.page = fn; },
    onLang: function (fn) { handlers.lang = fn; },
    onLogout: function (fn) { handlers.logout = fn; },
  };
})();
