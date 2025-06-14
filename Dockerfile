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

# Salin seluruh file proyek Laravel
COPY . .

# Install dependensi Laravel (tanpa dev)
RUN composer install --no-dev --optimize-autoloader

# Salin konfigurasi Apache virtual host
COPY ./docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# Aktifkan mod_rewrite
RUN a2enmod rewrite

# Tambahkan cron job Laravel schedule
RUN echo "* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1" > /etc/cron.d/laravel-schedule \
    && chmod 0644 /etc/cron.d/laravel-schedule \
    && crontab /etc/cron.d/laravel-schedule

# Salin entrypoint.sh dan beri izin eksekusi
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Jalankan entrypoint script saat container dijalankan
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
