FROM dunglas/frankenphp:1-php8.4

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV TZ=UTC
ENV APP_ENV=production

WORKDIR /app

# Install php extensions
RUN install-php-extensions \
    pcntl \
    pdo \
    pdo_mysql \
    gd \
    intl \
    zip \
    bcmath \
    mongodb \
    ffi \
    redis \
    opcache

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy app code
COPY . .

# Copy php settings
COPY ./infrastructure/pro/php.ini /usr/local/etc/php/conf.d/custom.ini

# Install production dependencies (without dev)
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --classmap-authoritative \
    --prefer-dist \
    --no-interaction

# Laravel optimizations & commands
RUN php artisan config:cache \
    && php artisan event:cache \
    && php artisan route:cache

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/public \
    && chmod -R 775 /app/storage /app/bootstrap/cache
