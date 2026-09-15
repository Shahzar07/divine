/* ============================================================================
   Divine Beauty & Nails By Dee — site behaviour
   Vanilla JS, no dependencies. Shared by index.html, services.html and
   portfolio.html; every block is guarded so a page without that markup is fine.
   ========================================================================== */
'use strict';

(function () {

  const $  = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));
  const reduceMotion = matchMedia('(prefers-reduced-motion: reduce)');
  const scrollBehavior = () => (reduceMotion.matches ? 'auto' : 'smooth');

  /* ---------------------------------------------------------------- year -- */
  const year = $('#year');
  if (year) year.textContent = String(new Date().getFullYear());

  /* -------------------------------------------------------------- header -- */
  const header = $('#site-header');
  if (header) {
    const onScroll = () => header.classList.toggle('is-stuck', window.scrollY > 12);
    addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* ---------------------------------------------------------- navigation -- */
  const nav = $('#primary-nav');
  const navToggle = $('.nav-toggle');
  const navScrim = $('.nav-scrim');

  if (nav && navToggle) {
    const setNav = (open) => {
      document.body.classList.toggle('nav-open', open);
      navToggle.setAttribute('aria-expanded', String(open));
      navToggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
      if (navScrim) navScrim.hidden = !open;
      if (open) {
        const first = $('.nav-link', nav);
        if (first) first.focus({ preventScroll: true });
      }
    };

    navToggle.addEventListener('click', () => setNav(!document.body.classList.contains('nav-open')));
    $$('.nav-link', nav).forEach((a) => a.addEventListener('click', () => setNav(false)));
    const navClose = $('.nav-close', nav);
    if (navClose) navClose.addEventListener('click', () => { setNav(false); navToggle.focus(); });
    if (navScrim) navScrim.addEventListener('click', () => setNav(false));

    addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && document.body.classList.contains('nav-open')) {
        setNav(false);
        navToggle.focus();
      }
    });
    // A resize past the desktop breakpoint must not leave the drawer latched open.
    matchMedia('(min-width: 1021px)').addEventListener('change', (e) => { if (e.matches) setNav(false); });
  }

  /* ------------------------------------------------------ reveal on scroll */
  const revealables = $$('[data-reveal]');
  if (revealables.length) {
    if (reduceMotion.matches || !('IntersectionObserver' in window)) {
      revealables.forEach((el) => el.classList.add('in'));
    } else {
      const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          // Reveal on entry, but also catch anything already scrolled past —
          // a fast flick must never leave a section invisible.
          if (!entry.isIntersecting && entry.boundingClientRect.top > 0) return;
          entry.target.classList.add('in');
          io.unobserve(entry.target);
        });
      }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
      revealables.forEach((el) => io.observe(el));
    }
  }

  /* ------------------------------------------------------------ carousels -- */
  /* One implementation drives the service rail, the work rail and the
     testimonial track: a scroll-snapping row plus arrows and dots. */
  $$('[data-carousel]').forEach((root) => {
    const rail = $('[data-rail]', root);
    if (!rail) return;

    const prev = $('[data-prev]', root);
    const next = $('[data-next]', root);
    const dots = $('[data-dots]', root);
    const slides = Array.from(rail.children);
    const autoplayMs = Number(root.dataset.autoplay || 0);
    let autoplayId = null;
    let autoplayDismissed = false;   // set once the reader takes control

    const step = () => {
      const first = slides[0];
      if (!first) return rail.clientWidth;
      const gap = parseFloat(getComputedStyle(rail).columnGap) || 0;
      return first.getBoundingClientRect().width + gap;
    };

    const perView = () => Math.max(1, Math.round(rail.clientWidth / step()));

    const pageCount = () => Math.max(1, slides.length - perView() + 1);

    const activeIndex = () => {
      const s = step();
      return s ? Math.round(rail.scrollLeft / s) : 0;
    };

    /* dots */
    let dotButtons = [];
    const buildDots = () => {
      if (!dots) return;
      const count = pageCount();
      if (dotButtons.length === count) return;
      dots.textContent = '';
      dotButtons = Array.from({ length: count }, (_, i) => {
        const b = document.createElement('button');
        b.type = 'button';
        b.setAttribute('role', 'tab');
        b.setAttribute('aria-label', `Go to slide ${i + 1} of ${count}`);
        b.addEventListener('click', () => {
          rail.scrollTo({ left: i * step(), behavior: scrollBehavior() });
          stopAutoplay(true);
        });
        dots.appendChild(b);
        return b;
      });
    };

    const sync = () => {
      const max = rail.scrollWidth - rail.clientWidth;
      const atStart = rail.scrollLeft <= 4;
      const atEnd = rail.scrollLeft >= max - 4;
      if (prev) prev.disabled = atStart && !autoplayMs;
      if (next) next.disabled = atEnd && !autoplayMs;
      const i = Math.min(activeIndex(), dotButtons.length - 1);
      dotButtons.forEach((b, n) => b.setAttribute('aria-selected', String(n === i)));
    };

    const move = (dir) => {
      const max = rail.scrollWidth - rail.clientWidth;
      let target = rail.scrollLeft + dir * step() * (autoplayMs ? 1 : Math.max(1, perView() - 1));
      // wrap around for the auto-rotating testimonial track
      if (autoplayMs) {
        if (target > max + 4) target = 0;
        else if (target < -4) target = max;
      }
      rail.scrollTo({ left: Math.max(0, Math.min(target, max)), behavior: scrollBehavior() });
    };

    if (prev) prev.addEventListener('click', () => { move(-1); stopAutoplay(true); });
    if (next) next.addEventListener('click', () => { move(1); stopAutoplay(true); });

    rail.addEventListener('scroll', () => requestAnimationFrame(sync), { passive: true });
    rail.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowRight') { e.preventDefault(); move(1); stopAutoplay(true); }
      if (e.key === 'ArrowLeft')  { e.preventDefault(); move(-1); stopAutoplay(true); }
    });

    /* autoplay — testimonials only, and never while the reader is interacting */
    function startAutoplay() {
      if (!autoplayMs || autoplayId || autoplayDismissed || reduceMotion.matches) return;
      autoplayId = setInterval(() => {
        if (document.hidden) return;
        move(1);
      }, autoplayMs);
    }
    function stopAutoplay(permanent) {
      if (permanent === true) autoplayDismissed = true;
      if (!autoplayId) return;
      clearInterval(autoplayId);
      autoplayId = null;
    }
    if (autoplayMs) {
      ['pointerenter', 'focusin', 'touchstart'].forEach((evt) =>
        root.addEventListener(evt, () => stopAutoplay(true), { passive: true }));
      // Only rotate while the carousel is actually on screen.
      if ('IntersectionObserver' in window) {
        new IntersectionObserver((entries) => {
          entries.forEach((e) => (e.isIntersecting ? startAutoplay() : stopAutoplay(false)));
        }, { threshold: 0.4 }).observe(root);
      } else {
        startAutoplay();
      }
    }

    const refresh = () => { buildDots(); sync(); };
    refresh();
    addEventListener('resize', refresh);
    if ('ResizeObserver' in window) new ResizeObserver(refresh).observe(rail);
  });

  /* --------------------------------------------------------------- video -- */
  /* Films are never downloaded until they are needed: the poster carries the
     section until the reader scrolls to it, or presses play. */
  const videos = $$('video[data-src]');

  const syncPlayer = (video) => {
    const btn = $(`[data-player="${video.id}"]`);
    if (!btn) return;
    const paused = video.paused;
    $('.player-label', btn).textContent = paused ? 'Play film' : 'Pause film';
    $('.glyph', btn).innerHTML = paused ? '&#9654;' : '&#10073;&#10073;';
    btn.setAttribute('aria-label', `${paused ? 'Play' : 'Pause'} film`);
  };

  const loadVideo = (video) => {
    if (video.dataset.loaded) return;
    video.src = video.dataset.src;
    video.dataset.loaded = 'true';
    video.load();
  };

  const mayAutoplay = () =>
    !reduceMotion.matches && !(navigator.connection && navigator.connection.saveData);

  if (videos.length) {
    videos.forEach((video) => {
      ['play', 'pause'].forEach((t) => video.addEventListener(t, () => syncPlayer(video)));
      video.addEventListener('error', () => {
        const btn = $(`[data-player="${video.id}"]`);
        if (!btn) return;
        $('.player-label', btn).textContent = 'Film unavailable';
        btn.disabled = true;
      });
      syncPlayer(video);
    });

    if ('IntersectionObserver' in window) {
      const vo = new IntersectionObserver((entries) => {
        entries.forEach(({ target: video, isIntersecting }) => {
          video.dataset.onscreen = String(isIntersecting);
          if (isIntersecting && mayAutoplay() && video.dataset.userPaused !== 'true') {
            loadVideo(video);
            video.play().catch(() => syncPlayer(video));
          } else {
            video.pause();
          }
        });
      }, { threshold: 0.25 });
      videos.forEach((v) => vo.observe(v));
    }

    $$('.player-btn').forEach((btn) => btn.addEventListener('click', () => {
      const video = document.getElementById(btn.dataset.player);
      if (!video) return;
      if (video.paused) {
        video.dataset.userPaused = 'false';
        loadVideo(video);
        video.play().catch(() => syncPlayer(video));
      } else {
        video.dataset.userPaused = 'true';
        video.pause();
      }
    }));

    document.addEventListener('visibilitychange', () => {
      videos.forEach((v) => {
        if (document.hidden) v.pause();
        else if (v.dataset.onscreen === 'true' && v.dataset.userPaused !== 'true' && mayAutoplay()) {
          v.play().catch(() => {});
        }
      });
    });

    reduceMotion.addEventListener('change', () => {
      if (reduceMotion.matches) videos.forEach((v) => v.pause());
    });
  }

  /* ------------------------------------------------------------- dialogs -- */
  const openDialog = (dialog) => {
    if (!dialog) return;
    dialog.showModal();
    document.body.classList.add('modal-open');
  };

  $$('dialog').forEach((dialog) => {
    dialog.addEventListener('close', () => document.body.classList.remove('modal-open'));
    const close = $('.dialog-close', dialog);
    if (close) close.addEventListener('click', () => dialog.close());
    // Clicking the backdrop (outside the dialog box) closes it.
    dialog.addEventListener('click', (e) => {
      if (e.target !== dialog) return;
      const r = dialog.getBoundingClientRect();
      const outside = e.clientX < r.left || e.clientX > r.right || e.clientY < r.top || e.clientY > r.bottom;
      if (outside) dialog.close();
    });
  });

  /* --------------------------------------------------- portfolio filters -- */
  const workGrid = $('#work-grid');
  if (workGrid) {
    const items = $$('.work', workGrid);
    const count = $('#work-count');
    const empty = $('#work-empty');

    $$('.filters [data-filter]').forEach((btn) => btn.addEventListener('click', () => {
      const filter = btn.dataset.filter;
      $$('.filters [data-filter]').forEach((b) => b.setAttribute('aria-pressed', String(b === btn)));
      let shown = 0;
      items.forEach((item) => {
        const match = filter === 'all' || item.dataset.category === filter;
        item.hidden = !match;
        if (match) shown++;
      });
      if (count) count.textContent = `${shown} ${shown === 1 ? 'piece' : 'pieces'} of work shown`;
      if (empty) empty.hidden = shown > 0;
    }));
  }

  /* ------------------------------------------------------- work lightbox -- */
  const lookDialog = $('#look-dialog');
  let chosen = null;

  if (lookDialog) {
    const media = $('#dialog-media');
    const title = $('#look-title');
    const text = $('#dialog-text');
    const eyebrow = $('#dialog-eyebrow');
    const useLook = $('#use-look');

    const decode = (s) => {
      const el = document.createElement('textarea');
      el.innerHTML = s || '';
      return el.value;
    };

    $$('.work').forEach((work) => {
      const trigger = $('.work-open', work);
      if (!trigger) return;
      trigger.addEventListener('click', () => {
        chosen = {
          title: decode(work.dataset.title),
          service: decode(work.dataset.service),
          src: work.dataset.media,
          alt: work.dataset.alt || ''
        };
        media.textContent = '';
        const img = document.createElement('img');
        img.src = chosen.src;
        img.alt = chosen.alt;
        media.appendChild(img);
        title.textContent = chosen.title;
        text.textContent = decode(work.dataset.text);
        eyebrow.textContent = chosen.service;
        useLook.href = `index.html?service=${encodeURIComponent(chosen.service)}&look=${encodeURIComponent(chosen.title)}#booking`;
        openDialog(lookDialog);
      });
    });
  }

  /* ------------------------------------------------------- info dialogs --- */
  const infoDialog = $('#info-dialog');
  if (infoDialog) {
    const INFO = {
      privacy: {
        title: 'Your privacy',
        html: `
          <p>The enquiry form prepares a message inside your browser. Your details are not submitted to a
             booking system and are not stored by this website.</p>
          <p>When you choose <em>Open email app</em>, your own email app opens a draft addressed to the studio.
             You decide whether to send it. The studio receives your details only once you do.</p>
          <p>This site uses no advertising or analytics cookies. Fonts are requested from Google Fonts, and
             your hosting provider may process ordinary technical request information.</p>
          <p>For questions about an enquiry you have already sent, contact
             <a href="mailto:hello@divinebeautybydee.com">hello@divinebeautybydee.com</a>.</p>`
      },
      credits: {
        title: 'About the imagery',
        html: `
          <p>Treatment, result and client photography — massage, cupping, facials, nails and makeup — was
             supplied by Divine Beauty &amp; Nails By Dee and shows work carried out at the studio.
             Client images are published with permission.</p>
          <p>The Ganesha emblem and the studio logo are the client's own artwork.</p>
          <p>A small number of styled nail and makeup images are editorial mood photography rather than
             studio work, and appear only on the home and services pages. Every image in the portfolio is
             the studio's own.</p>
          <p>Films of nail application are used under the
             <a href="https://www.pexels.com/license/" target="_blank" rel="noopener">Pexels licence</a>.</p>`
      }
    };

    $$('[data-info]').forEach((btn) => btn.addEventListener('click', () => {
      const entry = INFO[btn.dataset.info];
      if (!entry) return;
      $('#info-title').textContent = entry.title;
      $('#info-content').innerHTML = entry.html;
      openDialog(infoDialog);
    }));
  }

  /* ------------------------------------------------------- booking form --- */
  const form = $('#booking-form');
  if (!form) return;

  const serviceSelect = $('#service-select');
  const dateInput = $('#preferred-date');
  const chosenLook = $('#selected-look');
  const result = $('#enquiry-result');
  const preview = $('#enquiry-preview');
  const sendLink = $('#send-email');
  const copyBtn = $('#copy-enquiry');
  const copyStatus = $('#copy-status');
  const STUDIO_EMAIL = 'hello@divinebeautybydee.com';

  /* no past dates */
  if (dateInput) {
    const t = new Date();
    dateInput.min = [
      t.getFullYear(),
      String(t.getMonth() + 1).padStart(2, '0'),
      String(t.getDate()).padStart(2, '0')
    ].join('-');
    dateInput.addEventListener('input', () => dateInput.setCustomValidity(''));
  }

  /* prefill from ?service= / ?look= so "Book now" buttons land ready to send */
  let inspiration = null;
  const params = new URLSearchParams(location.search);
  const wantedService = params.get('service');
  const wantedLook = params.get('look');

  if (wantedService && serviceSelect) {
    const match = Array.from(serviceSelect.options)
      .find((o) => o.value && o.value.toLowerCase() === wantedService.toLowerCase());
    if (match) serviceSelect.value = match.value;
  }
  if (wantedLook) {
    inspiration = { name: wantedLook };
    if (chosenLook) {
      $('strong', chosenLook).textContent = wantedLook;
      chosenLook.hidden = false;
    }
  }

  const clearLook = $('#clear-look');
  if (clearLook) clearLook.addEventListener('click', () => {
    inspiration = null;
    if (chosenLook) chosenLook.hidden = true;
  });

  let enquiry = '';

  form.addEventListener('submit', (e) => {
    e.preventDefault();

    if (dateInput && dateInput.value && dateInput.value < dateInput.min) {
      dateInput.setCustomValidity('Please choose today or a future date.');
      dateInput.reportValidity();
      return;
    }
    if (dateInput) dateInput.setCustomValidity('');
    if (!form.reportValidity()) return;

    const data = new FormData(form);
    const get = (k) => String(data.get(k) || '').trim();

    const lines = [
      'Hello Dee,',
      '',
      "I'd like to enquire about an appointment.",
      '',
      `Name: ${get('name')}`,
      `Email: ${get('email')}`
    ];
    if (get('phone')) lines.push(`Phone: ${get('phone')}`);
    lines.push(`Treatment: ${get('service')}`);
    if (get('date')) lines.push(`Preferred date: ${get('date')}`);
    if (inspiration) lines.push(`Inspiration from the portfolio: ${inspiration.name}`);
    if (get('message')) lines.push('', get('message'));
    lines.push('', 'Please let me know your pricing and availability.', 'Thank you!');

    enquiry = lines.join('\n');
    preview.textContent = enquiry;
    sendLink.href =
      `mailto:${STUDIO_EMAIL}?subject=${encodeURIComponent('Appointment enquiry · ' + get('service'))}` +
      `&body=${encodeURIComponent(enquiry)}`;

    result.hidden = false;
    if (copyStatus) copyStatus.textContent = '';
    result.focus({ preventScroll: true });
    result.scrollIntoView({ behavior: scrollBehavior(), block: 'center' });
  });

  if (copyBtn) copyBtn.addEventListener('click', async () => {
    try {
      await navigator.clipboard.writeText(enquiry);
      copyStatus.textContent = 'Message copied. Paste it into your email to Dee.';
    } catch {
      copyStatus.textContent = 'Select and copy the message above, or choose “Open email app”.';
    }
  });

})();
