#!/bin/bash
set -e

# Configure Apache port dynamically if PORT is provided by cloud host (Render uses 10000)
PORT="${PORT:-80}"
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Create symbolic link for public storage
php artisan storage:link || true

# Cache Laravel configuration, routes, and views for high performance
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Start Apache in the foreground
exec apache2-foreground

