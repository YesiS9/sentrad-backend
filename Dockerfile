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

# Jalankan composer install setelah semua file tersedia
RUN composer install --no-dev --optimize-autoloader

# Copy konfigurasi virtualhost Apache
COPY ./docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# Aktifkan mod_rewrite Laravel
RUN a2enmod rewrite

# Tambahkan cron job untuk schedule Laravel
RUN echo "* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1" > /etc/cron.d/laravel-schedule

# Set permission dan berikan executable permission ke cron job
RUN chmod 0644 /etc/cron.d/laravel-schedule

# Apply cron job
RUN crontab /etc/cron.d/laravel-schedule

# Salin entrypoint.sh dari root project ke container
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

# Berikan hak eksekusi pada entrypoint
RUN chmod +x /usr/local/bin/entrypoint.sh

# Gunakan entrypoint script saat container dijalankan
CMD ["/usr/local/bin/entrypoint.sh"]
