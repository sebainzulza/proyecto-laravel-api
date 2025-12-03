# Dockerfile para API Laravel - Optimizado para Producción
# PHP 8.2 FPM con extensiones necesarias para Laravel

FROM php:8.2-fpm

# Metadata
LABEL maintainer="Sebastian Inzulza <sebainzulza@example.com>"
LABEL description="API Laravel de Inventario de Servidores - Producción"
LABEL version="1.0"

# Establecer el directorio de trabajo
WORKDIR /var/www

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP requeridas por Laravel
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd

# Copiar Composer desde la imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar archivos de dependencias primero (optimización de caché)
COPY composer.json composer.lock ./

# Instalar dependencias de PHP sin scripts ni autoloader
# Esto aprovecha la caché de Docker si composer.json no cambia
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction \
    --optimize-autoloader

# Copiar el resto de la aplicación
COPY . .

# Generar autoloader optimizado
RUN composer dump-autoload --optimize --classmap-authoritative

# Crear directorios necesarios y establecer permisos
RUN mkdir -p storage/logs \
    && mkdir -p storage/framework/cache \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p bootstrap/cache

# Asignar propiedad al usuario www-data
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# Cambiar al usuario www-data
USER www-data

# Exponer puerto 9000 para PHP-FPM
EXPOSE 9000

# Comando de entrada - PHP-FPM
CMD ["php-fpm"]
