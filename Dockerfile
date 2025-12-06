FROM php:8.3-apache

# Installer seulement PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Copier l'application
COPY . /var/www/html/

WORKDIR /var/www/html

# Simple serveur PHP
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]