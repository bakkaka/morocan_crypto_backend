FROM php:8.3-apache

# Activer Apache mod_rewrite
RUN a2enmod rewrite

# Installer PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    zip \
    intl

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copier composer
COPY composer.json composer.lock symfony.lock ./

# Installer dépendances
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copier tout
COPY . .

# Scripts Symfony
RUN composer run-script post-install-cmd

# Configurer Apache pour public/
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

EXPOSE 8080

# Solution SIMPLE et GARANTIE
CMD php -S 0.0.0.0:8080 -t public