const { chromium } = require('playwright');
const BASE = 'http://127.0.0.1:8080';
const PAGES = { home: '/', services: '/services/', portfolio: '/portfolio/' };
const WIDTHS = [320, 360, 390, 768, 1024, 1440];

let pass = 0, fail = 0;
const ok  = (m) => { pass++; console.log('  PASS ' + m); };
const bad = (m) => { fail++; console.log('  FAIL ' + m); };
const noise = (s) => /fonts\.(googleapis|gstatic)\.com|ERR_CERT_AUTHORITY_INVALID|\.mp4/.test(s);

(async () => {
  const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium-1194/chrome-linux/chrome' });
  const p = await b.newPage({ viewport: { width: 1440, height: 950 } });
  const errors = [], missing = [];
  p.on('console', m => { if (m.type() === 'error' && !noise(m.text())) errors.push(m.text()); });
  p.on('requestfailed', r => { if (!noise(r.url())) missing.push(r.url()); });
  p.on('response', r => { if (r.status() >= 400 && !noise(r.url())) missing.push(`${r.status()} ${r.url()}`); });

  console.log('\n== pages load cleanly ==');
  for (const [name, path] of Object.entries(PAGES)) {
    const res = await p.goto(BASE + path, { waitUntil: 'networkidle' });
    res.status() === 200 ? ok(`${name} → 200`) : bad(`${name} → ${res.status()}`);
    await p.evaluate(() => new Promise(r => { window.scrollTo(0, 99999); setTimeout(r, 900); }));
  }
  errors.length ? bad('console errors: ' + errors.slice(0,3).join(' | ')) : ok('no console errors');
  missing.length ? bad('failed requests: ' + missing.slice(0,3).join(' | ')) : ok('no failed requests');

  console.log('\n== no PHP notices leaked into the page ==');
  for (const [name, path] of Object.entries(PAGES)) {
    await p.goto(BASE + path, { waitUntil: 'domcontentloaded' });
    const body = await p.content();
    /Warning:|Notice:|Fatal error|Deprecated:/.test(body)
      ? bad(`${name}: PHP message in output`) : ok(`${name}: clean output`);
  }

  console.log('\n== no horizontal overflow ==');
  let over = 0;
  for (const [name, path] of Object.entries(PAGES)) {
    for (const w of WIDTHS) {
      await p.setViewportSize({ width: w, height: 900 });
      await p.goto(BASE + path, { waitUntil: 'domcontentloaded' });
      const d = await p.evaluate(() => document.documentElement.scrollWidth - document.documentElement.clientWidth);
      if (d > 1) { bad(`${name} @${w}px overflows ${d}px`); over++; }
    }
  }
  if (!over) ok(`checked ${Object.keys(PAGES).length * WIDTHS.length} page/width combinations`);
  await p.setViewportSize({ width: 1440, height: 950 });

  console.log('\n== fixed-ratio media fills its frame ==');
  await p.goto(BASE + '/', { waitUntil: 'networkidle' });
  const gaps = await p.$$eval('.frame img, .s-card-media img, .piece-media img', els =>
    els.filter(e => {
      const box = e.parentElement.getBoundingClientRect();
      return box.height > 0 && Math.abs(e.getBoundingClientRect().height - box.height) > 2;
    }).length);
  gaps === 0 ? ok('every framed image fills its container') : bad(`${gaps} images leave a gap`);

  console.log('\n== navigation ==');
  const navInfo = await p.evaluate(() => {
    const links = [...document.querySelectorAll('#primary-nav a.nav-link')];
    return { count: links.length, anyLi: links.some(a => a.closest('li')) };
  });
  navInfo.count >= 3 ? ok(`${navInfo.count} menu links render`) : bad(`only ${navInfo.count} menu links`);
  !navInfo.anyLi ? ok('menu emits bare links — no list markers') : bad('menu still wraps links in <li>');

  console.log('\n== portfolio ==');
  await p.goto(BASE + '/portfolio/', { waitUntil: 'networkidle' });
  const total = await p.$$eval('.piece', e => e.length);
  total === 12 ? ok('12 pieces') : bad(`${total} pieces`);
  await p.click('[data-filter="skin"]'); await p.waitForTimeout(250);
  const shown = await p.$$eval('.piece:not([hidden])', e => e.length);
  shown === 7 ? ok('skin filter shows 7') : bad(`skin filter shows ${shown}`);
  await p.click('[data-filter="all"]'); await p.waitForTimeout(250);
  await p.click('.piece:not([hidden]) .piece-open'); await p.waitForTimeout(400);
  (await p.$eval('#look-dialog', e => e.open)) ? ok('lightbox opens') : bad('lightbox did not open');
  const c = await p.$eval('#dialog-counter', e => e.textContent.trim());
  /^\d+ \/ 12$/.test(c) ? ok(`counter "${c}"`) : bad(`counter "${c}"`);
  const href = await p.$eval('#use-look', e => e.getAttribute('href'));
  /\?service=.+&look=.+#booking/.test(href) ? ok('CTA carries service + look') : bad(`CTA href ${href}`);
  await p.keyboard.press('Escape'); await p.waitForTimeout(250);

  console.log('\n== lightbox position and scroll ==');
  await p.addStyleTag({ content: 'html{scroll-behavior:auto !important}' });
  await p.evaluate(() => window.scrollTo(0, 2500));
  await p.waitForTimeout(400);
  const lb0 = await p.evaluate(() => ({
    y: window.scrollY,
    // Measure a section rather than the card itself: the clicked card lifts 4px
    // by design under :focus-visible, which is not the page moving.
    mark: Math.round(document.querySelector('#work').getBoundingClientRect().top),
  }));
  await p.evaluate(() => document.querySelector('.piece .piece-open').click());
  await p.waitForTimeout(400);
  const lb1 = await p.evaluate(() => {
    const r = document.getElementById('look-dialog').getBoundingClientRect();
    return {
      centred: Math.abs(r.left - (innerWidth - r.width) / 2) < 4
            && Math.abs(r.top - (innerHeight - r.height) / 2) < 4,
      mark: Math.round(document.querySelector('#work').getBoundingClientRect().top),
      frozen: document.body.style.top,
    };
  });
  lb1.centred ? ok('modal opens centred in the viewport') : bad('modal is not centred');
  lb1.mark === lb0.mark ? ok('page does not move when the modal opens')
                        : bad(`page shifted ${lb1.mark - lb0.mark}px on open`);
  lb1.frozen === `-${lb0.y}px` ? ok(`body frozen at the reading position (${lb1.frozen})`)
                               : bad(`body frozen at ${lb1.frozen}, expected -${lb0.y}px`);
  await p.keyboard.press('Escape');
  await p.waitForTimeout(400);
  const lb2 = await p.evaluate(() => window.scrollY);
  lb2 === lb0.y ? ok('scroll restored exactly on close') : bad(`scroll ${lb0.y} -> ${lb2}`);

  console.log('\n== whatsapp ==');
  for (const [name, path] of Object.entries(PAGES)) {
    await p.goto(BASE + path, { waitUntil: 'domcontentloaded' });
    const h = await p.$eval('#whatsapp-float', e => e.getAttribute('href')).catch(() => null);
    h && /^https:\/\/wa\.me\/447838063271\?text=/.test(h) ? ok(`${name}: WhatsApp link`) : bad(`${name}: ${h}`);
  }

  console.log('\n== enquiry form posts to WordPress ==');
  await p.goto(BASE + '/#booking', { waitUntil: 'networkidle' });
  await p.fill('#enq-name', 'Test Client');
  await p.fill('#enq-email', 'client@example.test');
  await p.selectOption('#service-select', 'Gel Nails');
  await p.fill('#enq-message', 'Automated end-to-end check.');
  await p.click('#booking-form button[type=submit]');
  await p.waitForTimeout(2500);
  const res = await p.$eval('#enquiry-result', e => ({ hidden: e.hidden, text: e.textContent.trim(), cls: e.className }));
  (!res.hidden && /is-ok/.test(res.cls)) ? ok(`form accepted: "${res.text.slice(0,58)}…"`) : bad(`form said: ${JSON.stringify(res)}`);

  console.log('\n== the treatment menu ==');
  await p.goto(BASE + '/services/', { waitUntil: 'networkidle' });
  const menu = await p.evaluate(() => ({
    groups: document.querySelectorAll('section[id="skin"], section[id="body"], section[id="nails"], section[id="glam"]').length,
    cards: document.querySelectorAll('.card-grid .s-card[id]').length,
    nailArt: /nail art/i.test(document.body.textContent),
  }));
  menu.groups === 4 ? ok('four treatment groups render') : bad(`${menu.groups} groups`);
  menu.cards === 17 ? ok('all 17 treatments render') : bad(`${menu.cards} treatment cards`);
  !menu.nailArt ? ok('nail art is gone') : bad('nail art still listed');

  console.log('\n== footer has no stray widgets ==');
  await p.goto(BASE + '/', { waitUntil: 'domcontentloaded' });
  const stray = await p.evaluate(() =>
    /uncategorized/i.test(document.querySelector('.site-footer')?.textContent || '')
    || !!document.querySelector('.site-footer .widget_categories'));
  !stray ? ok('no Categories / Uncategorized block') : bad('footer still shows a default widget');

  console.log('\n== service prefill from a Book now link ==');
  await p.goto(BASE + '/services/', { waitUntil: 'networkidle' });
  const bookHref = await p.$eval('.card-grid .s-card .btn-gold', e => e.getAttribute('href'));
  /service=/.test(bookHref) ? ok('services Book now carries the treatment') : bad(`href ${bookHref}`);
  await p.goto(BASE + '/?service=Gel%20Nails#booking', { waitUntil: 'networkidle' });
  const sel = await p.$eval('#service-select', e => e.value);
  sel === 'Gel Nails' ? ok('treatment preselected on arrival') : bad(`select = "${sel}"`);

  console.log('\n== studio details come from the Customizer ==');
  await p.goto(BASE + '/', { waitUntil: 'domcontentloaded' });
  const tel = await p.$eval('.topbar-contact', e => e.getAttribute('href'));
  tel === 'tel:+447838063271' ? ok('phone converted to international form') : bad(`tel href ${tel}`);
  const addr = await p.evaluate(() => document.body.textContent.includes('Hazel Grove, Stockport'));
  addr ? ok('address reads Hazel Grove, Stockport') : bad('address not updated');

  console.log('\n== one h1 per page ==');
  for (const [name, path] of Object.entries(PAGES)) {
    await p.goto(BASE + path, { waitUntil: 'domcontentloaded' });
    const n = await p.$$eval('h1', e => e.length);
    n === 1 ? ok(`${name}: one h1`) : bad(`${name}: ${n} h1s`);
  }

  await b.close();
  console.log(`\n================  ${pass} passed, ${fail} failed  ================`);
  process.exit(fail ? 1 : 0);
})().catch(e => { console.error('HARNESS ERROR', e); process.exit(2); });
