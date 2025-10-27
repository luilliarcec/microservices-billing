FROM dunglas/frankenphp:1-php8.4 AS builder

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
    && pecl install mongodb redis \
    && docker-php-ext-enable mongodb redis opcache

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy dependency files first (cache layer)
COPY composer.json composer.lock ./

# Install production dependencies
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --optimize-autoloader

# Copy source code
COPY . .

# Generate optimized autoloader
RUN composer dump-autoload --optimize --classmap-authoritative

# Stage 2: Runtime
FROM dunglas/frankenphp:1-php8.4

ENV APP_ENV=production
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /app

# Instalar solo dependencias runtime (sin dev tools)
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip4 \
    libpng16-16 \
    libjpeg62-turbo \
    libfreetype6 \
    libxml2 \
    libicu72 \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copiar extensiones compiladas del builder
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/

# Copy application from builder
COPY --from=builder --chown=www-data:www-data /app /app

# Copy production settings
COPY --chown=www-data:www-data ./infrastructure/pro/Caddyfile /etc/caddy/Caddyfile

# Laravel Optimizations
RUN php artisan optimize || true

# Final Permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

USER www-data

EXPOSE 8080

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
