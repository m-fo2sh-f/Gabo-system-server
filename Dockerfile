FROM php:8.3-apache

# 1. تثبيت الإضافات
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip git curl

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
RUN a2enmod rewrite

# 2. تظبيط الأباتشي
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# 3. نسخ الملفات
COPY . /var/www/html
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. تسطيب الكومبوزر
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts

# 5. 🚨 الضربة القاضية: تصفير الـ .env وحقن القيم الإجبارية
RUN cp .env.example .env && \
    sed -i 's/^APP_KEY=.*/APP_KEY=base64:Gmt8p89+AGHtlHy6xwVuN3wvcSk+XIFxaxQZtAL9hUA=/' .env && \
    sed -i 's/^SESSION_DRIVER=.*/SESSION_DRIVER=cookie/' .env && \
    sed -i 's/^CACHE_STORE=.*/CACHE_STORE=file/' .env && \
    sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env && \
    echo "APP_DEBUG=true" >> .env

# 6. الصلاحيات
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 7860
RUN sed -i 's/80/7860/g' /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf

CMD ["apache2-foreground"]