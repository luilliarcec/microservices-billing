FROM dunglas/frankenphp:1-php8.4

ENV APP_ENV=production
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /app

# Install system dependencies required to build
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libxml2-dev \
    libicu-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        zip \
        pdo \
        pdo_mysql \
        bcmath \
        intl \
        pcntl \
        soap \
        exif \
        opcache \
    && pecl install mongodb redis \
    && docker-php-ext-enable mongodb redis opcache

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy dependency files (best cache)
COPY composer.json composer.lock ./

# Install production dependencies
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist

# Copy source code
COPY . .

# Generate optimized autoloader
RUN composer dump-autoload --optimize --classmap-authoritative

# Copy production settings
COPY ./infrastructure/pro/Caddyfile /etc/caddy/Caddyfile

# Laravel Optimizations
RUN php artisan otimize || true

# Final Permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

USER www-data

EXPOSE 8080

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
