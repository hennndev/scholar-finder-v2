#!/usr/bin/env bash
# Deploy Scholar Finder ke shared hosting (rsync).
# Pakai: ./deploy.sh staging|production
set -euo pipefail

# ================= KONFIGURASI =================
# Format: user@host:/path/absolut/ke/scholar-finder-app
REMOTE="${DEPLOY_REMOTE:-}"      # contoh: user@host:/home/user/scholar-finder-app
DOCROOT="${DEPLOY_DOCROOT:-}"    # contoh: user@host:/home/user/public_html
# ================================================

ENV_NAME="${1:-staging}"

if [[ -z "$REMOTE" || -z "$DOCROOT" ]]; then
    echo "❌ Atur DEPLOY_REMOTE dan DEPLOY_DOCROOT (atau edit variabel REMOTE/DOCROOT di file ini)." >&2
    exit 1
fi

echo "==> [$ENV_NAME] Build frontend"
npm run build

echo "==> [$ENV_NAME] Upload project (tanpa .env, vendor, node_modules, scratch, sampah)"
rsync -avz --delete \
    --exclude '.env' \
    --exclude '.env.*' \
    --exclude 'vendor/' \
    --exclude 'node_modules/' \
    --exclude 'scratch/' \
    --exclude 'scratch_*' \
    --exclude 'test_*.php' \
    --exclude 'chat_interaktif.php' \
    --exclude '*.xlsx' \
    --exclude '*.csv' \
    --exclude '*.txt' \
    --exclude 'embedding_cache.json' \
    --exclude 'public/storage/' \
    --exclude '.git/' \
    ./ "$REMOTE/"

echo "==> [$ENV_NAME] Upload public/ ke docroot"
rsync -avz --delete \
    --exclude 'storage/' \
    ./public/ "$DOCROOT/"

echo "==> [$ENV_NAME] Patch public/index.php (2 baris require)"
ssh "${REMOTE%%:*}" "python3 - <<'PY'
import re
p = '${DOCROOT#*:}/index.php'
s = open(p).read()
s = s.replace(\"__DIR__.'/../vendor/autoload.php'\", \"__DIR__.'/../scholar-finder-app/vendor/autoload.php'\")
s = s.replace(\"__DIR__.'/../bootstrap/app.php'\", \"__DIR__.'/../scholar-finder-app/bootstrap/app.php'\")
open(p, 'w').write(s)
print('index.php patched' if 'scholar-finder-app' in s else 'WARNING: sudah terpatch? cek manual')
PY"

echo "==> [$ENV_NAME] Cache config/route/view + chmod"
ssh "${REMOTE%%:*}" "cd '${REMOTE#*:}' && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    chmod -R 775 storage bootstrap/cache"

echo "✅ [$ENV_NAME] Deploy selesai. Verifikasi: https://domain/up"
