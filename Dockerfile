FROM php:8.3-apache

# تثبيت الإضافات
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

COPY . /var/www/html
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# بناء الفولدرات
RUN mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/bootstrap/cache

# تسطيب الكومبوزر (الأول عشان ميبوظش الصلاحيات)
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts

# 🚨 الخازوق بيتحل هنا: نسخ الـ env وحقن المتغيرات جواه غصب عن الأباتشي
RUN cp .env.example .env \
    && echo "\nSESSION_DRIVER=cookie\nCACHE_STORE=file\nQUEUE_CONNECTION=sync" >> .env

# الصلاحيات الشاملة في الآخر
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 777 /var/www/html

EXPOSE 7860
RUN sed -i 's/80/7860/g' /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf

CMD ["apache2-foreground"]