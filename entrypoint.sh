#!/bin/bash
set -e  # Stop script jika ada error
set -x  # Tampilkan semua perintah yang dijalankan (debug log di Railway)

echo "🔄 Menjalankan cron..."
service cron start

echo "🧼 Membersihkan cache Laravel..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

echo "🔗 Menjalankan storage:link..."
php artisan storage:link || true

# Optional: generate key jika belum ada
if [ ! -f .env ] || ! grep -q "APP_KEY=" .env; then
  echo "🔑 APP_KEY belum diset, generate key..."
  php artisan key:generate || true
fi

echo "🚀 Menjalankan Apache server..."
exec apache2-foreground
