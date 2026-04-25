#!/bin/bash
set -e

echo "🚀 Starting Gabo System..."

# ============================================================
# 1. بنكتب .env من الـ Environment Variables اللي HF بيحقنها
#    ده أهم خطوة — بيضمن إن Laravel بيقرأ القيم الصح
# ============================================================
cat > /var/www/html/.env << EOF
APP_NAME="Gabo System"
APP_ENV=production
APP_DEBUG=${APP_DEBUG:-false}
APP_KEY=${APP_KEY}
APP_URL=${APP_URL:-http://localhost:7860}

APP_LOCALE=${APP_LOCALE:-en}
APP_FALLBACK_LOCALE=${APP_FALLBACK_LOCALE:-en}
APP_MAINTENANCE_DRIVER=${APP_MAINTENANCE_DRIVER:-file}

LOG_CHANNEL=stderr
LOG_LEVEL=error

DB_CONNECTION=${DB_CONNECTION:-mysql}
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}

BROADCAST_CONNECTION=${BROADCAST_CONNECTION:-log}
FILESYSTEM_DISK=${FILESYSTEM_DISK:-local}
QUEUE_CONNECTION=sync

# مهم جداً: نستخدم file لأي driver يحتاج DB
# عشان نمنع crash وقت الـ startup قبل ما الـ DB يكون ready
CACHE_STORE=file
SESSION_DRIVER=file
SESSION_LIFETIME=${SESSION_LIFETIME:-120}
SESSION_ENCRYPT=${SESSION_ENCRYPT:-false}
SESSION_PATH=${SESSION_PATH:-/}
SESSION_DOMAIN=${SESSION_DOMAIN:-null}

FRONTEND_URL=${FRONTEND_URL:-*}
CRON_KEY=${CRON_KEY}
TELEGRAM_BOT_TOKEN=${TELEGRAM_BOT_TOKEN}
TELEGRAM_CHAT_ID=${TELEGRAM_CHAT_ID}
TELEGRAM_BACKUP_BOT_TOKEN=${TELEGRAM_BACKUP_BOT_TOKEN}
EOF

echo "✅ .env file created from environment variables"

# ============================================================
# 2. نمسح الـ config cache القديم فقط (بدون cache:clear)
#    cache:clear بتحاول تتصل بالـ DB لو CACHE_STORE=database
# ============================================================
cd /var/www/html
php artisan config:clear
echo "✅ Config cache cleared"

# ============================================================
# 3. نجري الـ Migrations تلقائياً
# ============================================================
echo "🔄 Running migrations..."
php artisan migrate --force
echo "✅ Migrations complete"

# ============================================================
# 4. نضمن إن الـ Storage Permissions صح
# ============================================================
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "✅ Permissions set"
echo "🌐 Starting Apache on port 7860..."

# ============================================================
# 5. شغّل Apache
# ============================================================
exec apache2-foreground
