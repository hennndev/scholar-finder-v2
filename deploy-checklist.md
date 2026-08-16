# Deployment Checklist — Scholar Finder ke Shared Hosting

> DB sudah remote (Supabase PostgreSQL) → hosting hanya butuh PHP.
> Session/cache/queue semua `database` → tanpa worker, tanpa cron, tanpa storage write khusus.

## 0. Cek Kecocokan Hosting (WAJIB sebelum beli/pasang)

| Syarat | Nilai | Catatan |
|---|---|---|
| PHP | **≥ 8.3** | Laravel 13. Cek `php -v` di panel. |
| Ekstensi | `pdo_pgsql`, `pgsql`, `curl`, `openssl`, `mbstring` | **pdo_pgsql jarang aktif** di shared hosting → tanya support dulu. Kalau tidak ada = tidak jalan. |
| SSH / Terminal | opsional tapi sangat disarankan | cPanel biasanya ada "Terminal". |
| max_execution_time | ≥ 180s | Chatbot panggil API 45–120s. |

## 1. Build di Lokal

```bash
composer install --no-dev --optimize-autoloader
npm run build        # frontend → public/build/ (sudah ada; skip kalau tak ubah frontend)
```

## 2. Struktur Folder di Hosting

```
public_html/            ← isi folder public/ lokal (bukan folder public-nya!)
scholar-finder-app/     ← seluruh sisa project
```

## 3. Edit public/index.php (di hosting, 2 baris)

```php
require __DIR__.'/../scholar-finder-app/vendor/autoload.php';
$app = require_once __DIR__.'/../scholar-finder-app/bootstrap/app.php';
```

## 4. Buat .env di scholar-finder-app/ (JANGAN upload .env lokal)

```ini
APP_NAME="Scholar Finder"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.com

DB_CONNECTION=pgsql
DB_HOST=aws-1-ap-northeast-2.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres.<project-ref>
DB_PASSWORD=<password-supabase>
DB_SSLMODE=require

OPENAI_API_KEY=<key-produksi>

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local

MAIL_MAILER=log        # email verifikasi tak terkirim; ganti SMTP kalau perlu
```

## 5. Jalankan Artisan (via SSH/Terminal hosting, di scholar-finder-app/)

```bash
php artisan key:generate
php artisan migrate --force        # hanya jika DB prod belum termigrasi
php artisan config:cache
php artisan route:cache
php artisan view:cache
chmod -R 775 storage bootstrap/cache
```

## 6. Jangan Upload

`.env` · `vendor/` (kecuali hosting tanpa composer) · `node_modules/` · `scratch/` · `test_*.php` · `chat_interaktif.php` · `*.xlsx` · `*.csv` · `*.txt` debug · `embedding_cache.json` (21MB sampah) · `.env.example` boleh.

## 7. Verifikasi

- [ ] `https://domain/up` → OK (Laravel health)
- [ ] `/chatbot` → kirim "beasiswa S2 di Jepang", cek respons
- [ ] Login admin → dashboard → sync embeddings jalan
- [ ] `storage/logs/laravel.log` bersih

## Jebakan

- **Timeout**: kalau chatbot sering 500 → tambah di `public_html/.htaccess`:
  ```apache
  <IfModule mod_php.c>
      php_value max_execution_time 180
  </IfModule>
  ```
- **Supabase**: gunakan pooler (sudah). Kalau Supabase di-pause → semua mati; set no-pause.
- **SSL**: aktifkan, `APP_URL` harus `https://...`.
