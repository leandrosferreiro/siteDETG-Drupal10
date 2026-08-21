/**
 * @file
 * DETG Theme JavaScript
 * Menu mobile, busca e âncoras.
 */
(function (Drupal, once) {
  'use strict';

  /**
   * Toggle do menu mobile.
   */
  Drupal.behaviors.detgMobileMenu = {
    attach: function (context) {
      once('detg-mobile-menu', '#mobile-menu-btn', context).forEach(function (btn) {
        var menu = document.getElementById('mobile-menu-content');
        if (!menu) {
          return;
        }

        btn.addEventListener('click', function () {
          var isOpen = menu.classList.toggle('is-open');
          btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
          btn.setAttribute('aria-label', isOpen ? Drupal.t('Fechar menu') : Drupal.t('Abrir menu'));
        });
      });
    }
  };

  /**
   * Busca do chrome — Enter envia para a busca do site.
   */
  Drupal.behaviors.detgMenuSearch = {
    attach: function (context) {
      once('detg-menu-search', '#search_menu', context).forEach(function (input) {
        input.addEventListener('keydown', function (e) {
          if (e.key === 'Enter') {
            var query = input.value.trim();
            if (query) {
              window.location.href = '/search?keys=' + encodeURIComponent(query);
            }
          }
        });
      });
    }
  };

  /**
   * Mantém o menu visível ao rolar (fallback se position:sticky falhar).
   */
  Drupal.behaviors.detgStickyChrome = {
    attach: function (context) {
      once('detg-sticky-chrome', '.site-chrome', context).forEach(function (header) {
        var sentinel = document.querySelector('.site-sticky-sentinel');
        if (!sentinel) {
          sentinel = document.createElement('div');
          sentinel.className = 'site-sticky-sentinel';
          sentinel.setAttribute('aria-hidden', 'true');
          header.parentNode.insertBefore(sentinel, header);
        }

        var spacer = document.createElement('div');
        spacer.className = 'site-chrome-spacer';
        spacer.setAttribute('aria-hidden', 'true');
        header.parentNode.insertBefore(spacer, header.nextSibling);

        function pin(shouldPin) {
          if (shouldPin) {
            spacer.style.height = header.offsetHeight + 'px';
            header.classList.add('is-fixed');
          }
          else {
            header.classList.remove('is-fixed');
            spacer.style.height = '0px';
          }
        }

        if ('IntersectionObserver' in window) {
          var observer = new IntersectionObserver(function (entries) {
            pin(!entries[0].isIntersecting);
          });
          observer.observe(sentinel);
        }
        else {
          var onScroll = function () {
            pin(window.scrollY > sentinel.offsetTop);
          };
          window.addEventListener('scroll', onScroll, { passive: true });
          onScroll();
        }
      });
    }
  };

  /**
   * Esconde o iframe antigo do Google Calendar se ainda estiver na sidebar.
   */
  Drupal.behaviors.detgHideLegacyCalendar = {
    attach: function (context) {
      once('detg-hide-calendar', '.sidebar-right iframe[src*="calendar.google"]', context).forEach(function (iframe) {
        var block = iframe.closest('.block, .widget-card, .calendar-widget');
        if (block && !block.classList.contains('agenda-widget')) {
          block.style.display = 'none';
        }
        else {
          iframe.style.display = 'none';
        }
      });
    }
  };

  /**
   * Formulário de contato — abre o cliente de e-mail com detg@ufba.br.
   */
  Drupal.behaviors.detgContactForm = {
    attach: function (context) {
      once('detg-contact-form', '#detg-contact-form', context).forEach(function (form) {
        form.addEventListener('submit', function (e) {
          e.preventDefault();
          var name = (form.querySelector('#detg-contact-name') || {}).value || '';
          var email = (form.querySelector('#detg-contact-email') || {}).value || '';
          var subject = (form.querySelector('#detg-contact-subject') || {}).value || '';
          var message = (form.querySelector('#detg-contact-message') || {}).value || '';
          var body = [
            'Nome: ' + name.trim(),
            'E-mail: ' + email.trim(),
            '',
            message.trim()
          ].join('\n');
          var href = 'mailto:detg@ufba.br'
            + '?subject=' + encodeURIComponent('[DETG] ' + subject)
            + '&body=' + encodeURIComponent(body);
          window.location.href = href;
        });
      });
    }
  };

  /**
   * Smooth scroll para âncoras internas.
   */
  Drupal.behaviors.detgSmoothScroll = {
    attach: function (context) {
      once('detg-smooth-scroll', 'a[href^="#"]', context).forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
          var href = this.getAttribute('href');
          if (!href || href === '#') {
            return;
          }
          var target = document.querySelector(href);
          if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth' });
          }
        });
      });
    }
  };

})(Drupal, once);
