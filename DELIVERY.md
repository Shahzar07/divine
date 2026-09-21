# Divine Beauty — delivery notes

Two things ship from this repository:

1. **`/` — the static site** (Home, Services, Portfolio), which is what is
   deployed on Hostinger today.
2. **`/wordpress/divine-beauty` — the WordPress theme**, the same design rebuilt
   so every section is editable in Elementor.

---

## What changed in this round (bookings)

### Appointments — enquiries now live in WordPress

Every enquiry the website receives is stored as a record and appears under
**Appointments** in the admin menu, with a count of the new ones beside it.

- List: client, treatment, preferred date, contact, status, received. Filter by
  status, sort by treatment or date, search, and **Export to CSV**.
- Detail: everything the client sent, a **Reply to this client** button, and a
  status — New → Contacted → Confirmed → Completed, or Cancelled.

The enquiry is **stored first and emailed second**. Mail fails for reasons that
have nothing to do with the visitor — a host with no mailer, a provider
throttling — and an enquiry that only ever existed as an email is an enquiry the
studio loses. The record is the source of truth; the email is a notification. If
the email fails the appointment is still saved, and the visitor is still told it
went through, because it did.

> Note: the **static build has no database**, so its form still hands the visitor
> an email draft. The Appointments dashboard exists only in the WordPress theme.

### Book now on every treatment

All seventeen treatments carry a Book now — on the services page and now on the
home page carousel too. It lands on the enquiry form with that treatment already
selected.

### The portfolio popup

Two separate faults, both fixed:

1. **It opened at the top-left of the screen.** The stylesheet's
   `* { margin: 0 }` reset beats the browser's own `dialog:modal { margin: auto }`,
   so the modal lost its centring. Restored explicitly.
2. **It threw the reader back to the top of the page.** `body` is a scroll
   container here (it carries `overflow-x: hidden`), so switching its `overflow`
   to hidden resets the scroll position. The page is now frozen in place at a
   negative offset taken from the current scroll position and restored exactly on
   close — and the lock is taken *before* `showModal()`, because `showModal()`
   itself resets the scroll, so capturing afterwards recorded zero.

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

**Static site — 40 checks, all passing** (`tests/static.test.js`)
No console errors or failed requests; no horizontal overflow across 4 pages ×
6 widths; every image decodes and has alt text; the imagery policy holds on all
three pages; portfolio filters and counts; lightbox open/next/previous/Escape
and the CTA carrying service + look; the WhatsApp link on every page; booking
prefill from `?service=`/`?look=`; mobile drawer open and Escape; one `<h1>` per
page.

**WordPress theme — 36 checks, all passing** (`tests/wordpress.test.js`)
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

**Bookings — 20 checks, all passing** (`tests/appointments.test.js`)
A Book now link carries its treatment; following it preselects the dropdown and
scrolls to the form; the form submits; the appointment appears in the admin with
the client, treatment, date, contact and a New status; every field is stored;
the status can be changed and sticks; and the CSV export downloads containing it.

`tests/elementor-id-collisions.php` checks that no widget gives a controls
section the same id as a control.

Five bugs were found this way and fixed:

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
4. Book now produced `/#booking?service=…` — the query string landed *inside*
   the fragment, so the form never saw it and nothing preselected. The widgets
   now build the link through one helper that splits the fragment off first.
5. The scroll lock was taken after `showModal()`, which has already reset the
   scroll, so it recorded zero and the reader still lost their place.

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
