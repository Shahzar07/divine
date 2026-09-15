# Divine Beauty — delivery notes

A static, responsive, three-page website for Divine Beauty & Nails By Dee:
**Home**, **Services** and **Portfolio**. Black, antique gold and ivory;
Cormorant Garamond and Jost, carried over from the studio's existing identity.

## What was built

### Home (`index.html`)
- Hero with the **Ganesha blessing emblem** in the top-right corner, a three-part
  image collage of the studio's own work, and factual proof points (treatment
  count, one-to-one appointments, opening hours).
- Scrolling treatment marquee.
- **Welcome to Divine** rebuilt as a two-column section: a properly framed 9:16
  film of a client's finished look with a poster frame, a `Play film` /
  `Pause film` control and an overlapping still of real nail work, beside the
  studio story, four promises and a signature.
- **Nine-service carousel** (massage, cupping, makeup, facials, gel nails,
  extensions, nail art, manicure & pedicure, brows & lashes) with arrows and dots.
- **Four signature bands** — massage, cupping, makeup, facials — each with
  photography or film, a benefits list and two calls to action.
- **Recent work carousel**, full-bleed, linking through to the portfolio.
- **Why clients stay** — four numbered promise cards.
- **Testimonials carousel** — six reviews, auto-rotating only while on screen,
  stopping permanently as soon as the reader takes control.
- Social strip, then the enquiry form.

### Services (`services.html`)
Nine services, each with an image or film, name, short description, a
**Benefits** list and a clear **Book now** call to action that preselects that
treatment in the enquiry form. Plus a quick-jump chip row, a four-step
"how an appointment works" explainer and a closing CTA band.

### Portfolio (`portfolio.html`)
The studio's own work only. Two featured film spreads, then a filterable
gallery (All / Nails / Makeup & glam / Facials & skin / Massage & cupping) of
twelve pieces. Each card carries the service, a short description and the
**result**; opening one shows a larger view and a CTA that carries both the
service and the chosen look into the enquiry form.

### Across all pages
- Sticky header with Instagram and Facebook icons, a Book now button and a
  full-height mobile drawer.
- Footer with the studio address block, opening hours, phone, email, social
  icons, treatment and page links, and **Powered by Eagle Studio**.
- Mobile-first layouts verified with zero horizontal overflow at 320, 360, 390,
  768, 820 and 1440 px.

## Asset provenance

| Asset | Source |
|---|---|
| `massage-benefits.jpg`, `cupping-back.jpg`, `facial-*.jpg`, `nails-burgundy.jpg`, `glam-*-film.mp4` | Supplied by the client — work carried out at the studio |
| `ganesha.jpg` | Supplied by the client; cropped to remove the baked-in wordmark |
| `logo.png` | The studio's existing logo |
| `about.jpg`, `g1`, `g2`, `g3`, `g6` | Editorial mood photography carried over from the previous site; used on Home and Services only, never in the portfolio |
| `ritual-film.mp4` | [cottonbro studio on Pexels](https://www.pexels.com/video/close-up-on-manicure-10609138/), under the [Pexels licence](https://www.pexels.com/license/) |

Photographs and films were re-encoded for the web (long edge capped at 1200 px;
video at CRF 30, audio stripped, `faststart`). The assets folder is 4.8 MB in
total. Unused stock from the previous build was removed.

## Verification performed

- Chromium (Playwright) at 320 / 360 / 390 / 768 / 820 / 1440 px across all
  three pages: no console errors, no failed requests, no horizontal overflow.
- 42 interaction checks passing: mobile drawer (open, Escape, scrim, resize),
  enquiry form (validation, message body, `mailto:` link), `?service=` and
  `?look=` prefill, portfolio filters and lightbox, carousel arrows and dots,
  privacy and credits dialogs, social links and footer credit on every page.
- 8 video checks passing: lazy source attachment, muted autoplay on scroll into
  view, pause on scroll away, and the play/pause control and its label.
- Static validation: no duplicate IDs, no missing files, no dangling anchors,
  every image has alt text, one `<h1>` per page, every play control bound to a
  real video, and no unreferenced assets.

Note: the Chromium build available here has no H.264 decoder, so video playback
was verified against a VP9 transcode of the same clip. The shipped MP4s are
H.264/AAC-free baseline video and play in all current browsers. If a browser
cannot decode a film, the poster frame stays and the control reads
"Film unavailable".

## Still to confirm before a public launch

- **Studio address.** The contact block and footer currently read
  "Manchester, United Kingdom" with a note that the full address is shared on
  confirmation. Search for `ADDRESS` in the three HTML files to replace it.
- **Facebook URL.** `https://www.facebook.com/divinebeautyandnailsbydee` is a
  best guess from the Instagram handle and is marked with a `TODO` comment.
- **Email and phone.** `hello@divinebeautybydee.com` is carried from the
  previous site; `07838 063271` comes from the studio's own head-massage artwork.
- **Testimonials.** The six reviews are illustrative of the treatments offered,
  not verbatim client quotes. Replace them with real, attributed reviews.
- **Client image permissions.** Several photographs show identifiable clients
  (faces, a bare back). Written permission to publish should be on file.
- **Unused footage.** One supplied clip shows a scalpel being used for hard-skin
  removal on a foot. It is genuine studio work but reads as clinical rather than
  premium, so it was left off the site. It can be added to the portfolio on request.

The site prepares a client-controlled email draft; it does not reserve slots,
send mail automatically, publish prices or take payments.
