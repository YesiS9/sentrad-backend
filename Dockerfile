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

# Salin semua file project Laravel ke dalam container
COPY . .

# Copy konfigurasi virtualhost Apache
COPY ./docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# Aktifkan mod_rewrite Laravel
RUN a2enmod rewrite

# Tambahkan cron job untuk schedule Laravel
# Jalankan schedule setiap jam
RUN echo "0 * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1" > /etc/cron.d/laravel-schedule

# Set permission dan berikan executable permission ke cron job
RUN chmod 0644 /etc/cron.d/laravel-schedule

# Apply cron job
RUN crontab /etc/cron.d/laravel-schedule

# Jalankan Apache dan cron bersamaan
CMD service cron start && apache2-foreground
