# Divine Beauty — delivery notes

Two things ship from this repository:

1. **`/` — the static site** (Home, Services, Portfolio), which is what is
   deployed on Hostinger today.
2. **`/wordpress/divine-beauty` — the WordPress theme**, the same design rebuilt
   so every section is editable in Elementor.

---

## What changed in this round (menu expansion)

### The treatment menu — 17 treatments, grouped

Nail art was removed. Six named facials and five named massages were added, and
the two generic "Massage" and "Facials & Skin" entries were replaced by the
specific treatments that now sit under them:

| Group | Treatments |
|---|---|
| **Facials & skin** | Basic Facial · Luxury Facial · Dermaplaning · Microneedling · Korean Glass Skin · Microdermabrasion Facial |
| **Massage & body** | Swedish · Deep Tissue · Lymphatic Drainage · Pregnancy Massage · Manual Body Contouring · Cupping Therapy |
| **Nails** | Gel Nails · Nail Extensions · Manicure & Pedicure |
| **Makeup & finishing** | Makeup & Glam · Brow & Lash Finish |

Seventeen alternating full-width blocks would have made the services page an
enormous scroll, so the page is now **grouped into four sections with a card
grid**, each card carrying its own description, benefits and *Book now*. A
jump-to row sits above it.

### One source of truth for the menu

The menu appears in three places that must agree — the services page, the home
carousel and the enquiry dropdown — so they are now **generated** from
`.data/treatments.py`:

```bash
python3 tools/generate.py   # rewrites both builds from the menu
python3 tools/polish.py     # address, footer links, anchors; verifies nothing dangles
```

`tools/generate.py` also writes `wordpress/divine-beauty/elementor/data-treatments.php`,
which the Elementor widgets read for their defaults. The static site and the
theme cannot drift apart.

### Your own photography, used where it is the better picture

Six images were supplied and all six are in use:

| Image | Where |
|---|---|
| Dee working in the studio | Home page — the "More than an appointment" section |
| Body contouring, before & after | Portfolio, and the Manual Body Contouring card |
| Body contouring, waist | Portfolio |
| Microneedling in progress | Portfolio, and the Microneedling card |
| Post-treatment glow ×2 | Portfolio |

The earlier rule was "stock on home and services, your photographs only in the
portfolio". That rule existed because the photographs then available read as
amateur — it was really a quality rule wearing a provenance badge. These are
good, specific photographs, so the rule is now what it should always have been:
**use the best image for the job, and keep the portfolio exclusively yours.**

The photograph of you at work is a stronger trust signal than any stock
treatment room, which is why it now leads the home page.

Nine further licensed images were sourced for the treatments with no photograph
yet: dermaplaning, glass skin, microdermabrasion, Swedish, deep tissue,
lymphatic drainage and pregnancy massage. Swap any of them out in Elementor as
you photograph your own.

### Portfolio: 7 → 12 pieces

Five additions, all your own work: two body-contouring results, microneedling in
progress and two post-treatment glow shots. Filter counts update automatically.

### Address

Now **Hazel Grove, Stockport** everywhere — both builds and the Customizer
default.

### The footer "Categories / Uncategorized" block

That was WordPress's own doing: it drops its stock widgets into the first
registered sidebar on a new install, and the theme had registered a footer
widget area. The footer is a designed layout rather than a widget zone, so the
sidebar was **removed entirely** — which fixes it at the source rather than
hiding it.

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

**Static site — 36 checks, all passing** (`tests/static.test.js`)
No console errors or failed requests; no horizontal overflow across 4 pages ×
6 widths; every image decodes and has alt text; the imagery policy holds on all
three pages; portfolio filters and counts; lightbox open/next/previous/Escape
and the CTA carrying service + look; the WhatsApp link on every page; booking
prefill from `?service=`/`?look=`; mobile drawer open and Escape; one `<h1>` per
page.

**WordPress theme — 32 checks, all passing** (`tests/wordpress.test.js`)
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

A third suite, `tests/elementor-id-collisions.php`, checks that no widget
gives a controls section the same id as a control.

Three bugs were found this way and fixed:

1. Elementor ships `.elementor img { height: auto }` at the same specificity as
   the theme's `.frame img { height: 100% }`, and its stylesheet loads later — so
   every fixed-ratio frame collapsed and left a black gap. The theme now restates
   those rules one element deeper.
2. `wp_nav_menu()` wraps links in `<li>`, which became the flex item and brought
   a list marker with it. A small walker now emits the links on their own, so a
   WordPress menu renders exactly like the design.
3. The treatment menu widget rendered nothing at all: Elementor keys controls
   sections and controls in the same namespace, and the widget had a section
   called `groups` *and* a repeater called `groups`. The section silently won
   and the repeater defaulted to null. The section was renamed, and
   `tests/elementor-id-collisions.php` now guards every widget against it.

---

## Still to confirm before launch

- **Instagram and Facebook URLs.** `divinebeautyandnailsbydee` could not be
  verified — neither handle appears in public search results, and Instagram
  blocks unauthenticated checks. Set the real profile links in *Appearance →
  Customise → Studio details* (or edit the `href` in the static build).
- **Studio address.** Both builds still read "Manchester, United Kingdom".
- **Phone and email.** `07838 063271` and `hello@divinebeautybydee.com` are
  carried over and should be confirmed.
- **The portfolio is still light on nails** — 12 pieces now, but only one is
  nails, for a studio named after its nails. Skin and body work are well
  covered. Nail photography remains the single highest-value thing to send.
- **No videos arrived** in the latest upload despite the message mentioning
  reels, so the two existing films are unchanged.
- **Reviews.** One attributed quote is published. Add only real ones.
- **Client image permissions.** Several portfolio photographs show identifiable
  clients. Written permission to publish should be on file.
