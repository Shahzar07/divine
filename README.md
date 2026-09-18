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
├── portfolio.html          # Portfolio — the studio's own work only
├── 404.html                # Styled not-found page (wired up in .htaccess)
├── style.css               # Design system + page sections
├── header.css              # Announcement bar, header, navigation, footer
├── site.js                 # Navigation, carousels, films, filters, lightbox, enquiry
├── .htaccess               # Apache / LiteSpeed config for Hostinger
├── build.js                # Copies the site into dist/ for Hostinger's pipeline
├── dev.sh                  # Local preview + helper scripts
├── assets/                 # Photography, films, logo, emblem
├── .data/treatments.py     # The treatment menu — the single source of truth
├── tools/
│   ├── generate.py         # Rebuilds the menu into both builds
│   └── polish.py           # Address, footer links, anchor integrity
├── tests/                  # Playwright suites — see "Testing" below
├── wordpress/
│   ├── divine-beauty/      # The WordPress + Elementor theme
│   └── build-theme.sh      # Packages it as divine-beauty.zip
├── BRIEF.md                # Original design brief
└── DELIVERY.md             # What was built, imagery policy, verification
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

The home and services pages use licensed editorial photography; the portfolio
uses the studio's own photographs only. Keep that split — the portfolio is the
one page a client reads as proof of the work. See `DELIVERY.md`.

---

## Deploying to Hostinger

The site must end up with `index.html` and `.htaccess` **directly inside
`public_html`** (or inside the subfolder assigned to the domain/subdomain).

> ### ⚠️ How this site actually deploys
>
> Hostinger has this website registered as a **Node.js application**, and that
> setting lives on the website, not in this repository. Its build pipeline runs
> on every Git auto-deployment no matter what the code looks like, which caused
> three failures in a row:
>
> | Repo state | Outcome |
> |---|---|
> | `app.js` + `package.json` | built, then **503** — Node executed the browser script (`ReferenceError: matchMedia is not defined`) |
> | `app.js` renamed to `site.js` | **Build failed** — no entry file |
> | `package.json` removed | **Build failed** — `ERROR: package.json file not found` |
>
> The repository was never the switch. The fix is to give that pipeline
> something correct to do:
>
> - `package.json` declares one script, `build`, with **no dependencies**;
> - `build.js` copies the site into `dist/` and changes nothing;
> - Hostinger's build settings are `app_type: vite`, `root_directory: .`,
>   `output_directory: dist`, `build_script: build`, so it publishes `dist/`
>   as static files and never boots an entry file.
>
> **Do not add a dependency or an `app.js`.** There is nothing to compile here;
> `npm run build` is a copy. If you ever move off Hostinger's Git deployment,
> `./dev.sh zip` still produces the same files for a manual upload.

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

## Changing the treatment menu

The menu appears on the services page, in the home page carousel and in the
enquiry dropdown. Rather than edit three places and hope they agree, edit
`.data/treatments.py` and regenerate:

```bash
python3 tools/generate.py   # services page, home carousel, enquiry dropdown,
                            # and the theme's Elementor defaults
python3 tools/polish.py     # address, footer links; fails if an anchor dangles
```

Both scripts verify their own output — a bad splice or a link to a treatment
that no longer exists stops the build rather than shipping.

---

## The WordPress theme

`wordpress/divine-beauty/` is the same site rebuilt as a WordPress theme in
which **every section is an Elementor widget** — no code to edit anything.

```bash
./wordpress/build-theme.sh      # produces wordpress/divine-beauty.zip
```

Then, in WordPress: install and activate **Elementor** first, then
*Appearance → Themes → Add New → Upload Theme →* the zip *→ Activate*.
Activating it creates the Home, Services and Portfolio pages as real Elementor
documents, sets the front page and fills the menus.

Full guide: `wordpress/divine-beauty/README.md`.

---

## Testing

Playwright suites live in `tests/`. They need a browser and a server:

```bash
npm install --no-save playwright          # PLAYWRIGHT_SKIP_BROWSER_DOWNLOAD=1 if Chromium is preinstalled
python3 -m http.server 8000               # serve the static site
node tests/static.test.js                 # 38 checks against http://127.0.0.1:8000
```

`tests/wordpress.test.js` (27 checks) and `tests/elementor.test.js` (8 checks)
run against a WordPress install serving the theme on `http://127.0.0.1:8080`.

Two notes for anyone re-running these:

- `python3 -m http.server` answers a `Range` request with `200` instead of
  `206`, so Chromium aborts video streams. The suites filter that out; real
  hosting returns `206` and the films play.
- Google Fonts is blocked by some sandboxed networks. That is filtered too.

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
