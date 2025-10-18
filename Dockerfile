# ============================================================
# Etapa de construcción del frontend (Node + Vite)
# ============================================================
FROM node:20-bullseye AS build-frontend

# Establecer directorio de trabajo
WORKDIR /app

# Copiar archivos esenciales del proyecto
COPY package*.json vite.config.* postcss.config.* tailwind.config.* ./

# Instalar dependencias con limpieza de cache
RUN npm ci

# Copiar el resto del código
COPY . .

# Compilar los assets de Vite para producción
RUN npm run build


# ============================================================
# Etapa del backend con PHP (Laravel)
# ============================================================
FROM php:8.3-fpm

# Instalar dependencias del sistema necesarias
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

# Copiar Composer desde imagen oficial
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Crear directorio de trabajo
WORKDIR /var/www/html

# Copiar todo el proyecto
COPY . .

# Copiar los assets compilados desde el build de Node
COPY --from=build-frontend /app/public/build ./public/build

# Instalar dependencias PHP
RUN composer install --optimize-autoloader --no-scripts --no-interaction --no-dev

# Optimizar Laravel
RUN php artisan config:clear || true
RUN php artisan route:clear || true
RUN php artisan view:clear || true
RUN php artisan optimize || true

# Exponer el puerto 8000
EXPOSE 8000

# Comando de inicio
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8000}

