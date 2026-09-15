#!/usr/bin/env sh
# ---------------------------------------------------------------------------
# Local helper scripts for the Divine Beauty website.
#
# These live in a shell script rather than package.json on purpose: a
# package.json at the repository root makes the deployment pipeline classify
# this static site as a Node.js application and look for an "app.js" entry
# point that does not exist, which fails the build. There is no build step —
# the repository is served exactly as it sits.
#
#   ./dev.sh serve    preview at http://localhost:3000
#   ./dev.sh check    verify required files exist and site.js parses
#   ./dev.sh zip      build divine-beauty-website.zip for a manual upload
# ---------------------------------------------------------------------------
set -eu
cd "$(dirname "$0")"

case "${1:-serve}" in
  serve|dev|start)
    echo "Serving on http://localhost:3000 — Ctrl-C to stop"
    exec python3 -m http.server 3000
    ;;

  check)
    node --check site.js
    missing=0
    for f in index.html services.html portfolio.html 404.html \
             style.css header.css site.js .htaccess assets/logo.png; do
      [ -f "$f" ] || { echo "missing required file: $f" >&2; missing=1; }
    done
    [ "$missing" -eq 0 ] || exit 1
    echo "All required files present; site.js parses."
    ;;

  zip)
    rm -f divine-beauty-website.zip
    zip -r -q divine-beauty-website.zip \
      .htaccess index.html services.html portfolio.html 404.html \
      style.css header.css site.js assets robots.txt sitemap.xml
    echo "divine-beauty-website.zip ready (site files only)"
    ;;

  *)
    echo "usage: ./dev.sh [serve|check|zip]" >&2
    exit 64
    ;;
esac
