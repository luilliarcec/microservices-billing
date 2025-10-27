FROM dunglas/frankenphp:1-php8.4

# Instalar dependencias del sistema y extensiones PHP necesarias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip pdo pdo_mysql bcmath \
    && pecl install mongodb redis \
    && docker-php-ext-enable mongodb redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /app

# Copiar archivos de la aplicación
COPY . .

# Copiar Caddyfile personalizado
COPY Caddyfile /etc/caddy/Caddyfile

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Permisos correctos para Laravel
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# Exponer puerto (FrankenPHP usa 8080 por defecto)
EXPOSE 8080

# Comando de inicio
# FrankenPHP sirve automáticamente desde public/
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
