FROM php:7.4-fpm

# Install required PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql

# Set working directory
WORKDIR /var/www/html/v1/notifications

# Copy application code
COPY . /var/www/html/v1/notifications

# Install Composer
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

# Install application dependencies
RUN composer install --no-dev

# Set permissions for storage and bootstrap directories
RUN chmod -R 777 storage bootstrap/cache
