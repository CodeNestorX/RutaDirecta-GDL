# Dockerfile
# Usamos la imagen oficial de PHP con Apache (servidor web)
FROM php:8.2-fpm

# Instalamos dependencias del sistema necesarias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Limpiamos el caché del instalador para que el contenedor sea ligero
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalamos las extensiones de PHP necesarias para Laravel y MySQL
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Obtenemos la última versión de Composer (el gestor de paquetes de PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definimos el directorio de trabajo dentro del servidor
WORKDIR /var/www

# Exponemos el puerto 9000 para el servicio PHP-FPM
EXPOSE 9000