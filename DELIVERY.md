# Divine Beauty — delivery notes

Two things ship from this repository:

1. **`/` — the static site** (Home, Services, Portfolio), which is what is
   deployed on Hostinger today.
2. **`/wordpress/divine-beauty` — the WordPress theme**, the same design rebuilt
   so every section is editable in Elementor.

---

## What changed in this round

### The imagery policy — the reason the site read as "AI sloppy"

The layout and typography were never the problem. The problem was that the
studio's own phone photographs were being used as *marketing* imagery: a Canva
poster with a baked-in phone number was the hero image of the massage section,
cupping marks on a bare back sat above the fold, and a facial shot with blue
latex gloves led the skin section. Authentic photographs, but they read as
clinical or amateur in the places a visitor forms their first impression.

The fix is a split that now holds across the site:

| Page | Imagery |
|---|---|
| Home, Services | Licensed editorial photography — polished, on-brand, warm |
| Portfolio | **The studio's own photographs only** |

Nothing on the home page is captioned as client work any more. The old "Recent
work — real clients, real results" rail was removed rather than filled with
stock, and replaced with a typographic invitation to the portfolio. That keeps
the home page polished *and* keeps the claim honest.

### New photography

Nineteen images were sourced, cropped and optimised (1.4 MB in total) under the
[Pexels licence](https://www.pexels.com/license/) — free for commercial use, no
attribution required, modification allowed. They were chosen for the black /
antique-gold / burgundy palette, and deliberately include the Black and South
Asian clients the studio actually serves.

`hero-nails` `hero-glam` `hero-treatment` `studio-room` `studio-detail`
`band-nails` `band-massage` `band-cupping` `band-makeup` `band-facials`
`svc-massage` `svc-cupping` `svc-makeup` `svc-facials` `svc-gel-nails`
`svc-extensions` `svc-nail-art` `svc-mani-pedi` `svc-brows`

Removed as unused: `about.jpg`, `g1`–`g6`, `massage-benefits.jpg` (the Canva
poster), `ritual-film.mp4`.

### Portfolio rebuilt

- Only the studio's own media: **7 pieces and 2 films**.
- Five entries were removed because they were editorial stock, not the studio's
  work: *Onyx & Gold Foil*, *Chrome Extensions*, *The Modern Nude*, *Ivory Line
  Art*, and the *Head Massage Ritual* Canva poster.
- New card design: the whole tile is one button, so it works by keyboard and
  touch, with the treatment tag over the photograph.
- Filters now carry live counts.
- New lightbox: previous/next, a position counter, ←/→ and Escape, and swipe.

### A nails section on the home page

The studio is named for its nails, but nails had no feature band. There is now
one, and it leads the five.

### WhatsApp

A floating WhatsApp button on every page of both builds. It is a plain link, not
a third-party chat widget — nothing is loaded from anyone else, and the
conversation opens in the visitor's own WhatsApp.

---

## The WordPress theme

`wordpress/divine-beauty/` — see `wordpress/divine-beauty/README.md` for the
full guide. In short:

- **14 Elementor widgets** in a "Divine Beauty" category cover every section of
  every page.
- **Activating the theme builds the site**: Home, Services and Portfolio are
  created as real Elementor documents, Home becomes the front page and the menus
  are filled. There is nothing to import separately.
- **Studio details** (phone, email, address, hours, socials, WhatsApp) live in
  *Appearance → Customise* and are used everywhere at once.
- **Enquiries are emailed**, not handed to a `mailto:` link: validated,
  rate-limited, honeypotted, with the visitor's address as Reply-To.

Build the installable zip with `wordpress/build-theme.sh`.

---

## Verification performed

Everything below was run, not assumed.

**Static site — 38 checks, all passing** (`tests/static.test.js`)
No console errors or failed requests; no horizontal overflow across 4 pages ×
6 widths; every image decodes and has alt text; the imagery policy holds on all
three pages; portfolio filters and counts; lightbox open/next/previous/Escape
and the CTA carrying service + look; the WhatsApp link on every page; booking
prefill from `?service=`/`?look=`; mobile drawer open and Escape; one `<h1>` per
page.

**WordPress theme — 27 checks, all passing** (`tests/wordpress.test.js`)
Run against a real WordPress 6.x on PHP 8.4 with Elementor installed. All three
pages return 200 with no PHP notices in the output; no console errors; no
overflow across 3 pages × 6 widths; every framed image fills its frame; the menu
renders without list markers; portfolio filters and lightbox; WhatsApp on every
page; **the enquiry form posts and the email is produced**; `Book now` carries
the treatment through to a preselected dropdown; the phone number is converted
to its international form.

**Elementor editor — 8 checks, all passing** (`tests/elementor.test.js`)
Admin sign-in; the setup screen under Appearance; the "Studio details"
Customiser section; the editor opens the Home page; the widget panel lists the
Divine Beauty widgets; the preview renders all 14; no editor JavaScript errors.

Two bugs were found this way and fixed:

1. Elementor ships `.elementor img { height: auto }` at the same specificity as
   the theme's `.frame img { height: 100% }`, and its stylesheet loads later — so
   every fixed-ratio frame collapsed and left a black gap. The theme now restates
   those rules one element deeper.
2. `wp_nav_menu()` wraps links in `<li>`, which became the flex item and brought
   a list marker with it. A small walker now emits the links on their own, so a
   WordPress menu renders exactly like the design.

---

## Still to confirm before launch

- **Instagram and Facebook URLs.** `divinebeautyandnailsbydee` could not be
  verified — neither handle appears in public search results, and Instagram
  blocks unauthenticated checks. Set the real profile links in *Appearance →
  Customise → Studio details* (or edit the `href` in the static build).
- **Studio address.** Both builds still read "Manchester, United Kingdom".
- **Phone and email.** `07838 063271` and `hello@divinebeautybydee.com` are
  carried over and should be confirmed.
- **The portfolio is thin, and unevenly weighted** — 4 of the 7 pieces are
  facials, and there is a single nails piece for a studio named after its nails.
  This is the honest limit of the photographs supplied. More nail photography is
  the single highest-value thing the studio can send.
- **Reviews.** One attributed quote is published. Add only real ones.
- **Client image permissions.** Several portfolio photographs show identifiable
  clients. Written permission to publish should be on file.
