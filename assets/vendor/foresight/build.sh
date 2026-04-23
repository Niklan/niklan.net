#!/usr/bin/env bash
# Rebuilds the ForesightJS IIFE bundle from npm.
# Run from repo root: docker compose exec -T php bash /var/www/html/assets/vendor/foresight/build.sh
set -euo pipefail

PACKAGE="js.foresight"
VERSION="3.5.0"
OUT_DIR="$(cd "$(dirname "$0")" && pwd)/dist"
TMP_DIR="$(mktemp -d)"

trap 'rm -rf "$TMP_DIR"' EXIT

echo "Installing $PACKAGE@$VERSION..."
npm install --prefix "$TMP_DIR" "$PACKAGE@$VERSION" --no-save --silent

echo "Bundling with esbuild..."
npx --prefix "$TMP_DIR" esbuild \
  "$TMP_DIR/node_modules/$PACKAGE/dist/index.js" \
  --bundle \
  --format=iife \
  --global-name=ForesightLib \
  --minify \
  --outfile="$OUT_DIR/foresight.umd.js"

echo "Done: $OUT_DIR/foresight.umd.js ($(wc -c < "$OUT_DIR/foresight.umd.js") bytes)"
