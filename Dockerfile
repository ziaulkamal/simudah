FROM php:8.3-fpm

ENV COMPOSER_ALLOW_SUPERUSER=1

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libzip-dev zlib1g-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy project & install dependencies *at build time*
COPY . /var/www
RUN composer install --no-dev --optimize-autoloader

# Set permissions hanya untuk storage
RUN chown -R www-data:www-data /var/www/storage \
    && chmod -R 775 /var/www/storage

EXPOSE 9000
CMD ["php-fpm"]
