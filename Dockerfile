# هنبدأ بنسخة PHP فيها Apache جاهز
FROM php:8.2-apache

# تثبيت الإضافات اللي Laravel بيحتاجها
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

# تفعيل الـ Apache Rewrite Module (عشان الـ Routing في لارافيل يشتغل)
RUN a2enmod rewrite

# تغيير الـ Document Root ليكون فولدر الـ public بتاع لارافيل
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# نسخ ملفات المشروع للسيرفر
COPY . /var/www/html

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-interaction --optimize-autoloader --no-dev

# تظبيط الصلاحيات (مهم جداً عشان السيرفر يعرف يكتب في الـ Storage)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# السيرفر هيشتغل على بورت 7860 (ده اللي Hugging Face بيفهمه)
EXPOSE 7860
RUN sed -i 's/80/7860/g' /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf

CMD ["apache2-foreground"]