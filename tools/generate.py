#!/usr/bin/env python3
"""
Generate the treatment markup from .data/treatments.py.

The menu appears in three places that must agree: the services page, the home
page carousel and the enquiry dropdown. Writing them by hand is how they drift,
so they are all generated from one list.

    python3 tools/generate.py
"""
import re
import sys
import pathlib

ROOT = pathlib.Path(__file__).resolve().parent.parent
sys.path.insert(0, str(ROOT / '.data'))
import treatments as T  # noqa: E402

E = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;'}


def esc(s: str) -> str:
    return ''.join(E.get(c, c) for c in str(s))


def attr(s: str) -> str:
    return esc(s).replace("'", '&#39;')


def booking(name: str) -> str:
    from urllib.parse import quote
    return f'index.html?service={quote(name)}#booking'


# --------------------------------------------------------------- services --

def services_main() -> str:
    chips = '\n'.join(
        f'        <a class="btn btn-ghost btn-sm" href="#{key}">{esc(label)}</a>'
        for key, label, _h, _s in T.GROUPS
    )

    out = [f'''  <section class="page-hero">
    <div class="wrap">
      <nav class="crumbs" aria-label="Breadcrumb">
        <a href="index.html">Home</a><span aria-hidden="true">&#47;</span><span>Services</span>
      </nav>
      <p class="eyebrow centred">The menu</p>
      <h1>Every treatment,<br><em>in full.</em></h1>
      <p class="lede">
        {len(T.TREATMENTS)} treatments across skin, body, nails and glam — what each one
        involves, and what it is actually for.
      </p>
    </div>
  </section>

  <section class="section section--tight">
    <div class="wrap">
      <nav class="filters" aria-label="Jump to a group">
{chips}
      </nav>
    </div>
  </section>
''']

    for key, label, heading, standfirst in T.GROUPS:
        items = [t for t in T.TREATMENTS if t[2] == key]
        cards = []
        for i, (slug, name, _g, img, alt, desc, benefits, _f) in enumerate(items):
            blist = '\n'.join(f'                <dd>{esc(b)}</dd>' for b in benefits)
            delay = f' style="--delay:{(i % 3) * 70}ms"' if i % 3 else ''
            cards.append(f'''        <article class="s-card" id="{slug}" data-reveal{delay}>
          <div class="s-card-media">
            <img src="assets/{img}" alt="{attr(alt)}" width="800" height="1000" loading="lazy" decoding="async">
          </div>
          <div class="s-card-body">
            <h3>{esc(name)}</h3>
            <p>{esc(desc)}</p>
            <dl class="s-card-benefits">
              <dt>Benefits</dt>
{blist}
            </dl>
            <a class="btn btn-gold btn-sm" href="{booking(name)}">Book now <span aria-hidden="true">&#8599;</span></a>
          </div>
        </article>''')

        out.append(f'''
  <section class="section" id="{key}" aria-labelledby="{key}-title">
    <div class="wrap">
      <div class="section-head" data-reveal>
        <div>
          <p class="eyebrow">{esc(label)}</p>
          <h2 id="{key}-title">{heading}</h2>
        </div>
        <p class="lede">{standfirst}</p>
      </div>

      <div class="card-grid card-grid-3">
{chr(10).join(cards)}
      </div>
    </div>
  </section>
''')

    out.append('''
  <section class="section on-paper" aria-labelledby="how-title">
    <div class="wrap">
      <div class="section-head stacked" data-reveal>
        <div>
          <p class="eyebrow centred">How it works</p>
          <h2 id="how-title">Simple, from<br><em>first message.</em></h2>
        </div>
      </div>
      <div class="card-grid card-grid-4">
        <article class="s-card" data-reveal><div class="s-card-body">
          <span class="s-card-step" aria-hidden="true">01</span>
          <h3>Send an enquiry</h3>
          <p>Tell Dee the treatment you have in mind and roughly when suits you.</p>
        </div></article>
        <article class="s-card" data-reveal style="--delay:70ms"><div class="s-card-body">
          <span class="s-card-step" aria-hidden="true">02</span>
          <h3>Pricing and a slot</h3>
          <p>She comes back with the price, how long to allow and the times she has free.</p>
        </div></article>
        <article class="s-card" data-reveal style="--delay:140ms"><div class="s-card-body">
          <span class="s-card-step" aria-hidden="true">03</span>
          <h3>Your appointment</h3>
          <p>One client at a time, in a private room, with the full address sent on confirmation.</p>
        </div></article>
        <article class="s-card" data-reveal style="--delay:210ms"><div class="s-card-body">
          <span class="s-card-step" aria-hidden="true">04</span>
          <h3>Aftercare</h3>
          <p>What to do — and what to avoid — for the days after, in writing if you want it.</p>
        </div></article>
      </div>
    </div>
  </section>

  <section class="cta-band section">
    <div class="wrap-narrow">
      <p class="eyebrow centred" data-reveal>Your turn</p>
      <h2 data-reveal>Ready when<br><em>you are.</em></h2>
      <p class="lede" data-reveal>
        Tell Dee what you have in mind and she will come back with pricing and availability.
      </p>
      <div class="cta-actions" data-reveal>
        <a class="btn btn-gold" href="index.html#booking">Book an appointment <span aria-hidden="true">&#8599;</span></a>
        <a class="btn btn-ghost" href="portfolio.html">See the work</a>
      </div>
    </div>
  </section>
''')
    return '<main id="main">\n' + ''.join(out) + '\n</main>'


# ------------------------------------------------------------------- home --

def home_cards() -> str:
    featured = [t for t in T.TREATMENTS if t[7]]
    cards = []
    for i, (slug, name, group, img, alt, desc, benefits, _f) in enumerate(featured):
        tags = ''.join(f'<span class="tag">{esc(b.split("—")[0].strip())}</span>'
                       for b in benefits[:2])
        short = desc.split('. ')[0].rstrip('.') + '.'
        delay = f' style="--delay:{(i % 3) * 70}ms"' if i % 3 else ''
        cards.append(f'''          <article class="s-card" data-reveal{delay}>
            <div class="s-card-media">
              <img src="assets/{img}" alt="{attr(alt)}" width="800" height="1000" loading="lazy" decoding="async">
              <span class="s-card-no">{i + 1:02d}</span>
            </div>
            <div class="s-card-body">
              <h3>{esc(name)}</h3>
              <div class="tag-row">{tags}</div>
              <p>{esc(short)}</p>
              <div class="s-card-actions">
                <a class="btn btn-gold btn-sm" href="{booking(name)}">Book now <span aria-hidden="true">&#8599;</span></a>
                <a class="link-more" href="services.html#{slug}">Learn more <span aria-hidden="true">&#8599;</span></a>
              </div>
            </div>
          </article>''')
    return '\n'.join(cards)


def booking_options() -> str:
    rows = ['                <option value="">Select a treatment</option>']
    for key, label, _h, _s in T.GROUPS:
        rows.append(f'                <optgroup label="{attr(label)}">')
        for slug, name, group, *_ in T.TREATMENTS:
            if group == key:
                rows.append(f'                  <option>{esc(name)}</option>')
        rows.append('                </optgroup>')
    rows.append("                <option>I&#39;d like some guidance</option>")
    return '\n'.join(rows)



# -------------------------------------------------------------- wordpress --

def php_str(s: str) -> str:
    """A single-quoted PHP string."""
    return "'" + str(s).replace('\\', '\\\\').replace("'", "\\'") + "'"


def wordpress_data() -> str:
    """Emit the treatment menu as a PHP file the Elementor widgets read.

    Generated from the same list as the static build, so the theme and the
    static site can never disagree about what the studio offers.
    """
    lines = [
        '<?php',
        '/**',
        ' * The studio\'s treatment menu.',
        ' *',
        ' * GENERATED FILE — do not edit by hand.',
        ' * Source: .data/treatments.py   Regenerate: python3 tools/generate.py',
        ' *',
        ' * @package DivineBeauty',
        ' */',
        '',
        'declare( strict_types = 1 );',
        '',
        "if ( ! defined( 'ABSPATH' ) ) {",
        '\texit;',
        '}',
        '',
        '/**',
        ' * The four groups the menu is organised into.',
        ' *',
        ' * @return array<int,array<string,string>>',
        ' */',
        'function divine_treatment_groups(): array {',
        '\treturn array(',
    ]
    for key, label, heading, standfirst in T.GROUPS:
        lines.append('\t\tarray(')
        lines.append(f'\t\t\t{php_str("key")}        => {php_str(key)},')
        lines.append(f'\t\t\t{php_str("label")}      => {php_str(label)},')
        lines.append(f'\t\t\t{php_str("heading")}    => {php_str(heading)},')
        lines.append(f'\t\t\t{php_str("standfirst")} => {php_str(standfirst)},')
        lines.append('\t\t),')
    lines += ['\t);', '}', '']

    lines += [
        '/**',
        ' * Every treatment, in menu order.',
        ' *',
        ' * @return array<int,array<string,mixed>>',
        ' */',
        'function divine_treatments(): array {',
        '\treturn array(',
    ]
    for slug, name, group, img, alt, desc, benefits, featured in T.TREATMENTS:
        lines.append('\t\tarray(')
        lines.append(f'\t\t\t{php_str("slug")}     => {php_str(slug)},')
        lines.append(f'\t\t\t{php_str("name")}     => {php_str(name)},')
        lines.append(f'\t\t\t{php_str("group")}    => {php_str(group)},')
        lines.append(f'\t\t\t{php_str("image")}    => {php_str(img)},')
        lines.append(f'\t\t\t{php_str("alt")}      => {php_str(alt)},')
        lines.append(f'\t\t\t{php_str("text")}     => {php_str(desc)},')
        lines.append(f'\t\t\t{php_str("benefits")} => {php_str(chr(10).join(benefits))},')
        lines.append(f'\t\t\t{php_str("featured")} => ' + ('true' if featured else 'false') + ',')
        lines.append('\t\t),')
    lines += ['\t);', '}', '']

    return '\n'.join(lines)


def write_wordpress() -> None:
    out = ROOT / 'wordpress/divine-beauty/elementor/data-treatments.php'
    if not out.parent.exists():
        print('  (no theme directory — skipping the WordPress data file)')
        return
    out.write_text(wordpress_data(), encoding='utf-8')
    print(f'  {out.relative_to(ROOT)}: {len(T.TREATMENTS)} treatments written')

# ------------------------------------------------------------------ patch --

def splice(path: str, start: str, end: str, replacement: str, label: str) -> None:
    """Replace from `start` up to and including the first `end` that follows it.

    The end marker is searched for *after* the start marker — looking from the
    top of the file instead will happily match a closing tag that appears
    earlier and splice the document into nonsense.
    """
    p = ROOT / path
    t = p.read_text(encoding='utf-8')

    a = t.find(start)
    if a < 0:
        sys.exit(f'{path}: could not find the start of the {label} block')
    b = t.find(end, a + len(start))
    if b < 0:
        sys.exit(f'{path}: could not find the end of the {label} block')
    if t.count(start) != 1:
        sys.exit(f'{path}: the {label} start marker is not unique')

    p.write_text(t[:a] + replacement + t[b + len(end):], encoding='utf-8')
    print(f'  {path}: {label} rewritten')


def main() -> None:
    print('Generating from .data/treatments.py '
          f'({len(T.TREATMENTS)} treatments in {len(T.GROUPS)} groups)')

    splice('services.html', '<main id="main">', '</main>', services_main(), 'treatment menu')

    splice('index.html',
           '        <div class="rail rail-3" data-rail tabindex="0" role="group"',
           '\n        </div>\n',
           '        <div class="rail rail-3" data-rail tabindex="0" role="group"'
           ' aria-label="Treatment collection, scrollable">\n'
           + home_cards() + '\n        </div>\n',
           'treatment carousel')

    splice('index.html',
           '              <select name="service" id="service-select" required>',
           '\n              </select>',
           '              <select name="service" id="service-select" required>\n'
           + booking_options() + '\n              </select>',
           'enquiry dropdown')

    write_wordpress()


def verify() -> None:
    """Cheap structural checks — a bad splice duplicates or truncates sections."""
    checks = [
        ('index.html', '<main id="main">', 1),
        ('index.html', 'class="rail rail-3"', 1),
        ('index.html', '<select name="service"', 1),
        ('index.html', 'id="welcome"', 1),
        ('services.html', '<main id="main">', 1),
        ('services.html', 'class="site-footer"', 1),
    ]
    for path, needle, want in checks:
        got = (ROOT / path).read_text(encoding='utf-8').count(needle)
        if got != want:
            sys.exit(f'VERIFY FAILED {path}: {needle!r} appears {got}x, expected {want}')

    home = (ROOT / 'index.html').read_text(encoding='utf-8')
    rail = home.split('class="rail rail-3"', 1)[1].split('carousel-nav', 1)[0]
    cards = rail.count('<article class="s-card"')
    want = sum(1 for t in T.TREATMENTS if t[7])
    if cards != want:
        sys.exit(f'VERIFY FAILED: carousel has {cards} cards, expected {want}')

    svc = (ROOT / 'services.html').read_text(encoding='utf-8')
    for slug, *_ in T.TREATMENTS:
        if f'id="{slug}"' not in svc:
            sys.exit(f'VERIFY FAILED: services.html is missing #{slug}')
    print(f'  verified: {cards} carousel cards, {len(T.TREATMENTS)} treatment anchors')


if __name__ == '__main__':
    main()
    verify()
