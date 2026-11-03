/**
 * SPIPHOTO FSE – Main JS
 * Vanilla JS, zero dependencies, deferred.
 */

'use strict';

(function () {

  // ─────────────────────────────────────────
  // Utilities
  // ─────────────────────────────────────────
  const qs  = (sel, ctx = document) => ctx.querySelector(sel);
  const qsa = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];
  const on  = (el, ev, fn, opts) => el?.addEventListener(ev, fn, opts);

  // ─────────────────────────────────────────
  // Reading Progress Bar
  // ─────────────────────────────────────────
  const progressBar = qs('#readingProgress');
  if (progressBar) {
    const updateProgress = () => {
      const el      = qs('.entry-content') || document.body;
      const top     = el.getBoundingClientRect().top + window.scrollY;
      const height  = el.offsetHeight;
      const scroll  = window.scrollY - top;
      const pct     = Math.min(100, Math.max(0, (scroll / height) * 100));
      progressBar.style.width = pct + '%';
      progressBar.setAttribute('aria-valuenow', Math.round(pct));
    };
    window.addEventListener('scroll', updateProgress, { passive: true });
  }

  // ─────────────────────────────────────────
  // Sticky Header Shadow
  // ─────────────────────────────────────────
  const header = qs('.site-header');
  if (header) {
    const onScroll = () => header.classList.toggle('scrolled', window.scrollY > 40);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // ─────────────────────────────────────────
  // Back to Top
  // ─────────────────────────────────────────
  const btt = qs('#backToTop');
  if (btt) {
    window.addEventListener('scroll', () => {
      btt.classList.toggle('is-visible', window.scrollY > 600);
    }, { passive: true });
    on(btt, 'click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
  }

  // ─────────────────────────────────────────
  // Search Overlay
  // ─────────────────────────────────────────
  const overlay    = qs('#searchOverlay');
  const overlayClose = qs('#searchOverlayClose');
  const searchBtns = qsa('.header-search-btn, [data-action="open-search"]');

  const openSearch  = () => {
    overlay?.classList.add('is-active');
    overlay?.querySelector('input')?.focus();
    document.body.style.overflow = 'hidden';
  };
  const closeSearch = () => {
    overlay?.classList.remove('is-active');
    document.body.style.overflow = '';
  };

  searchBtns.forEach(btn => on(btn, 'click', openSearch));
  on(overlayClose, 'click', closeSearch);
  on(overlay, 'click', e => { if (e.target === overlay) closeSearch(); });
  on(document, 'keydown', e => { if (e.key === 'Escape') closeSearch(); });

  // ─────────────────────────────────────────
  // Lazy Images (IntersectionObserver)
  // ─────────────────────────────────────────
  if ('IntersectionObserver' in window) {
    const imgObs = new IntersectionObserver((entries, obs) => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        const img = entry.target;
        if (img.dataset.src) img.src = img.dataset.src;
        if (img.dataset.srcset) img.srcset = img.dataset.srcset;
        img.classList.add('is-loaded');
        obs.unobserve(img);
      });
    }, { rootMargin: '200px' });

    qsa('img[data-src], img[data-srcset]').forEach(img => imgObs.observe(img));
  }

  // ─────────────────────────────────────────
  // Copy Link Button (Share)
  // ─────────────────────────────────────────
  qsa('.share-btn--copy').forEach(btn => {
    on(btn, 'click', async () => {
      try {
        await navigator.clipboard.writeText(window.location.href);
        const orig = btn.textContent;
        btn.textContent = spiphotoFSE?.i18n?.linkCopied || 'Copied!';
        setTimeout(() => { btn.textContent = orig; }, 2000);
      } catch {}
    });
  });

  // ─────────────────────────────────────────
  // Smooth Scroll for Anchor Links
  // ─────────────────────────────────────────
  on(document, 'click', e => {
    const anchor = e.target.closest('a[href^="#"]');
    if (!anchor) return;
    // getElementById plutôt que querySelector : « # » seul ou un identifiant
    // commençant par un chiffre faisaient échouer le sélecteur.
    const id = decodeURIComponent(anchor.hash.slice(1));
    if (!id) return;
    const target = document.getElementById(id);
    if (!target) return;
    e.preventDefault();
    const headerH = header?.offsetHeight || 0;
    const top = target.getBoundingClientRect().top + window.scrollY - headerH - 16;
    window.scrollTo({ top, behavior: 'smooth' });
    // Update focus for accessibility.
    target.setAttribute('tabindex', '-1');
    target.focus({ preventScroll: true });
  });

  // ─────────────────────────────────────────
  // Breaking News Ticker – pause on hover
  // ─────────────────────────────────────────
  const ticker = qs('.ticker-content');
  if (ticker) {
    const row = ticker.closest('.breaking-ticker');
    on(row, 'mouseenter', () => ticker.style.animationPlayState = 'paused');
    on(row, 'mouseleave', () => ticker.style.animationPlayState = 'running');
  }

  // ─────────────────────────────────────────
  // Animate-on-scroll (lightweight)
  // ─────────────────────────────────────────
  if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    const aosObs = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          aosObs.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });

    qsa('[data-aos]').forEach(el => {
      el.style.opacity = 0;
      el.style.transform = 'translateY(20px)';
      el.style.transition = 'opacity .5s ease, transform .5s ease';
      aosObs.observe(el);
    });

    // When visible.
    document.head.insertAdjacentHTML('beforeend', `<style>
      [data-aos].is-visible { opacity: 1 !important; transform: none !important; }
    </style>`);
  }

  // ─────────────────────────────────────────
  // Apparitions : cartes au défilement et photos en fondu
  // Rien n'est masqué sans JavaScript, ni pour les visiteurs qui demandent
  // moins d'animations. Ce qui est déjà à l'écran au chargement n'est jamais
  // masqué, pour éviter tout clignotement.
  // ─────────────────────────────────────────
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (!reduceMotion && 'IntersectionObserver' in window) {
    const viewH = window.innerHeight || document.documentElement.clientHeight;

    // 1. Cartes d'articles (accueil, archives, recherche, articles liés).
    const cards = qsa('.wp-block-post-template > li')
      .filter(li => li.querySelector('.post-card'))
      .filter(li => li.getBoundingClientRect().top > viewH);

    if (cards.length) {
      const revealObs = new IntersectionObserver((entries, obs) => {
        // Les cartes qui entrent ensemble à l'écran arrivent en léger décalé.
        entries
          .filter(entry => entry.isIntersecting)
          .forEach((entry, i) => {
            entry.target.style.setProperty('--sp-reveal-delay', Math.min(i, 3) * 0.08 + 's');
            entry.target.classList.add('is-revealed');
            obs.unobserve(entry.target);
          });
      }, { rootMargin: '0px 0px -8% 0px', threshold: 0.05 });

      cards.forEach(li => {
        li.classList.add('sp-reveal');
        revealObs.observe(li);
      });
    }

    // 2. Photos : fondu à la fin du chargement.
    const photoSelector = [
      '.wp-block-post-featured-image img',
      '.entry-content .wp-block-image img',
    ].join(',');

    qsa(photoSelector).forEach(img => {
      if (img.complete) return; // déjà chargée (cache, ou au-dessus de la ligne de flottaison)
      if (img.closest('.sp-article-header')) return; // a sa propre animation d'entrée (CSS)
      const show = () => img.classList.add('is-shown');
      img.addEventListener('load', show, { once: true });
      img.addEventListener('error', show, { once: true });
      img.classList.add('sp-img-fade');
      // Le chargement a pu se terminer entre le test et l'écoute.
      if (img.complete) show();
    });

    // 3. Filets de section : ceux qui sont visibles au chargement se tracent
    //    en CSS ; les autres attendent d'entrer dans l'écran.
    const lines = qsa('.section-header, .sp-page-header')
      .filter(el => el.getBoundingClientRect().top > viewH);

    if (lines.length) {
      const lineObs = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
          if (!entry.isIntersecting) return;
          entry.target.classList.add('is-drawn');
          obs.unobserve(entry.target);
        });
      }, { rootMargin: '0px 0px -10% 0px' });

      lines.forEach(el => {
        el.classList.add('sp-line-wait');
        lineObs.observe(el);
      });
    }
  }

  // ─────────────────────────────────────────
  // Table of Contents – active state
  // ─────────────────────────────────────────
  const tocLinks = qsa('.wp-block-table-of-contents a');
  if (tocLinks.length) {
    const headings = tocLinks.map(link => qs(link.getAttribute('href')));
    const tocObs = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          tocLinks.forEach(l => l.removeAttribute('aria-current'));
          const active = tocLinks.find(l => l.getAttribute('href') === '#' + entry.target.id);
          active?.setAttribute('aria-current', 'true');
        }
      });
    }, { rootMargin: '-80px 0px -60%' });
    headings.forEach(h => h && tocObs.observe(h));
  }

  // ─────────────────────────────────────────
  // Colonne de droite collante (accueil, categories)
  // Si elle tient dans l'ecran : figee a 24px du haut (sous la barre
  // d'administration le cas echeant). Si elle est plus haute : elle defile
  // avec la page jusqu'a ce que son bas soit visible, puis se fige en bas.
  // ─────────────────────────────────────────
  const sidebar = qs('.site-sidebar');
  if (sidebar) {
    const gap = 24;
    const placeSidebar = () => {
      const adminBar = qs('#wpadminbar');
      const admin = adminBar && getComputedStyle(adminBar).position === 'fixed' ? adminBar.offsetHeight : 0;
      const fits = gap + admin;
      const bottom = window.innerHeight - sidebar.offsetHeight - gap;
      sidebar.style.top = Math.min(fits, bottom) + 'px';
    };
    placeSidebar();
    window.addEventListener('resize', placeSidebar, { passive: true });
    window.addEventListener('load', placeSidebar);
    if ('ResizeObserver' in window) new ResizeObserver(placeSidebar).observe(sidebar);
  }

  // ─────────────────────────────────────────
  // Recherche : idees de mots-cles sous le champ
  // Le panneau est fourni par le PHP (functions.php), masque. Il s'ouvre
  // quand on clique dans le champ encore vide et se referme des qu'on tape,
  // qu'on quitte le champ ou qu'on appuie sur Echap.
  // ─────────────────────────────────────────
  qsa('.sp-search-suggest').forEach(panel => {
    const form  = panel.closest('form');
    const input = form && qs('.wp-block-search__input', form);
    if (!input) return;
    input.setAttribute('aria-controls', panel.id);
    input.setAttribute('aria-expanded', 'false');
    const show = visible => {
      panel.hidden = !visible;
      input.setAttribute('aria-expanded', String(visible));
    };
    on(input, 'focus', () => show(!input.value.trim()));
    on(input, 'input', () => show(!input.value.trim()));
    // Garde le focus dans le champ pendant le clic sur une idee : sinon le
    // panneau se refermerait avant que le lien ne soit suivi (Safari).
    on(panel, 'mousedown', e => e.preventDefault());
    on(form, 'focusout', e => { if (!form.contains(e.relatedTarget)) show(false); });
    on(form, 'keydown', e => {
      if (e.key === 'Escape' && !panel.hidden) { show(false); input.focus(); }
    });
  });

})();
