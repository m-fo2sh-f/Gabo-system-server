FROM php:8.3-apache

# تثبيت الإضافات الأساسية
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl

# تثبيت إضافات PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# تفعيل Rewrite Module
RUN a2enmod rewrite

# تظبيط مسار لارافيل
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# السر هنا: إجبار الأباتشي إنه يقبل الـ .htaccess بتاع لارافيل
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# نسخ الملفات
COPY . /var/www/html

# تحميل Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# بناء الفولدرات الأساسية يدوياً عشان لارافيل ميكراشش
RUN mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/bootstrap/cache

# تظبيط الصلاحيات 
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

# السر التاني: تشغيل الكومبوزر بدون السكريبتات عشان ميسألش على الداتا بيز وقت البناء
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts

# تظبيط البورت
EXPOSE 7860
RUN sed -i 's/80/7860/g' /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf

CMD ["apache2-foreground"]