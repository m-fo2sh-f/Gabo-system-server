#!/bin/bash
set -e

echo "🚀 Starting Gabo System..."

# ============================================================
# 1. بنكتب .env من الـ Environment Variables اللي HF بيحقنها
# ============================================================
cat > /var/www/html/.env << EOF
APP_NAME="Gabo System"
APP_ENV=production
APP_DEBUG=${APP_DEBUG:-false}
APP_KEY=${APP_KEY}
APP_URL=${APP_URL:-https://m-fo2sh-f-fo2sh-laravel-api.hf.space}

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_MAINTENANCE_DRIVER=file

LOG_CHANNEL=stderr
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT:-21621}
DB_DATABASE=${DB_DATABASE:-defaultdb}
DB_USERNAME=${DB_USERNAME:-avnadmin}
DB_PASSWORD=${DB_PASSWORD}

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
CACHE_STORE=file
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

MYSQL_ATTR_SSL_CA=${MYSQL_ATTR_SSL_CA:-/etc/ssl/certs/ca-certificates.crt}

FRONTEND_URL=${FRONTEND_URL:-*}
CRON_KEY=${CRON_KEY}
TELEGRAM_BOT_TOKEN=${TELEGRAM_BOT_TOKEN}
TELEGRAM_CHAT_ID=${TELEGRAM_CHAT_ID}
TELEGRAM_BACKUP_BOT_TOKEN=${TELEGRAM_BACKUP_BOT_TOKEN}
EOF

echo "✅ .env file created"

# ============================================================
# 2. نطبع DB config للـ debug (بدون الـ password)
# ============================================================
echo "🔍 DB_HOST=$(grep DB_HOST /var/www/html/.env | cut -d= -f2)"
echo "🔍 DB_CONNECTION=$(grep DB_CONNECTION /var/www/html/.env | head -1 | cut -d= -f2)"

cd /var/www/html

# ============================================================
# 3. نعمل config:cache عشان يضمن إن Laravel يقرأ الـ .env صح
# ============================================================
php artisan config:cache
echo "✅ Config cached"

# ============================================================
# 4. نجري الـ Migrations
# ============================================================
echo "🔄 Running migrations..."
php artisan migrate --force
echo "✅ Migrations complete"

# ============================================================
# 5. Storage Permissions
# ============================================================
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "✅ Permissions set"
echo "🌐 Starting Apache on port 7860..."

exec apache2-foreground
