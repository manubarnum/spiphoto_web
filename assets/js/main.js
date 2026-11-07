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
  // Colonne courte synchronisee avec la colonne longue
  // - accueil et categories : la colonne de droite (encadres) avec les articles ;
  // - article : la colonne du texte avec la galerie, souvent bien plus longue.
  // Decalage proportionnel a l'avancement dans la zone des colonnes : 0 quand
  // le haut des colonnes atteint le haut de l'ecran, (hauteur de la rangee -
  // hauteur de la colonne courte) quand leur bas atteint le bas de l'ecran.
  // Les bas coincident donc en fin de defilement, et la colonne courte reste
  // visible pendant tout le trajet. Rien ne bouge quand les colonnes sont
  // empilees (petit ecran), masquees, ou quand la colonne n'est pas la plus
  // courte.
  // ─────────────────────────────────────────
  const syncPairs = qsa('.site-sidebar, .sp-article-texte')
    .map(col => ({ col, row: col.closest('.wp-block-columns') }))
    .filter(pair => pair.row);
  if (syncPairs.length) {
    let ticking = false;
    const syncColumns = () => {
      ticking = false;
      const adminBar = qs('#wpadminbar');
      const admin = adminBar && getComputedStyle(adminBar).position === 'fixed' ? adminBar.offsetHeight : 0;
      const view = window.innerHeight - admin;
      syncPairs.forEach(({ col, row }) => {
        const stacked = getComputedStyle(row).flexDirection.startsWith('column');
        const travel  = row.offsetHeight - col.offsetHeight; // marge de deplacement
        const range   = row.offsetHeight - view;             // defilement disponible
        if (stacked || !col.offsetHeight || travel <= 0 || range <= 0) {
          col.style.transform = '';
          return;
        }
        const rowTop = row.getBoundingClientRect().top + window.scrollY - admin;
        const p = Math.min(1, Math.max(0, (window.scrollY - rowTop) / range));
        col.style.transform = 'translateY(' + Math.round(p * travel) + 'px)';
      });
    };
    const request = () => { if (!ticking) { ticking = true; requestAnimationFrame(syncColumns); } };
    window.addEventListener('scroll', request, { passive: true });
    window.addEventListener('resize', request, { passive: true });
    window.addEventListener('load', request);
    if ('ResizeObserver' in window) {
      const ro = new ResizeObserver(request);
      syncPairs.forEach(({ col, row }) => { ro.observe(col); ro.observe(row); });
    }
    request();
  }

  // ─────────────────────────────────────────
  // En-tete : bouton Jour/Nuit, ombre au defilement, lien actif
  // (anciennement dans un bloc HTML de parts/header.html). L'etat initial du
  // theme est pose avant tout rendu par theme_antiflash() (functions.php,
  // wp_head priorite 1) : c'est ce qui evite le flash, ne pas le deplacer ici.
  // ─────────────────────────────────────────
  (function headerUi() {
    const KEY = 'spiphoto-theme';
    const root = document.documentElement;
    const applyTheme = t => {
      root.setAttribute('data-theme', t);
      try { localStorage.setItem(KEY, t); } catch (e) { /* stockage indisponible */ }
    };

    // Bouton Jour/Nuit : range dans l'en-tete, apres la recherche de la barre
    // du haut, ou sous 900px juste avant la loupe de la ligne du logo (la
    // barre du haut y est masquee). Sans l'un ni l'autre, il reste flottant.
    // Seulement avec l'en-tete du theme (pas sur le modele « Vide »), comme
    // lorsque ce code etait dans parts/header.html.
    if (qs('.site-header') && !document.getElementById('theme-toggle')) {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'theme-toggle';
      btn.id = 'theme-toggle';
      btn.setAttribute('aria-label', 'Basculer thème clair/sombre');
      btn.innerHTML =
        '<svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="13" height="13" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>' +
        '<svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="13" height="13" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>' +
        '<span class="toggle-label-dark">Jour</span>' +
        '<span class="toggle-label-light">Nuit</span>';
      btn.addEventListener('click', () => applyTheme(root.getAttribute('data-theme') === 'light' ? 'dark' : 'light'));
      document.body.appendChild(btn);

      const loupe  = qs('.sp-search-mobile');
      const topbar = qs('.topbar-right');
      const small  = window.matchMedia('(max-width: 900px)');
      const placeToggle = () => {
        btn.classList.remove('theme-toggle--inline', 'theme-toggle--topbar');
        if (small.matches && loupe) {
          loupe.parentNode.insertBefore(btn, loupe);
          btn.classList.add('theme-toggle--inline');
        } else if (!small.matches && topbar) {
          topbar.appendChild(btn);
          btn.classList.add('theme-toggle--topbar');
        } else if (btn.parentNode !== document.body) {
          document.body.appendChild(btn);
        }
      };
      placeToggle();
      if (small.addEventListener) small.addEventListener('change', placeToggle);
    }

    // Ombre de l'en-tete apres 40px de defilement.
    const header = qs('.site-header');
    if (header) {
      window.addEventListener('scroll', () => header.classList.toggle('scrolled', window.scrollY > 40), { passive: true });
    }

    // Lien actif des menus. Les liens sont des adresses completes : on compare
    // leur chemin (l'ancienne version comparait l'adresse complete au chemin
    // de la page et ne marquait jamais rien).
    const path = window.location.pathname;
    qsa('.cats-navigation a, .topbar-navigation a, .topbar-navigation-mobile a').forEach(a => {
      let linkPath;
      try { linkPath = new URL(a.getAttribute('href') || '', window.location.href).pathname; } catch (e) { return; }
      if (linkPath && linkPath !== '/' && path.startsWith(linkPath)) {
        a.classList.add('active');
        a.setAttribute('aria-current', 'page');
      }
    });
  })();

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
