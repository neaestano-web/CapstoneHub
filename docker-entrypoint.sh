#!/bin/bash
set -e

# Configure Apache port dynamically if PORT is provided by cloud host (Render uses 10000)
PORT="${PORT:-80}"
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Ensure storage subdirectories exist with proper permissions
mkdir -p /var/www/html/storage/app/private/id-pictures \
         /var/www/html/storage/app/public/id-pictures \
         /var/www/html/storage/app/public/researches \
         /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs || true
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

# Create symbolic link for public storage
php artisan storage:link || true

# Run pending database migrations automatically
php artisan migrate --force || true

# Cache Laravel configuration, routes, and views for high performance
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Re-ensure Apache user www-data owns storage, cache, and logs created by artisan
touch /var/www/html/storage/logs/laravel.log || true
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

# Start Apache in the foreground
exec apache2-foreground
