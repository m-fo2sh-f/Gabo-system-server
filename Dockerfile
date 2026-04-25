FROM php:8.3-apache

# 1. تثبيت الإضافات المطلوبة للارافيل
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip git curl

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
RUN a2enmod rewrite

# 2. تظبيط الأباتشي والـ Document Root
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# 3. 🚨 السطر السحري: إجبار PHP على قراءة متغيرات البيئة (System Environment)
RUN echo 'variables_order = "EGPCS"' >> /usr/local/etc/php/conf.d/docker-php-vars.ini

# 4. نسخ ملفات المشروع
COPY . /var/www/html

# 5. تسطيب مكتبات الكومبوزر
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts

# 6. إنشاء الفولدرات وصلاحيات الأباتشي
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 7. 🚨 منع لارافيل من البحث عن ملف .env (هنعتمد على السيكريتس مباشرة)
# لو عندك ملف .env.example هنمسحه أو نفضيه عشان ميعملش Conflict مع سيكريتس السيرفر
RUN rm -f .env

EXPOSE 7860
RUN sed -i 's/80/7860/g' /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf

CMD ["apache2-foreground"]