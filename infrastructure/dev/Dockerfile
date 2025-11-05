FROM dunglas/frankenphp:1-php8.4

# Arguments for local user
ARG WWWUSER=1000
ARG WWWGROUP=1000

# Environment variables
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV TZ=UTC

WORKDIR /app

# Install so dependencies
RUN apt-get install -y gcc  \
    make  \
    autoconf

# Install php extensions
RUN install-php-extensions \
    pcntl \
	pdo \
	gd \
	intl \
	zip \
	bcmath \
    mongodb \
    ffi \
    redis \
    opentelemetry \
    && echo "ffi.enable=true" >> /usr/local/etc/php/conf.d/99-custom.ini \
    && echo "post_max_size=50M" >> /usr/local/etc/php/conf.d/99-custom.ini \
    && echo "upload_max_filesize=50M" >> /usr/local/etc/php/conf.d/99-custom.ini

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest

# Create user with the same UID/GID as the host
RUN groupadd --force -g $WWWGROUP app \
    && useradd -ms /bin/bash --no-user-group -g $WWWGROUP -u $WWWUSER app

# Give permissions to the directory
RUN chown -R app:app .

# Switch to user app
USER app

EXPOSE 8000
