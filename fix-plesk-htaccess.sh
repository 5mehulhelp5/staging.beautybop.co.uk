#!/bin/bash

set -e

ROOT="$(cd "$(dirname "$0")" && pwd)"

FILES=(
  "$ROOT/pub/.htaccess"
  "$ROOT/pub/media/.htaccess"
)

for FILE in "${FILES[@]}"; do
  if [ -f "$FILE" ]; then
    sed -i \
      's/Options +FollowSymLinks/Options +SymLinksIfOwnerMatch/' \
      "$FILE"
  fi
done

grep -n "FollowSymLinks\|SymLinksIfOwnerMatch" \
  "$ROOT/pub/.htaccess" \
  "$ROOT/pub/media/.htaccess"
