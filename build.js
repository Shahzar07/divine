/* ---------------------------------------------------------------------------
 * Assembles the deployable site into dist/.
 *
 * This site is plain HTML, CSS and JS — there is nothing to compile. The step
 * exists because Hostinger's Git auto-deployment runs a Node build pipeline for
 * this website and publishes whatever ends up in the output directory. Copying
 * the site into dist/ gives that pipeline an unambiguous, source-free folder to
 * publish, instead of serving the whole repository checkout.
 *
 * No dependencies, no transformation: what lands in dist/ is byte-identical to
 * what is in the repository.
 * ------------------------------------------------------------------------- */
'use strict';

const fs = require('node:fs');
const path = require('node:path');

const OUT = 'dist';

// Everything the browser needs, and nothing else. Docs, git metadata and the
// helper scripts are deliberately excluded from the published output.
const ENTRIES = [
  '.htaccess',
  'index.html',
  'services.html',
  'portfolio.html',
  '404.html',
  'style.css',
  'header.css',
  'site.js',
  'robots.txt',
  'sitemap.xml',
  'assets',
];

fs.rmSync(OUT, { recursive: true, force: true });
fs.mkdirSync(OUT, { recursive: true });

let files = 0;
for (const entry of ENTRIES) {
  if (!fs.existsSync(entry)) {
    console.error(`build: missing required entry "${entry}"`);
    process.exit(1);
  }
  fs.cpSync(entry, path.join(OUT, entry), { recursive: true });
  files += fs.statSync(entry).isDirectory()
    ? fs.readdirSync(entry).length
    : 1;
}

console.log(`build: copied ${ENTRIES.length} entries (${files} files) into ${OUT}/`);
