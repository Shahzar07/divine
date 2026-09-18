#!/usr/bin/env bash
# Package the theme for "Appearance → Themes → Add New → Upload Theme".
# The zip is a build artefact and is not tracked in git; run this to recreate it.
set -euo pipefail
cd "$(dirname "$0")"
rm -f divine-beauty.zip
zip -qr divine-beauty.zip divine-beauty \
  -x "*.DS_Store" -x "*/node_modules/*" -x "*/.git/*"
printf 'divine-beauty.zip  %s  (%s files)\n' \
  "$(du -h divine-beauty.zip | cut -f1)" \
  "$(unzip -l divine-beauty.zip | tail -1 | awk '{print $2}')"
