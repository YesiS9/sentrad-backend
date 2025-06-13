# Gunakan base image PHP dengan Apache
FROM php:8.2-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    cron \
    zip \
    unzip \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Salin SELURUH file Laravel dulu (supaya artisan tersedia saat composer install)
COPY . .

# Jalankan composer install
RUN composer install --no-dev --optimize-autoloader

# Jalankan perintah Laravel build
RUN php artisan config:clear \
    && php artisan cache:clear \
    && php artisan route:clear \
    && php artisan view:clear \
    && php artisan storage:link

# Copy konfigurasi virtualhost Apache
COPY ./docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# Aktifkan mod_rewrite Laravel
RUN a2enmod rewrite

# Tambahkan cron job untuk schedule Laravel
RUN echo "0 * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1" > /etc/cron.d/laravel-schedule

# Set permission dan berikan executable permission ke cron job
RUN chmod 0644 /etc/cron.d/laravel-schedule

# Apply cron job
RUN crontab /etc/cron.d/laravel-schedule

# Jalankan Apache dan cron bersamaan
CMD service cron start && apache2-foreground
