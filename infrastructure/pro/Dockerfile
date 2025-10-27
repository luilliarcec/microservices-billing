FROM dunglas/frankenphp:1-php8.4

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV TZ=UTC
ENV APP_ENV=production

WORKDIR /app

# Install php extensions + opcache
RUN install-php-extensions \
    pcntl \
    pdo \
    gd \
    intl \
    zip \
    bcmath \
    mongodb \
    redis \
    opcache

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files first (best cache for docker)
COPY composer.json composer.lock ./

# Install production dependencies (without dev)
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction

# Copy application code
COPY . .

# Generate optimized autoloader
RUN composer dump-autoload --optimize --classmap-authoritative

# Laravel optimizations (caches)
RUN php artisan optimize

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

USER www-data

EXPOSE 8000

CMD ["php", "artisan", "octane:frankenphp", "--host=0.0.0.0", "--port=8000", "--workers=4", "--max-requests=1000"]
