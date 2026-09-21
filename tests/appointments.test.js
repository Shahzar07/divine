/**
 * The booking flow, end to end: a visitor submits the form and the studio finds
 * the enquiry waiting in Appointments with the right details.
 */
const { chromium } = require('playwright');
const BASE = 'http://127.0.0.1:8080';

let pass = 0, fail = 0;
const ok  = m => { pass++; console.log('  PASS ' + m); };
const bad = m => { fail++; console.log('  FAIL ' + m); };

const CLIENT = {
  name: 'Harriet Vale',
  email: 'harriet.vale@example.test',
  phone: '07700 900123',
  service: 'Microneedling',
  message: 'Some scarring on my cheeks I would like to work on.',
};

(async () => {
  const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium-1194/chrome-linux/chrome' });
  const ctx = await b.newContext({ viewport: { width: 1440, height: 950 } });
  const p = await ctx.newPage();

  console.log('\n== a Book now link carries its treatment ==');
  await p.goto(BASE + '/services/', { waitUntil: 'networkidle' });
  const href = await p.$eval('#microneedling .btn-gold', e => e.getAttribute('href'));
  /service=Microneedling/.test(href) ? ok('Microneedling Book now carries the treatment')
                                     : bad(`href was ${href}`);

  await p.goto(BASE + '/', { waitUntil: 'networkidle' });
  const railHref = await p.$eval('.rail .s-card .btn-gold', e => e.getAttribute('href'));
  /service=/.test(railHref) ? ok('home carousel cards have a Book now') : bad(`rail href ${railHref}`);

  console.log('\n== following it preselects the treatment ==');
  await p.goto(BASE + href.replace(BASE, ''), { waitUntil: 'networkidle' });
  const selected = await p.$eval('#service-select', e => e.value);
  selected === CLIENT.service ? ok(`dropdown preselected "${selected}"`) : bad(`dropdown shows "${selected}"`);

  const scrolled = await p.evaluate(() => {
    const el = document.getElementById('booking');
    return el ? Math.abs(el.getBoundingClientRect().top) < window.innerHeight : false;
  });
  scrolled ? ok('the form is in view on arrival') : bad('the form was not scrolled to');

  console.log('\n== submitting the form ==');
  await p.fill('#enq-name', CLIENT.name);
  await p.fill('#enq-email', CLIENT.email);
  await p.fill('#enq-phone', CLIENT.phone);
  await p.fill('#enq-message', CLIENT.message);
  const future = new Date(Date.now() + 12 * 864e5).toISOString().slice(0, 10);
  await p.fill('#preferred-date', future);
  await p.click('#booking-form button[type=submit]');
  await p.waitForTimeout(3000);

  const res = await p.$eval('#enquiry-result', e => ({ hidden: e.hidden, ok: e.classList.contains('is-ok'), text: e.textContent.trim() }));
  (!res.hidden && res.ok) ? ok(`visitor sees confirmation: "${res.text.slice(0, 54)}…"`) : bad(`result: ${JSON.stringify(res)}`);

  console.log('\n== it appears in the admin ==');
  await p.goto(BASE + '/wp-login.php', { waitUntil: 'domcontentloaded' });
  await p.fill('#user_login', 'dee');
  await p.fill('#user_pass', 'testpass123');
  await Promise.all([p.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 60000 }), p.click('#wp-submit')]);

  await p.goto(BASE + '/wp-admin/edit.php?post_type=divine_appointment', { waitUntil: 'domcontentloaded' });
  const menuLabel = await p.evaluate(() =>
    [...document.querySelectorAll('#adminmenu .wp-menu-name')].map(e => e.textContent.trim()).find(t => /^Appointments/.test(t)));
  menuLabel ? ok(`admin menu shows "${menuLabel.replace(/\s+/g, ' ')}"`) : bad('no Appointments item in the admin menu');

  const row = await p.evaluate(() => {
    const tr = document.querySelector('#the-list tr');
    if (!tr) return null;
    const cell = (c) => (tr.querySelector('.column-' + c)?.textContent || '').trim();
    return {
      client: cell('divine_client'), service: cell('divine_service'),
      when: cell('divine_when'), contact: cell('divine_contact'), status: cell('divine_status'),
    };
  });
  if (!row) { bad('no appointment row in the list'); }
  else {
    row.client.includes(CLIENT.name) ? ok(`client column: ${row.client}`) : bad(`client column: ${row.client}`);
    row.service === CLIENT.service ? ok(`treatment column: ${row.service}`) : bad(`treatment column: ${row.service}`);
    row.contact.includes(CLIENT.email) ? ok('contact column shows the email') : bad(`contact: ${row.contact}`);
    /New/i.test(row.status) ? ok('status starts as New') : bad(`status: ${row.status}`);
    row.when && !/No preference/.test(row.when) ? ok(`preferred date recorded: ${row.when}`) : bad(`date: ${row.when}`);
  }

  console.log('\n== the record holds everything the client sent ==');
  const editHref = await p.$eval('#the-list tr .column-divine_client a', e => e.getAttribute('href')).catch(() => null);
  if (!editHref) { bad('could not open the appointment'); }
  else {
    await p.goto(editHref, { waitUntil: 'domcontentloaded' });
    const detail = await p.evaluate(() => document.querySelector('.divine-detail')?.textContent || '');
    [['name', CLIENT.name], ['email', CLIENT.email], ['phone', CLIENT.phone],
     ['treatment', CLIENT.service], ['message', CLIENT.message]].forEach(([label, value]) => {
      detail.includes(value) ? ok(`${label} stored`) : bad(`${label} missing from the record`);
    });
    const statuses = await p.$$eval('#divine-appointment-status option', els => els.map(e => e.value));
    statuses.length === 5 ? ok(`status selector offers ${statuses.join(', ')}`) : bad(`statuses: ${statuses}`);
  }

  console.log('\n== changing the status sticks ==');
  await p.selectOption('select[name="divine_status"]', 'confirmed');
  await Promise.all([p.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 60000 }), p.click('#publish')]);
  const after = await p.$eval('select[name="divine_status"]', e => e.value);
  after === 'confirmed' ? ok('status saved as Confirmed') : bad(`status is "${after}" after saving`);

  console.log('\n== export ==');
  const exportHref = await p.evaluate(async (base) => {
    const r = await fetch(base + '/wp-admin/edit.php?post_type=divine_appointment', { credentials: 'include' });
    const html = await r.text();
    const m = html.match(/href="([^"]*divine_export_appointments[^"]*)"/);
    if (!m) return null;
    // esc_url() writes ampersands as &#038;, so decode entities properly or the
    // nonce parameter arrives mangled and WordPress rejects the request.
    const d = document.createElement('textarea');
    d.innerHTML = m[1];
    return d.value;
  }, BASE);
  if (!exportHref) { bad('no export link on the list screen'); }
  else {
    const csv = await p.evaluate(async (u) => {
      const r = await fetch(u, { credentials: 'include' });
      return { status: r.status, type: r.headers.get('content-type'), body: (await r.text()).slice(0, 400) };
    }, exportHref);
    csv.status === 200 && /csv/.test(csv.type || '') ? ok('CSV export downloads') : bad(`export ${csv.status} ${csv.type}`);
    csv.body.includes(CLIENT.name) ? ok('CSV contains the appointment') : bad('CSV missing the appointment');
  }

  await b.close();
  console.log(`\n================  ${pass} passed, ${fail} failed  ================`);
  process.exit(fail ? 1 : 0);
})().catch(e => { console.error('HARNESS ERROR', e.message); process.exit(2); });
