# Divine Beauty & Nails By Dee — Website

A static, responsive website for Divine Beauty & Nails By Dee.
Plain HTML, CSS and vanilla JavaScript — **no build step, no framework, no backend**.
What is in this repository is exactly what gets served.

---

## Repository layout

```
.
├── index.html              # Home
├── services.html           # Services — full treatment menu
├── portfolio.html          # Portfolio — the studio's own work
├── 404.html                # Styled not-found page (wired up in .htaccess)
├── style.css               # Design system + page sections
├── header.css              # Announcement bar, header, navigation, footer
├── site.js                 # Navigation, carousels, films, filters, lightbox, enquiry form
├── .htaccess               # Apache / LiteSpeed config for Hostinger
├── dev.sh                  # Local preview + helper scripts (not needed to deploy)
├── robots.txt              # Crawler rules  ← set your real domain
├── sitemap.xml             # Sitemap        ← set your real domain
├── assets/
│   ├── logo.png                                    # Brand logo (also the favicon)
│   ├── ganesha.jpg                                 # Blessing emblem in the hero
│   ├── massage-benefits.jpg                        # Studio's own head-massage artwork
│   ├── cupping-back.jpg                            # Cupping therapy result
│   ├── facial-globes/-therapy/-detail/-glow.jpg    # Facial treatment + result
│   ├── nails-burgundy.jpg                          # Gel nail work
│   ├── glam-studio-film.mp4 / glam-portrait-film.mp4  + posters   # Client makeup looks
│   ├── ritual-film.mp4 + ritual-poster.jpg         # Nail application film (Pexels)
│   └── about.jpg, g1/g2/g3/g6-*.jpg                # Editorial mood photography
├── BRIEF.md                # Original design brief
└── DELIVERY.md             # What was built, asset provenance, caveats
```

All asset paths are **relative**, so the site works from a domain root, a
subdomain, or a subfolder without any changes.

---

## Before launch — details to confirm

A few values are carried over or best-guess and should be checked by the studio:

| Where | Value | Action |
|---|---|---|
| `index.html`, `services.html`, `portfolio.html` — search for `ADDRESS` | `Manchester, United Kingdom` | Replace with the studio's full street address (two places per page: the contact block and the footer). |
| Every page — search for `facebook.com` | `https://www.facebook.com/divinebeautyandnailsbydee` | Confirm the real Facebook page URL. |
| Every page | `hello@divinebeautybydee.com` | Confirm the studio's real inbox — the enquiry form sends here. |
| Every page | `07838 063271` / `tel:+447838063271` | Carried from the studio's own head-massage artwork; confirm. |
| `robots.txt`, `sitemap.xml`, `<link rel="canonical">` | `divinebeautybydee.com` | Replace with the live domain. |

Testimonials on the home page are illustrative of the treatments offered.
Replace them with real, attributed client reviews before launch.

---

## Deploying to Hostinger

The site must end up with `index.html` and `.htaccess` **directly inside
`public_html`** (or inside the subfolder assigned to the domain/subdomain).

> ### ⚠️ This repository must never look like a Node.js app
>
> Deployment pipelines — Hostinger's Git deploy included — inspect a repository
> for `package.json`. If they find one they classify the site as a **Node.js
> application** and look for an entry file, by default `app.js`. This site has
> no server and no build step, so that classification only ever breaks it:
>
> - with an `app.js` present, the entry script was executed by Node and died on
>   the first browser API (`ReferenceError: matchMedia is not defined`), so
>   nothing listened and every request returned **503**;
> - once `app.js` was renamed to `site.js` to stop that, the pipeline had no
>   entry file to build and the deployment failed outright with **Build failed**.
>
> Both failures had the same root cause, so `package.json` has been removed.
> The local helper scripts now live in `dev.sh`, which no tooling treats as an
> application manifest. The browser script is named `site.js`, never `app.js`.
>
> **Do not reintroduce `package.json` at the repository root.** If you need npm
> for local work, keep it in an untracked file or a subdirectory.

### Option A — Static deploy (recommended)

```bash
./dev.sh zip    # divine-beauty-website.zip — site files only
```

hPanel → **Websites → Dashboard → File Manager** → open `public_html` →
delete anything already there → **Upload** the zip → right-click → **Extract**.
Confirm `index.html` and `.htaccess` sit at the top level of `public_html`,
not inside a nested folder.

### Option B — FTP / SFTP

Upload the contents of the zip (or the repository minus `dev.sh`, `.git/` and
the `*.md` docs) into `public_html`, keeping the folder structure intact.

> **Hidden files:** File Manager and most FTP clients hide dotfiles by default.
> In File Manager use *Settings → Show hidden files* so `.htaccess` is visible;
> in FileZilla use *Server → Force showing hidden files*. If `.htaccess` is
> missing the site still loads, but compression, caching, the 404 page and the
> security headers will not apply.

---

## After deploying — a short launch checklist

1. **SSL.** hPanel → *Security → SSL* → install the free certificate, then
   *Force HTTPS*. The HTTPS redirect in `.htaccess` is active by default —
   if the certificate is not issued yet and the site becomes unreachable,
   comment out the three `RewriteCond`/`RewriteRule` lines under
   `--- Force HTTPS ---` until it is.
2. **Canonical host.** In `.htaccess`, uncomment either the *strip www* or the
   *force www* block (not both) so the site answers on one address.
3. **Domain and contact details.** Work through the table above.
4. **Verify.** Load each page and check: the logo and hero images appear, the
   films play when the play buttons are pressed, the service and work carousels
   scroll, the portfolio filters and lightbox work, and the enquiry form opens
   an email draft.

### How the enquiry form works

The booking form is **client-side only**. It validates the fields, composes a
message and hands it to the visitor's email app via `mailto:` (with a
copy-to-clipboard fallback). Nothing is submitted to a server, so no PHP, no
database and no form-handler configuration is needed on Hostinger — and no
appointment is ever auto-confirmed.

"Book now" buttons on the services and portfolio pages link to
`index.html?service=…#booking`, which preselects that treatment in the form.
The portfolio lightbox additionally passes `&look=…` so the chosen piece of
work is quoted in the enquiry.

---

## Local preview

```bash
./dev.sh serve   # http://localhost:3000  (python3 http.server)
./dev.sh check   # verifies required files exist and site.js parses
./dev.sh zip     # packages the site for a manual upload
```

Opening the HTML straight from the filesystem mostly works, but serve it over
HTTP for accurate video and font behaviour.

---

## Notes

- `.htaccess` blocks public access to `.md`, `.json`, dotfiles and `.git/`, so
  deploying the whole repository does not expose the project docs.
- CSS and JS are served with `must-revalidate` because their filenames are not
  content-hashed; images and video are cached for a year. A redeploy therefore
  goes live immediately without visitors needing a hard refresh.
- Google Fonts is the only external request the pages make. Everything else —
  images, video, scripts — is hosted locally.
- Films are muted, loop, autoplay only when scrolled into view, pause when they
  leave it, and are never downloaded until they are needed. They respect
  `prefers-reduced-motion` and Save-Data, and every one has a manual
  play/pause control and a poster frame.
- The header, footer and dialogs are duplicated verbatim across the three
  pages. When you change one, change all three — there is no template step.
