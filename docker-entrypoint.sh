#!/bin/bash
set -e

echo "🚀 Starting Gabo System..."

# ============================================================
# 1. كتابة الـ .env
# ============================================================
cat > /var/www/html/.env << 'ENVEOF'
APP_NAME="Gabo System"
APP_ENV=production
APP_DEBUG=false
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_MAINTENANCE_DRIVER=file
LOG_CHANNEL=stderr
LOG_LEVEL=error
DB_CONNECTION=mysql
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
ENVEOF

# الآن نلحق القيم الديناميكية (من HF Secrets)
echo "APP_KEY=${APP_KEY}" >> /var/www/html/.env
echo "APP_URL=${APP_URL:-https://m-fo2sh-f-fo2sh-laravel-api.hf.space}" >> /var/www/html/.env
echo "DB_HOST=${DB_HOST}" >> /var/www/html/.env
echo "DB_PORT=${DB_PORT:-21621}" >> /var/www/html/.env
echo "DB_DATABASE=${DB_DATABASE:-defaultdb}" >> /var/www/html/.env
echo "DB_USERNAME=${DB_USERNAME:-avnadmin}" >> /var/www/html/.env
echo "DB_PASSWORD=${DB_PASSWORD}" >> /var/www/html/.env
echo "FRONTEND_URL=${FRONTEND_URL:-*}" >> /var/www/html/.env
echo "CRON_KEY=${CRON_KEY}" >> /var/www/html/.env
echo "TELEGRAM_BOT_TOKEN=${TELEGRAM_BOT_TOKEN}" >> /var/www/html/.env
echo "TELEGRAM_CHAT_ID=${TELEGRAM_CHAT_ID}" >> /var/www/html/.env
echo "TELEGRAM_BACKUP_BOT_TOKEN=${TELEGRAM_BACKUP_BOT_TOKEN}" >> /var/www/html/.env

echo "✅ .env file created"
echo "🔍 DB_HOST=$(grep '^DB_HOST=' /var/www/html/.env | cut -d= -f2)"
echo "🔍 DB_CONNECTION=$(grep '^DB_CONNECTION=' /var/www/html/.env | cut -d= -f2)"

cd /var/www/html

# ============================================================
# 2. Cache config
# ============================================================
php artisan config:cache
echo "✅ Config cached"

# ============================================================
# 3. Migrations — مع explicit --database=mysql
# ============================================================
echo "🔄 Running migrations..."
php artisan migrate --force --database=mysql
echo "✅ Migrations complete"

# ============================================================
# 4. Permissions
# ============================================================
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "✅ Permissions set"
echo "🌐 Starting Apache on port 7860..."

exec apache2-foreground
