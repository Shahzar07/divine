#!/usr/bin/env python3
"""
Site-wide edits that sit outside the generated treatment markup.

Idempotent — safe to run after every `tools/generate.py`.
"""
import pathlib
import re
import sys

ROOT = pathlib.Path(__file__).resolve().parent.parent
PAGES = ('index.html', 'services.html', 'portfolio.html', '404.html')

# The two generic entries became group anchors, and nail art is no longer offered.
ANCHORS = {
    'services.html#facials':  'services.html#skin',
    'services.html#massage':  'services.html#body',
    'services.html#nail-art': 'services.html#nails',
}

ADDRESS_OLD = 'Manchester, United Kingdom'
ADDRESS_NEW = 'Hazel Grove, Stockport'

FOOTER_TREATMENTS = '''      <h3>Treatments</h3>
      <ul>
        <li><a href="services.html#skin">Facials &amp; skin</a></li>
        <li><a href="services.html#microneedling">Microneedling</a></li>
        <li><a href="services.html#body">Massage &amp; body</a></li>
        <li><a href="services.html#body-contouring">Body contouring</a></li>
        <li><a href="services.html#nails">Nails</a></li>
        <li><a href="services.html#glam">Makeup &amp; glam</a></li>
      </ul>'''

# Home-page copy that has to track the size and shape of the menu.
HOME_COPY = [
    ('<img src="assets/studio-room.jpg" alt="A calm, prepared treatment room with folded towels and a lit candle" width="980" height="1225" loading="lazy">',
     '<img src="assets/studio-dee.jpg" alt="Dee carrying out a facial treatment at the studio" width="980" height="1225" loading="lazy">'),
    ('<div><strong>9</strong><span>Signature treatments</span></div>',
     '<div><strong>17</strong><span>Treatments offered</span></div>'),
    ('<h2>Relax. Refresh.<br><em>Rejuvenate.</em></h2>',
     '<h2>Five ways<br><em>to unwind.</em></h2>'),
    ('A head, neck and shoulder massage is the fastest way back into your own body. Warm oil, steady pressure, and a room quiet enough to actually let go.',
     'Swedish, deep tissue, lymphatic drainage, pregnancy massage and manual body contouring — worked at the pressure that suits you, in a room quiet enough to actually let go.'),
    ('<h2>Calm skin.<br><em>Lit from within.</em></h2>',
     '<h2>Six facials.<br><em>One skin — yours.</em></h2>'),
    ('A full cleanse, exfoliation and facial massage, finished with chilled glass globes worked along the cheeks and jaw to bring down heat and puffiness.',
     'From a straightforward cleanse-and-glow to dermaplaning, microneedling, Korean glass skin and microdermabrasion — chosen around your skin on the day, not a fixed script.'),
]


def main() -> None:
    for name in PAGES:
        p = ROOT / name
        if not p.exists():
            continue
        t = original = p.read_text(encoding='utf-8')

        for old, new in ANCHORS.items():
            t = t.replace(old, new)

        t = t.replace(ADDRESS_OLD, ADDRESS_NEW)

        m = re.search(r'      <h3>Treatments</h3>\n      <ul>.*?</ul>', t, re.S)
        if m:
            t = t[:m.start()] + FOOTER_TREATMENTS + t[m.end():]

        if t != original:
            p.write_text(t, encoding='utf-8')
            print(f'  {name}: anchors, address and footer aligned')

    home = ROOT / 'index.html'
    t = home.read_text(encoding='utf-8')
    for old, new in HOME_COPY:
        if new in t:
            continue                     # already applied
        if t.count(old) != 1:
            sys.exit(f'index.html: expected exactly one {old[:48]!r}, found {t.count(old)}')
        t = t.replace(old, new, 1)
    t = re.sub(r'Nine signature treatments across body, skin,\s*\n?\s*nails and glam\.',
               'Seventeen treatments across skin, body, nails and glam.', t)
    home.write_text(t, encoding='utf-8')
    print('  index.html: copy aligned with the menu')

    # Nothing should point at a treatment anchor that no longer exists.
    svc = (ROOT / 'services.html').read_text(encoding='utf-8')
    broken = set()
    for name in PAGES:
        p = ROOT / name
        if not p.exists():
            continue
        for ref in re.findall(r'services\.html#([a-z0-9-]+)', p.read_text(encoding='utf-8')):
            if f'id="{ref}"' not in svc:
                broken.add(ref)
    if broken:
        sys.exit(f'VERIFY FAILED: dangling anchors {sorted(broken)}')
    print('  verified: every services.html anchor resolves')


if __name__ == '__main__':
    main()
