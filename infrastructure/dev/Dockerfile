FROM dunglas/frankenphp:1-php8.4

# Arguments for local user
ARG WWWUSER=1000
ARG WWWGROUP=1000

# Environment variables
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV TZ=UTC
ENV APP_ENV=local

WORKDIR /app

# Install necessary system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip pdo bcmath \
    && pecl install mongodb redis \
    && docker-php-ext-enable mongodb redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create user with the same UID/GID as the host
RUN groupadd --force -g $WWWGROUP app \
    && useradd -ms /bin/bash --no-user-group -g $WWWGROUP -u $WWWUSER app

# Copy configuration files
COPY ./infrastructure/dev/Caddyfile /etc/caddy/Caddyfile

# Give permissions to the directory
RUN chown -R app:app .

# Switch to user app
USER app

EXPOSE 8080

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
