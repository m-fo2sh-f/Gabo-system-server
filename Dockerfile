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

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

RUN a2enmod rewrite

# تظبيط مسار لارافيل وإجبار الأباتشي على قراءة الـ .htaccess
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# إظهار الأخطاء
RUN echo "display_errors = On\nerror_reporting = E_ALL" > /usr/local/etc/php/conf.d/errors.ini

# نسخ الملفات وبناء الفولدرات
COPY . /var/www/html
RUN mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/bootstrap/cache

# تحميل وتسطيب الكومبوزر (أولاً)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts

# إرضاء لارافيل
RUN cp .env.example .env

# الصلاحيات الشاملة لكل حاجة في المشروع (ثانياً)
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 7860
RUN sed -i 's/80/7860/g' /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf

CMD ["apache2-foreground"]