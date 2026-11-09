# Stage 1: Build frontend assets with Node
FROM node:20-alpine AS node_builder
WORKDIR /app
COPY package*.json ./
RUN npm ci || npm install
COPY . .
RUN npm run build

# Stage 2: Production PHP 8.3 with Apache
FROM php:8.3-apache

WORKDIR /var/www/html

# Install required system dependencies and PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Configure PHP upload and execution limits for 512MB RAM environment
RUN echo "upload_max_filesize = 64M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 120" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_input_time = 120" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "opcache.enable = 1" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "opcache.memory_consumption = 32" >> /usr/local/etc/php/conf.d/uploads.ini

# Tune Apache MPM prefork to strictly prevent OOM (Out Of Memory) crashes on Render Free 512MB
RUN { \
        echo '<IfModule mpm_prefork_module>'; \
        echo '    StartServers             2'; \
        echo '    MinSpareServers          1'; \
        echo '    MaxSpareServers          2'; \
        echo '    MaxRequestWorkers        4'; \
        echo '    MaxConnectionsPerChild   100'; \
        echo '</IfModule>'; \
    } > /etc/apache2/mods-available/mpm_prefork.conf

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Copy compiled frontend assets from node_builder
COPY --from=node_builder /app/public/build ./public/build

# Install PHP dependencies without dev packages
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Configure Apache DocumentRoot to point to Laravel's public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set permissions for storage and cache directories
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Startup script to handle dynamic PORT from Render
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80 10000

ENTRYPOINT ["docker-entrypoint.sh"]

