/**
 * @file detg.js
 * Comportamentos JavaScript — Tema DETG/UFBA Drupal 10.4
 */
(function (Drupal, once) {
  'use strict';

  // Menu mobile toggle
  Drupal.behaviors.detgMobileMenu = {
    attach(context) {
      once('detg-mobile-menu', '#mobile-menu-btn', context).forEach(function (btn) {
        const content = document.getElementById('mobile-menu-content');
        if (!content) return;
        btn.addEventListener('click', function () {
          const open = content.classList.toggle('is-open');
          btn.setAttribute('aria-expanded', open);
        });
        document.addEventListener('keydown', function (e) {
          if (e.key === 'Escape' && content.classList.contains('is-open')) {
            content.classList.remove('is-open');
            btn.setAttribute('aria-expanded', 'false');
            btn.focus();
          }
        });
      });
    }
  };

  // Scroll suave para âncoras
  Drupal.behaviors.detgSmoothScroll = {
    attach(context) {
      once('detg-smooth-scroll', 'a[href^="#"]', context).forEach(function (link) {
        link.addEventListener('click', function (e) {
          const id = this.getAttribute('href');
          if (id === '#') return;
          const target = document.querySelector(id);
          if (!target) return;
          e.preventDefault();
          const nav = document.getElementById('main-nav');
          const offset = nav ? nav.offsetHeight + 12 : 60;
          window.scrollTo({ top: target.getBoundingClientRect().top + window.pageYOffset - offset, behavior: 'smooth' });
        });
      });
    }
  };

  // Busca no menu
  Drupal.behaviors.detgMenuSearch = {
    attach(context) {
      once('detg-search', '#search_menu', context).forEach(function (input) {
        input.addEventListener('keydown', function (e) {
          if (e.key === 'Enter') {
            e.preventDefault();
            const q = input.value.trim();
            if (q) window.location.href = '/search/node?keys=' + encodeURIComponent(q);
          }
        });
      });
    }
  };

  // Tabelas responsivas
  Drupal.behaviors.detgTables = {
    attach(context) {
      once('detg-table', '#main-content table', context).forEach(function (table) {
        if (table.closest('.table-wrap')) return;
        const wrap = document.createElement('div');
        wrap.style.cssText = 'overflow-x:auto;-webkit-overflow-scrolling:touch;margin-bottom:1rem;';
        table.parentNode.insertBefore(wrap, table);
        wrap.appendChild(table);
      });
    }
  };

})(Drupal, once);
