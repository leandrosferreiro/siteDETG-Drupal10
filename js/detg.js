/**
 * @file
 * DETG Theme JavaScript
 * Responsável pelo menu mobile e interações da página.
 */
(function ($, Drupal) {
  'use strict';

  /**
   * Toggle do menu mobile.
   */
  Drupal.behaviors.detgMobileMenu = {
    attach: function (context, settings) {
      var btn = document.getElementById('mobile-menu-btn');
      var menu = document.getElementById('mobile-menu-content');

      if (!btn || !menu) return;

      btn.addEventListener('click', function () {
        menu.classList.toggle('is-open');
        var isOpen = menu.classList.contains('is-open');
        btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      });
    }
  };

  /**
   * Busca no menu — filtra links pelo termo digitado.
   */
  Drupal.behaviors.detgMenuSearch = {
    attach: function (context, settings) {
      var input = document.getElementById('search_menu');
      if (!input) return;

      input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
          var query = input.value.trim();
          if (query) {
            window.location.href = '/search?keys=' + encodeURIComponent(query);
          }
        }
      });
    }
  };

  /**
   * Smooth scroll para âncoras internas.
   */
  Drupal.behaviors.detgSmoothScroll = {
    attach: function (context, settings) {
      var anchors = context.querySelectorAll('a[href^="#"]');
      anchors.forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
          var target = document.querySelector(this.getAttribute('href'));
          if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth' });
          }
        });
      });
    }
  };

})(jQuery, Drupal);
