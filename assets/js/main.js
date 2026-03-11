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
  // Load More Posts (AJAX)
  // ─────────────────────────────────────────
  const loadMoreBtn = qs('[data-action="load-more"]');
  if (loadMoreBtn) {
    let page    = 2;
    let loading = false;

    on(loadMoreBtn, 'click', async () => {
      if (loading) return;
      loading = true;
      loadMoreBtn.setAttribute('aria-busy', 'true');
      loadMoreBtn.textContent = '…';

      try {
        const body = new FormData();
        body.append('action',   'spiphoto_load_more');
        body.append('nonce',    spiphotoFSE.nonce);
        body.append('page',     page);
        body.append('category', loadMoreBtn.dataset.category || '');
        body.append('perPage',  loadMoreBtn.dataset.perPage  || 6);

        const res  = await fetch(spiphotoFSE.ajaxUrl, { method: 'POST', body });
        const json = await res.json();

        if (json.success) {
          const container = qs('[data-post-grid]');
          if (container) {
            container.insertAdjacentHTML('beforeend', json.data.html);
            // Fade in new items.
            const newItems = qsa('[data-post-grid] > *:nth-last-child(-n+' + (json.data.html.match(/post-card/g)?.length || 6) + ')');
            newItems.forEach((el, i) => {
              el.style.opacity = 0;
              el.style.transform = 'translateY(16px)';
              el.style.transition = `opacity .4s ease ${i * 0.08}s, transform .4s ease ${i * 0.08}s`;
              requestAnimationFrame(() => { el.style.opacity = 1; el.style.transform = 'none'; });
            });
            page++;
          }
          if (!json.data.hasMore) loadMoreBtn.style.display = 'none';
          else loadMoreBtn.textContent = loadMoreBtn.dataset.label || 'Load More';
        } else {
          loadMoreBtn.style.display = 'none';
        }
      } catch (err) {
        console.error('Load more error:', err);
        loadMoreBtn.textContent = 'Error – try again';
      } finally {
        loading = false;
        loadMoreBtn.removeAttribute('aria-busy');
      }
    });
  }

  // ─────────────────────────────────────────
  // Smooth Scroll for Anchor Links
  // ─────────────────────────────────────────
  on(document, 'click', e => {
    const anchor = e.target.closest('a[href^="#"]');
    if (!anchor) return;
    const target = qs(anchor.hash);
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

})();
