FROM node:20-alpine AS frontend

WORKDIR /app

# Copy node dependency files
COPY package.json package-lock.json* ./

# Install node dependencies
RUN npm ci --no-audit --no-fund

# Copy source code needed to compile
COPY . .

# Compile assets (vite, mix, etc.)
RUN npm run build

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
    redis \
    opcache

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy app code
COPY . .

# Copy compiled assets
COPY --from=frontend /app/public/build ./public/build

# Copy php settings
COPY ./infrastructure/pro/php.ini /usr/local/etc/php/conf.d/custom.ini

# Install production dependencies (without dev)
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --classmap-authoritative \
    --prefer-dist \
    --no-interaction

# Laravel optimizations
RUN php artisan optimize

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/public \
    && chmod -R 775 /app/storage /app/bootstrap/cache

USER www-data

EXPOSE 8000

CMD ["php", "artisan", "octane:frankenphp", "--host=0.0.0.0", "--port=8000", "--workers=4", "--max-requests=1000"]
