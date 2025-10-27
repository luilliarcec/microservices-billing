FROM dunglas/frankenphp:1-php8.4

# Arguments for local user
ARG WWWUSER=1000
ARG WWWGROUP=1000

# Environment variables
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV TZ=UTC

WORKDIR /app

# Install php extensions
RUN install-php-extensions \
    pcntl \
	pdo \
	gd \
	intl \
	zip \
	bcmath \
    mongodb \
    redis

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create user with the same UID/GID as the host
RUN groupadd --force -g $WWWGROUP app \
    && useradd -ms /bin/bash --no-user-group -g $WWWGROUP -u $WWWUSER app

# Give permissions to the directory
RUN chown -R app:app .

# Switch to user app
USER app

EXPOSE 8000
