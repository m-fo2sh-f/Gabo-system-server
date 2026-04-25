FROM php:8.3-apache

# ============================================================
# 1. تثبيت الـ System Dependencies
# ============================================================
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    zip \
    unzip \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*

# ============================================================
# 2. تثبيت الـ PHP Extensions
# ============================================================
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# ============================================================
# 3. تفعيل الـ Apache Rewrite (مهم لـ Laravel routing)
#    وتغيير الـ Document Root لـ /public
# ============================================================
RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
        /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' \
        /etc/apache2/apache2.conf \
        /etc/apache2/conf-available/*.conf && \
    sed -i 's/AllowOverride None/AllowOverride All/g' \
        /etc/apache2/apache2.conf

# ============================================================
# 4. نسخ ملفات المشروع
# ============================================================
COPY . /var/www/html

# حذف الـ cached package manifest القديم عشان Laravel يعمل discovery من أول
# بالـ packages الفعلية (بدون dev packages)
RUN rm -f /var/www/html/bootstrap/cache/packages.php \
         /var/www/html/bootstrap/cache/services.php

# ============================================================
# 5. تثبيت Composer والـ Dependencies
#    --no-scripts مهم جداً عشان نمنع Laravel من تشغيل
#    config:cache وقت البيلد (لأن الـ DB credentials مش موجودة)
# ============================================================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN cd /var/www/html && \
    composer install \
        --no-interaction \
        --no-dev \
        --no-scripts \
        --prefer-dist \
        --optimize-autoloader

# ============================================================
# 6. إعداد ملف .env أساسي (هيتبدل بالكامل في وقت التشغيل)
#    بس Laravel محتاجه عشان يبدأ
# ============================================================
RUN cp /var/www/html/.env.example /var/www/html/.env

# ============================================================
# 7. إنشاء المجلدات المطلوبة وضبط الصلاحيات
# ============================================================
RUN mkdir -p \
        /var/www/html/storage/framework/sessions \
        /var/www/html/storage/framework/views \
        /var/www/html/storage/framework/cache \
        /var/www/html/storage/logs \
        /var/www/html/bootstrap/cache && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ============================================================
# 8. تغيير البورت من 80 إلى 7860 (مطلوب لـ Hugging Face)
# ============================================================
EXPOSE 7860
RUN sed -i 's/^Listen 80$/Listen 7860/' /etc/apache2/ports.conf && \
    sed -i 's/<VirtualHost \*:80>/<VirtualHost *:7860>/' \
        /etc/apache2/sites-available/*.conf

# ============================================================
# 9. نسخ وتفعيل الـ Entrypoint Script
#    هو اللي هيشتغل أول ما الـ Container يبدأ
# ============================================================
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

CMD ["/usr/local/bin/docker-entrypoint.sh"]