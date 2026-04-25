#!/bin/bash
set -e

echo "🚀 Starting Gabo System..."

# ============================================================
# الـ DB credentials الفعلية من Aiven (hardcoded as fallback)
# لأن HF Secrets مش بتتحقن في الـ shell environment
# ============================================================
_DB_HOST="${DB_HOST:-mysql-1ee91562-gabo-system.c.aivencloud.com}"
_DB_PORT="${DB_PORT:-21621}"
_DB_DATABASE="${DB_DATABASE:-defaultdb}"
_DB_USERNAME="${DB_USERNAME:-avnadmin}"
_DB_PASSWORD="${DB_PASSWORD:-AVNS_R4Heo4-j8-zhdBehAx0}"
_APP_KEY="${APP_KEY:-base64:ZQcTP1iJ8J1bbeWmQcymN5x+4aNCyODGtRdELis4vzw=}"
_APP_URL="${APP_URL:-https://m-fo2sh-f-fo2sh-laravel-api.hf.space}"

# ============================================================
# كتابة الـ .env
# ============================================================
cat > /var/www/html/.env << ENVEOF
APP_NAME="Gabo System"
APP_ENV=production
APP_DEBUG=false
APP_KEY=${_APP_KEY}
APP_URL=${_APP_URL}
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_MAINTENANCE_DRIVER=file
LOG_CHANNEL=stderr
LOG_LEVEL=error
DB_CONNECTION=mysql
DB_HOST=${_DB_HOST}
DB_PORT=${_DB_PORT}
DB_DATABASE=${_DB_DATABASE}
DB_USERNAME=${_DB_USERNAME}
DB_PASSWORD=${_DB_PASSWORD}
BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
CACHE_STORE=file
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
MYSQL_ATTR_SSL_CA=/var/www/html/aiven-ca.crt
FRONTEND_URL=${FRONTEND_URL:-*}
CRON_KEY=${CRON_KEY:-}
TELEGRAM_BOT_TOKEN=${TELEGRAM_BOT_TOKEN:-}
TELEGRAM_CHAT_ID=${TELEGRAM_CHAT_ID:-}
TELEGRAM_BACKUP_BOT_TOKEN=${TELEGRAM_BACKUP_BOT_TOKEN:-}
ENVEOF

echo "✅ .env file created"
echo "🔍 DB_HOST=$(grep '^DB_HOST=' /var/www/html/.env | cut -d= -f2)"
echo "🔍 DB_PORT=$(grep '^DB_PORT=' /var/www/html/.env | cut -d= -f2)"

cd /var/www/html

# ============================================================
# Cache config
# ============================================================
php artisan config:cache
echo "✅ Config cached"

# ============================================================
# Migrations
# ============================================================
echo "🔄 Running migrations..."
php artisan migrate --force --database=mysql
echo "✅ Migrations complete"

# ============================================================
# Permissions
# ============================================================
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "✅ Permissions set"
echo "🌐 Starting Apache on port 7860..."

exec apache2-foreground
