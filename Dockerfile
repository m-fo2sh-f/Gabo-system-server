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

# تثبيت الـ PHP Extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# تفعيل الـ Apache Rewrite
RUN a2enmod rewrite

# تغيير مسار الـ Document Root
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# نسخ الملفات
COPY . /var/www/html

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# إجبار السيرفر على إنشاء فولدرات التخزين الأساسية عشان لارافيل ما يقعش
RUN mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/bootstrap/cache

# إعطاء صلاحيات كاملة 777 للفولدرات دي
RUN chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

# تثبيت مكتبات لارافيل
RUN composer install --no-interaction --optimize-autoloader --no-dev

# فتح بورت 7860
EXPOSE 7860
RUN sed -i 's/80/7860/g' /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf

CMD ["apache2-foreground"]