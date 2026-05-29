#!/usr/bin/env bash
#
# Release a new version of this package.
#
# Run on the dev server inside the package's git checkout. Builds a
# distribution zip + manifest JSON, drops both into the update endpoint
# directory (dev.estatesite.eu/updates/). Customer WordPress installs
# poll the manifest URL and pick up the new version within ~12 hours.
#
# Usage:
#     ./bin/release.sh <version>
#
# Example:
#     ./bin/release.sh 1.0.0
#
# Prerequisites (handled by the caller, not this script):
#   - The plugin header `Version:` matches <version>.
#   - The matching constant define( '*_VERSION', '<version>' ) matches too.
#   - All changes committed and pushed to GitHub.
#
# What this script does NOT do:
#   - Bump versions or commit. Do that by hand first.
#   - Push to GitHub. CI is for quality gating, not distribution.
#   - Sign the zip / verify checksum. v1 ships without — revisit if needed.

set -euo pipefail

# -----------------------------------------------------------------------------
# Config
# -----------------------------------------------------------------------------
# These three vars are the only per-package thing; everything else generic.
PACKAGE_SLUG="estatesite-classic"
PACKAGE_TYPE="theme"   # plugin | theme
PACKAGE_DISPLAY_NAME="EstateSite Classic"
PACKAGE_DESCRIPTION="EstateSite Classic — WordPress theme (header, footer, archives, property templates)."

# Where customer WP installs fetch from. Both files (zip + json) land here.
UPDATE_ENDPOINT_DIR="/home/estatesite-dev/htdocs/dev.estatesite.eu/updates"
UPDATE_ENDPOINT_URL="https://dev.estatesite.eu/updates"

# -----------------------------------------------------------------------------
# Args
# -----------------------------------------------------------------------------
if [ $# -lt 1 ]; then
  echo "Usage: $0 <version>" >&2
  exit 2
fi
VERSION="$1"

# Basic sanity: looks like semver
if ! [[ "$VERSION" =~ ^[0-9]+\.[0-9]+\.[0-9]+(-[a-zA-Z0-9.]+)?$ ]]; then
  echo "❌ Version '$VERSION' doesn't look like semver (e.g. 1.0.0 or 1.0.0-beta.1)" >&2
  exit 2
fi

# Resolve the repo root (this script lives in bin/)
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
REPO_ROOT="$( cd "$SCRIPT_DIR/.." && pwd )"
cd "$REPO_ROOT"

echo "==> Releasing $PACKAGE_SLUG v$VERSION"
echo "    Repo root: $REPO_ROOT"
echo "    Endpoint:  $UPDATE_ENDPOINT_DIR"
echo ""

# -----------------------------------------------------------------------------
# Verify version matches what's in the plugin/theme header
# -----------------------------------------------------------------------------
if [ "$PACKAGE_TYPE" = "plugin" ]; then
  HEADER_VERSION=$(grep -m1 '^[[:space:]]*\*[[:space:]]*Version:' "$PACKAGE_SLUG.php" | sed -E 's/.*Version:[[:space:]]+//' | xargs || echo "")
  HEADER_REQUIRES=$(grep -m1 '^[[:space:]]*\*[[:space:]]*Requires at least:' "$PACKAGE_SLUG.php" | sed -E 's/.*Requires at least:[[:space:]]+//' | xargs || echo "")
  HEADER_REQUIRES_PHP=$(grep -m1 '^[[:space:]]*\*[[:space:]]*Requires PHP:' "$PACKAGE_SLUG.php" | sed -E 's/.*Requires PHP:[[:space:]]+//' | xargs || echo "")
  HEADER_TESTED=$(grep -m1 '^[[:space:]]*\*[[:space:]]*Tested up to:' "$PACKAGE_SLUG.php" | sed -E 's/.*Tested up to:[[:space:]]+//' | xargs || echo "")
else
  HEADER_VERSION=$(grep -m1 '^[[:space:]]*Version:' style.css | sed -E 's/.*Version:[[:space:]]+//' | xargs || echo "")
  HEADER_REQUIRES=$(grep -m1 '^[[:space:]]*Requires at least:' style.css | sed -E 's/.*Requires at least:[[:space:]]+//' | xargs || echo "")
  HEADER_REQUIRES_PHP=$(grep -m1 '^[[:space:]]*Requires PHP:' style.css | sed -E 's/.*Requires PHP:[[:space:]]+//' | xargs || echo "")
  HEADER_TESTED=""
fi

if [ "$HEADER_VERSION" != "$VERSION" ]; then
  echo "❌ Version mismatch:" >&2
  echo "   Requested:     $VERSION" >&2
  echo "   In file header: $HEADER_VERSION" >&2
  echo "   Bump the header first, commit, then re-run." >&2
  exit 1
fi
echo "✓ Header version matches: $HEADER_VERSION"

# -----------------------------------------------------------------------------
# Build dist zip
# -----------------------------------------------------------------------------
WORK_DIR=$(mktemp -d)
DIST_DIR="$WORK_DIR/$PACKAGE_SLUG"
ZIP_NAME="$PACKAGE_SLUG-$VERSION.zip"

trap 'rm -rf "$WORK_DIR"' EXIT

mkdir -p "$DIST_DIR"

# Copy everything that ships to customers. Excludes dev artifacts so the
# zip mirrors what WordPress puts on the customer server post-install.
rsync -a \
  --exclude='.git' \
  --exclude='.github' \
  --exclude='.gitignore' \
  --exclude='.gitattributes' \
  --exclude='.editorconfig' \
  --exclude='bin' \
  --exclude='node_modules' \
  --exclude='tests' \
  --exclude='phpunit.xml*' \
  --exclude='phpcs.xml*' \
  --exclude='composer.lock' \
  --exclude='*.log' \
  --exclude='.DS_Store' \
  --exclude='Thumbs.db' \
  --exclude='RELEASE_NOTES_v*.md' \
  ./ "$DIST_DIR/"

# Build zip inside the temp dir so the top-level entry is $PACKAGE_SLUG/
( cd "$WORK_DIR" && zip -qr "$ZIP_NAME" "$PACKAGE_SLUG" )

ZIP_SIZE=$(du -h "$WORK_DIR/$ZIP_NAME" | cut -f1)
echo "✓ Built zip: $ZIP_NAME ($ZIP_SIZE)"

# -----------------------------------------------------------------------------
# Build manifest JSON
# -----------------------------------------------------------------------------
# Customers' WP plugin reads this file to decide whether to update.
MANIFEST="$WORK_DIR/$PACKAGE_SLUG.json"
ZIP_URL="$UPDATE_ENDPOINT_URL/$ZIP_NAME"
LAST_UPDATED=$(date -u +%Y-%m-%d)

cat > "$MANIFEST" <<JSON
{
  "name":         "$PACKAGE_DISPLAY_NAME",
  "slug":         "$PACKAGE_SLUG",
  "version":      "$VERSION",
  "download_url": "$ZIP_URL",
  "homepage":     "https://estatesite.eu",
  "author":       "Estate Site",
  "requires":     "$HEADER_REQUIRES",
  "requires_php": "$HEADER_REQUIRES_PHP",
  "tested":       "$HEADER_TESTED",
  "last_updated": "$LAST_UPDATED",
  "sections": {
    "description": "$PACKAGE_DESCRIPTION",
    "changelog":   "See https://github.com/MilenFrom/$PACKAGE_SLUG/releases/tag/v$VERSION"
  }
}
JSON
echo "✓ Built manifest: $PACKAGE_SLUG.json"

# -----------------------------------------------------------------------------
# Deploy: copy both files into the update endpoint dir
# -----------------------------------------------------------------------------
if [ ! -d "$UPDATE_ENDPOINT_DIR" ]; then
  echo "Creating update endpoint dir: $UPDATE_ENDPOINT_DIR"
  mkdir -p "$UPDATE_ENDPOINT_DIR"
fi

cp "$WORK_DIR/$ZIP_NAME" "$UPDATE_ENDPOINT_DIR/$ZIP_NAME"
cp "$MANIFEST"          "$UPDATE_ENDPOINT_DIR/$PACKAGE_SLUG.json"

echo ""
echo "✓ Released $PACKAGE_SLUG v$VERSION"
echo ""
echo "Customer-visible URLs:"
echo "    Manifest: $UPDATE_ENDPOINT_URL/$PACKAGE_SLUG.json"
echo "    Zip:      $ZIP_URL"
echo ""
echo "Customers will see the update in WordPress Dashboard → Updates"
echo "within ~12 hours (the manifest fetch cache TTL)."
