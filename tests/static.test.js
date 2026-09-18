const { chromium } = require('playwright');

const BASE = 'http://127.0.0.1:8000';
const PAGES = ['index', 'services', 'portfolio', '404'];
const WIDTHS = [320, 360, 390, 768, 1024, 1440];

let pass = 0, fail = 0;
const ok  = (m) => { pass++; console.log('  PASS ' + m); };
const bad = (m) => { fail++; console.log('  FAIL ' + m); };

(async () => {
  const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium-1194/chrome-linux/chrome' });
  const ctx = await b.newContext({ viewport: { width: 1440, height: 950 } });
  const p = await ctx.newPage();

  /* Two classes of noise are specific to this sandbox, not to the site:
     - the egress proxy re-terminates TLS, so Chromium rejects fonts.googleapis.com;
     - python -m http.server answers a Range request with 200 rather than 206,
       so Chromium aborts the video stream it was trying to seek in.
     Both behave correctly on real hosting, so they are filtered out here. */
  const sandboxNoise = (s) => /fonts\.(googleapis|gstatic)\.com/.test(s)
                           || /ERR_CERT_AUTHORITY_INVALID/.test(s)
                           || /\.mp4/.test(s);

  const errors = [];
  const missing = [];
  p.on('console', (m) => { if (m.type() === 'error' && !sandboxNoise(m.text())) errors.push(m.text()); });
  p.on('requestfailed', (r) => { if (!sandboxNoise(r.url())) missing.push(r.url()); });
  p.on('response', (r) => { if (r.status() >= 400 && !sandboxNoise(r.url())) missing.push(`${r.status()} ${r.url()}`); });

  console.log('\n== console + requests ==');
  for (const page of PAGES) {
    await p.goto(`${BASE}/${page}.html`, { waitUntil: 'networkidle' });
    await p.evaluate(() => new Promise(r => { window.scrollTo(0, 99999); setTimeout(r, 900); }));
  }
  errors.length ? bad('console errors: ' + errors.slice(0,4).join(' | ')) : ok('no console errors on any page');
  missing.length ? bad('failed requests: ' + missing.slice(0,4).join(' | ')) : ok('no failed requests');

  console.log('\n== no horizontal overflow ==');
  for (const page of PAGES) {
    for (const w of WIDTHS) {
      await p.setViewportSize({ width: w, height: 900 });
      await p.goto(`${BASE}/${page}.html`, { waitUntil: 'domcontentloaded' });
      const over = await p.evaluate(() => document.documentElement.scrollWidth - document.documentElement.clientWidth);
      if (over > 1) bad(`${page} @${w}px overflows by ${over}px`);
    }
  }
  ok('checked ' + PAGES.length * WIDTHS.length + ' page/width combinations');

  await p.setViewportSize({ width: 1440, height: 950 });

  console.log('\n== imagery policy ==');
  for (const page of ['index', 'services']) {
    await p.goto(`${BASE}/${page}.html`, { waitUntil: 'networkidle' });
    const srcs = await p.$$eval('img', els => els.map(e => e.getAttribute('src')));
    const leaked = srcs.filter(s => /massage-benefits|cupping-back|facial-|nails-burgundy|glam-(portrait|studio)-poster|about\.jpg|g[1236]-/.test(s || ''));
    leaked.length ? bad(`${page} still shows client photos: ${leaked.join(', ')}`) : ok(`${page} uses commissioned photography only`);
  }
  await p.goto(`${BASE}/portfolio.html`, { waitUntil: 'networkidle' });
  const pf = await p.$$eval('#work-grid img, .player video', els => els.map(e => e.getAttribute('src') || e.getAttribute('data-src') || e.getAttribute('poster')));
  const stock = pf.filter(s => /\/(hero|svc|band|studio)-[a-z-]+\.jpg$/.test(s || ''));
  stock.length ? bad('portfolio shows stock: ' + stock.join(', ')) : ok('portfolio shows only the studio\'s own media');

  console.log('\n== images actually render ==');
  for (const page of PAGES) {
    await p.goto(`${BASE}/${page}.html`, { waitUntil: 'networkidle' });
    await p.evaluate(() => new Promise(r => { window.scrollTo(0, 99999); setTimeout(r, 1200); }));
    const broken = await p.$$eval('img', els =>
      els.filter(e => e.complete && e.naturalWidth === 0).map(e => e.getAttribute('src')));
    broken.length ? bad(`${page}: broken images ${broken.join(', ')}`) : ok(`${page}: every image decoded`);
    const noAlt = await p.$$eval('img', els => els.filter(e => !e.hasAttribute('alt')).length);
    noAlt ? bad(`${page}: ${noAlt} images without alt`) : ok(`${page}: all images have alt text`);
  }

  console.log('\n== portfolio filters ==');
  await p.goto(`${BASE}/portfolio.html`, { waitUntil: 'networkidle' });
  const total = await p.$$eval('.piece', e => e.length);
  total === 7 ? ok(`7 pieces present`) : bad(`expected 7 pieces, found ${total}`);
  await p.click('[data-filter="skin"]');
  await p.waitForTimeout(250);
  const shown = await p.$$eval('.piece:not([hidden])', e => e.length);
  shown === 4 ? ok('skin filter shows 4') : bad(`skin filter shows ${shown}, expected 4`);
  const pressed = await p.$eval('[data-filter="skin"]', e => e.getAttribute('aria-pressed'));
  pressed === 'true' ? ok('filter marks itself pressed') : bad('filter aria-pressed not set');
  await p.click('[data-filter="all"]');
  await p.waitForTimeout(250);
  const back = await p.$$eval('.piece:not([hidden])', e => e.length);
  back === 7 ? ok('all filter restores 7') : bad(`all filter shows ${back}`);

  console.log('\n== lightbox ==');
  await p.click('.piece:not([hidden]) .piece-open');
  await p.waitForTimeout(400);
  const open = await p.$eval('#look-dialog', e => e.open);
  open ? ok('dialog opens') : bad('dialog did not open');
  const t1 = await p.$eval('#look-title', e => e.textContent.trim());
  t1 ? ok(`title populated: "${t1}"`) : bad('title empty');
  const counter = await p.$eval('#dialog-counter', e => e.textContent.trim());
  /^\d+ \/ 7$/.test(counter) ? ok(`counter reads "${counter}"`) : bad(`counter reads "${counter}"`);
  await p.click('#dialog-next');
  await p.waitForTimeout(300);
  const t2 = await p.$eval('#look-title', e => e.textContent.trim());
  t2 && t2 !== t1 ? ok(`next advances to "${t2}"`) : bad('next did not advance');
  await p.keyboard.press('ArrowLeft');
  await p.waitForTimeout(300);
  const t3 = await p.$eval('#look-title', e => e.textContent.trim());
  t3 === t1 ? ok('left arrow returns') : bad(`left arrow gave "${t3}"`);
  const href = await p.$eval('#use-look', e => e.getAttribute('href'));
  /service=.+look=.+#booking/.test(href) ? ok('CTA carries service and look') : bad(`CTA href: ${href}`);
  await p.keyboard.press('Escape');
  await p.waitForTimeout(300);
  const closed = await p.$eval('#look-dialog', e => !e.open);
  closed ? ok('escape closes dialog') : bad('escape did not close');

  console.log('\n== whatsapp button ==');
  for (const page of PAGES) {
    await p.goto(`${BASE}/${page}.html`, { waitUntil: 'domcontentloaded' });
    const wa = await p.$('#whatsapp-float');
    if (!wa) { bad(`${page}: no WhatsApp button`); continue; }
    const h = await wa.getAttribute('href');
    /^https:\/\/wa\.me\/\d{10,}\?text=/.test(h) ? ok(`${page}: WhatsApp link valid`) : bad(`${page}: bad href ${h}`);
  }
  const visible = await p.$eval('#whatsapp-float', e => {
    const r = e.getBoundingClientRect();
    return r.width > 0 && r.bottom <= innerHeight + 1 && r.right <= innerWidth + 1;
  });
  visible ? ok('WhatsApp button sits inside the viewport') : bad('WhatsApp button off-screen');

  console.log('\n== booking prefill ==');
  await p.goto(`${BASE}/index.html?service=Gel%20Nails&look=Burgundy%20Shimmer%20Almond#booking`, { waitUntil: 'networkidle' });
  const sel = await p.$eval('#service-select', e => e.value);
  sel === 'Gel Nails' ? ok('service preselected') : bad(`service is "${sel}"`);
  const lookShown = await p.$eval('#selected-look', e => !e.hidden);
  lookShown ? ok('chosen look shown') : bad('chosen look hidden');

  console.log('\n== mobile drawer ==');
  await p.setViewportSize({ width: 390, height: 844 });
  await p.goto(`${BASE}/index.html`, { waitUntil: 'networkidle' });
  await p.click('.nav-toggle');
  await p.waitForTimeout(350);
  const navOpen = await p.evaluate(() => document.body.classList.contains('nav-open'));
  navOpen ? ok('drawer opens') : bad('drawer did not open');
  await p.keyboard.press('Escape');
  await p.waitForTimeout(350);
  const navClosed = await p.evaluate(() => !document.body.classList.contains('nav-open'));
  navClosed ? ok('escape closes drawer') : bad('drawer stayed open');

  console.log('\n== headings & landmarks ==');
  for (const page of PAGES) {
    await p.goto(`${BASE}/${page}.html`, { waitUntil: 'domcontentloaded' });
    const h1 = await p.$$eval('h1', e => e.length);
    h1 === 1 ? ok(`${page}: exactly one h1`) : bad(`${page}: ${h1} h1 elements`);
  }

  await b.close();
  console.log(`\n================  ${pass} passed, ${fail} failed  ================`);
  process.exit(fail ? 1 : 0);
})().catch(e => { console.error('HARNESS ERROR', e); process.exit(2); });
