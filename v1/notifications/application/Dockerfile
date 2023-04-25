FROM php:7.4-fpm-alpine

# Install required extensions
RUN docker-php-ext-install pdo pdo_pgsql pgsql

# Copy application files to container
COPY . /var/www/html/v1/notifications

# Set working directory
WORKDIR /var/www/html/v1/notifications

# Install composer dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --no-interaction

# Expose port 80
EXPOSE 80

# Start PHP-FPM
CMD ["php-fpm"]
