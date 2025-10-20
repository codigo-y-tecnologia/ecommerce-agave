# Etapa 1 — Build de assets con Node y Composer
FROM php:8.3-fpm-alpine AS builder

# Instalar dependencias necesarias
RUN apk add --no-cache git zip unzip curl nodejs-current npm

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias de PHP y Node
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader
RUN npm ci && npm run build

# Cache de Laravel
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache ---

# Etapa 2 — Imagen final con Nginx y PHP-FPM
FROM nginx:stable-alpine

# Instalar PHP-FPM y extensiones necesarias
RUN apk add --no-cache php83 php83-fpm php83-pdo php83-pdo_mysql php83-mbstring php83-xml php83-curl php83-bcmath php83-tokenizer php83-fileinfo php83-ctype php83-openssl php83-session php83-dom

# Configurar PHP-FPM
RUN mkdir -p /run/php && \
    sed -i 's|listen = 127.0.0.1:9000|listen = /run/php/php-fpm.sock|' /etc/php83/php-fpm.d/www.conf && \
    sed -i 's|;listen.owner = nobody|listen.owner = nginx|' /etc/php83/php-fpm.d/www.conf && \
    sed -i 's|;listen.group = nobody|listen.group = nginx|' /etc/php83/php-fpm.d/www.conf

# Copiar archivos desde la etapa anterior
COPY --from=builder /var/www/html /var/www/html

# Copiar configuración de Nginx
COPY ./nginx-site.conf /etc/nginx/conf.d/default.conf

# Copiar script de inicio
COPY ./start.sh /start.sh
RUN chmod +x /start.sh

WORKDIR /var/www/html

EXPOSE 80

CMD ["/start.sh"]
