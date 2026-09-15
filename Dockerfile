FROM php:8.2-fpm-alpine

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    curl \
    zip \
    unzip \
    git \
    oniguruma-dev \
    libpng-dev \
    jpeg-dev \
    freetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mysqli gd mbstring

# Set working directory
WORKDIR /var/www/html

# Copy custom PHP configuration
COPY ./docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Copy API files to the container with correct ownership to utilize caching and avoid RUN chown
COPY --chown=www-data:www-data ./api /var/www/html/api

# Switch to non-root user
USER www-data

EXPOSE 9000
CMD ["php-fpm"]
