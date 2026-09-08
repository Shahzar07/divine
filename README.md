# Divine Beauty & Nails By Dee — Website

A static, responsive editorial website for Divine Beauty & Nails By Dee.
Plain HTML, CSS and vanilla JavaScript — **no build step, no framework, no backend**.
What is in this repository is exactly what gets served.

---

## Repository layout

```
.
├── index.html              # The site (single page, anchor navigation)
├── 404.html                # Styled not-found page (wired up in .htaccess)
├── style.css               # Main stylesheet
├── header.css              # Header / navigation styles
├── app.js                  # Interactions: menu, lookbook, films, enquiry form
├── .htaccess               # Apache / LiteSpeed config for Hostinger
├── package.json            # Local preview + helper scripts (not needed to deploy)
├── robots.txt              # Crawler rules  ← set your real domain
├── sitemap.xml             # Sitemap        ← set your real domain
├── assets/
│   ├── logo.png            # Brand logo (also used as favicon)
│   ├── about.jpg, g1…g6-*.jpg   # Photography
│   ├── beauty-film.mp4, ritual-film.mp4   # Locally hosted films
│   ├── film-poster.jpg, ritual-poster.jpg # Video poster frames
│   └── scrollcraft.css, scrollcraft.js    # Scroll/animation runtime
├── BRIEF.md                # Original design brief
├── DELIVERY.md             # What was built, asset provenance, caveats
└── scrollcraft/FINGERPRINTS.md
```

All asset paths in `index.html` are **relative**, so the site works from a domain
root, a subdomain, or a subfolder without any changes.

---

## Deploying to Hostinger

The site must end up with `index.html` and `.htaccess` **directly inside
`public_html`** (or inside the subfolder assigned to the domain/subdomain).

### Option A — Git deployment (recommended)

1. hPanel → **Websites → Dashboard → Advanced → GIT**.
2. **Repository:** `https://github.com/shahzar07/divine.git`
   **Branch:** the branch holding this code
   **Directory:** leave blank to deploy into `public_html`.
3. Click **Create**, then **Deploy** (or copy the webhook URL into the GitHub
   repo under *Settings → Webhooks* for auto-deploy on every push).

Because the site lives at the repository root, Hostinger's clone lands in the
right place with no build or post-deploy command.

### Option B — File Manager upload

```bash
npm run zip     # produces divine-beauty-website.zip
```

hPanel → **File Manager** → open `public_html` → **Upload** the zip →
right-click → **Extract**. Confirm `index.html` and `.htaccess` sit at the top
level of `public_html`, not inside a nested folder.

> **Hidden files:** File Manager and most FTP clients hide dotfiles by default.
> In File Manager use *Settings → Show hidden files* so `.htaccess` is visible;
> in FileZilla use *Server → Force showing hidden files*. If `.htaccess` is
> missing the site still loads, but compression, caching, the 404 page and the
> security headers will not apply.

### Option C — FTP / SFTP

Upload the full contents of the repository (excluding `.git/`) into
`public_html`, keeping the folder structure intact.

---

## After deploying — a short launch checklist

1. **SSL.** hPanel → *Security → SSL* → install the free certificate, then
   *Force HTTPS*. The HTTPS redirect in `.htaccess` is active by default —
   if the certificate is not issued yet and the site becomes unreachable,
   comment out the three `RewriteCond`/`RewriteRule` lines under
   `--- Force HTTPS ---` until it is.
2. **Canonical host.** In `.htaccess`, uncomment either the *strip www* or the
   *force www* block (not both) so the site answers on one address.
3. **Domain references.** Replace `divinebeautybydee.com` in `robots.txt` and
   `sitemap.xml` with the live domain.
4. **Contact details.** `index.html` and `app.js` use
   `hello@divinebeautybydee.com` and list *Manchester, UK · Tue–Sat 9:00–19:00*.
   Confirm both are correct before launch (see `DELIVERY.md`).
5. **Verify.** Load the site and check: logo and hero images appear, the two
   films play when the play buttons are pressed, the lookbook filters work,
   and the enquiry form opens an email draft.

### How the enquiry form works

The booking form is **client-side only**. It validates the fields, composes a
message and hands it to the visitor's email app via `mailto:` (with a
copy-to-clipboard fallback). Nothing is submitted to a server, so no PHP, no
database and no form-handler configuration is needed on Hostinger — and no
appointment is ever auto-confirmed.

---

## Local preview

```bash
npm run dev     # http://localhost:3000  (python3 http.server)
# or
npm run serve   # same, via `npx serve`
npm run check   # verifies required files exist and app.js parses
```

Opening `index.html` straight from the filesystem mostly works, but serve it
over HTTP for accurate video and font behaviour.

---

## Notes

- `.htaccess` blocks public access to `.md`, `.json`, dotfiles and `.git/`, so
  deploying the whole repository does not expose the project docs.
- CSS and JS are served with `must-revalidate` because their filenames are not
  content-hashed; images and video are cached for a year. A redeploy therefore
  goes live immediately without visitors needing a hard refresh.
- Google Fonts are the only external request the page makes. Everything else —
  images, video, scripts — is hosted locally.
