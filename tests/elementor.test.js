const { chromium } = require('playwright');
const BASE = 'http://127.0.0.1:8080';
let pass = 0, fail = 0;
const ok = m => { pass++; console.log('  PASS ' + m); };
const bad = m => { fail++; console.log('  FAIL ' + m); };

(async () => {
  const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium-1194/chrome-linux/chrome' });
  const ctx = await b.newContext({ viewport: { width: 1600, height: 1000 } });
  const p = await ctx.newPage();

  console.log('\n== admin login ==');
  await p.goto(BASE + '/wp-login.php', { waitUntil: 'domcontentloaded' });
  await p.fill('#user_login', 'dee');
  await p.fill('#user_pass', 'testpass123');
  await Promise.all([p.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 60000 }), p.click('#wp-submit')]);
  (p.url().includes('wp-admin')) ? ok('signed in to WordPress') : bad('login failed: ' + p.url());

  console.log('\n== theme setup screen ==');
  await p.goto(BASE + '/wp-admin/themes.php?page=divine-setup', { waitUntil: 'domcontentloaded' });
  const setupTitle = await p.textContent('h1').catch(() => '');
  /Divine Beauty setup/.test(setupTitle || '') ? ok('setup screen present under Appearance') : bad('setup screen missing');

  console.log('\n== customizer section ==');
  await p.goto(BASE + '/wp-admin/customize.php', { waitUntil: 'domcontentloaded' });
  await p.waitForTimeout(6000);
  const hasStudio = await p.evaluate(() =>
    [...document.querySelectorAll('#customize-theme-controls li, .accordion-section h3, .accordion-section-title')]
      .some(e => /Studio details/i.test(e.textContent || '')));
  hasStudio ? ok('"Studio details" section registered') : bad('Studio details section not found');

  console.log('\n== elementor editor ==');
  const homeId = await p.evaluate(async (base) => {
    const r = await fetch(base + '/wp-json/wp/v2/pages?slug=home', { credentials: 'include' });
    const j = await r.json();
    return j[0] ? j[0].id : 0;
  }, BASE);
  homeId ? ok('found the Home page (#' + homeId + ')') : bad('could not resolve Home page id');

  const editorErrors = [];
  p.on('pageerror', e => editorErrors.push(e.message));

  await p.goto(`${BASE}/wp-admin/post.php?post=${homeId}&action=elementor`, { waitUntil: 'domcontentloaded', timeout: 120000 });

  // The editor boots inside an iframe preview; wait for the panel to settle.
  await p.waitForSelector('#elementor-panel', { timeout: 120000 }).catch(() => {});
  await p.waitForTimeout(15000);

  const panelUp = await p.$('#elementor-panel');
  panelUp ? ok('Elementor editor panel loaded') : bad('editor panel never appeared');

  // Open the widget list and look for the theme's category.
  const widgets = await p.evaluate(() => {
    const titles = [...document.querySelectorAll('#elementor-panel .elementor-element .title, #elementor-panel .elementor-element-wrapper .title')]
      .map(e => e.textContent.trim());
    const cats = [...document.querySelectorAll('#elementor-panel .elementor-panel-category-title, #elementor-panel .elementor-panel-heading-title')]
      .map(e => e.textContent.trim());
    return { titles, cats };
  });
  const found = widgets.titles.filter(t =>
    ['Hero','Page hero','Treatment marquee','Studio story','Treatment carousel','Treatment menu',
     'Treatment band','Promise cards','Portfolio gallery','Film band','Reviews','Social strip',
     'Call to action','Enquiry form'].includes(t));
  found.length >= 10
    ? ok(`widget panel lists ${found.length} Divine Beauty widgets`)
    : bad(`only ${found.length} Divine widgets in the panel (cats: ${widgets.cats.slice(0,6).join(', ')})`);

  // The preview iframe should be rendering the real sections.
  const frame = p.frames().find(f => f.name() === 'elementor-preview-iframe' || /elementor-preview/.test(f.url()));
  if (frame) {
    const sections = await frame.evaluate(() =>
      document.querySelectorAll('[data-widget_type^="divine-"]').length).catch(() => 0);
    sections >= 10 ? ok(`preview renders ${sections} Divine widgets`) : bad(`preview rendered ${sections} widgets`);
  } else {
    bad('no preview iframe found');
  }

  const realErrors = editorErrors.filter(e => !/ResizeObserver|Non-Error promise|cert/i.test(e));
  realErrors.length ? bad('editor JS errors: ' + realErrors.slice(0,2).join(' | ')) : ok('no editor JS errors');

  await p.screenshot({ path: process.env.SHOT + '/elementor-editor.png' });

  await b.close();
  console.log(`\n================  ${pass} passed, ${fail} failed  ================`);
  process.exit(fail ? 1 : 0);
})().catch(e => { console.error('HARNESS ERROR', e.message); process.exit(2); });
