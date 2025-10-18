FROM node:20-alpine AS build-frontend

WORKDIR /app

COPY package*.json vite.config.* postcss.config.* tailwind.config.* ./
RUN npm install

COPY . .

RUN npm run build

FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Copiar Composer desde la imagen oficial
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Crear directorio de trabajo
WORKDIR /var/www/html

# Copiar todo el proyecto Laravel al contenedor
COPY . .

# Copiar los assets compilados desde la etapa de Node (Vite)
COPY --from=build-frontend /app/public/build ./public/build

# Instalar dependencias PHP
RUN composer install --optimize-autoloader --no-scripts --no-interaction --no-dev

# Limpiar cachés y optimizar Laravel
RUN php artisan config:clear || true
RUN php artisan route:clear || true
RUN php artisan view:clear || true
RUN php artisan optimize || true

# Exponer el puerto 8000
EXPOSE 8000

# Comando de inicio
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
