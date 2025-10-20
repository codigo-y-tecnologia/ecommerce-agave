#!/bin/sh

echo "Starting PHP-FPM..."
php-fpm83 -D

echo "Starting Nginx..."
nginx -g "daemon off;"
