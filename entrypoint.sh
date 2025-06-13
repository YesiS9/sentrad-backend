#!/bin/bash

echo

# Start cron
echo
service cron start

# Jalankan perintah Laravel
echo
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan storage:link || true

# Jalankan migration jika perlu (opsional)
# php artisan migrate --force

# Jalankan Apache
echo "🌐 Menjalankan Apache server..."
exec apache2-foreground
