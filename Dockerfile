FROM php:8.3-apache

# Activer mod_rewrite
RUN a2enmod rewrite

# Installer extensions nécessaires
RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev libicu-dev git unzip \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip intl

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copier uniquement les fichiers composer
COPY composer.json composer.lock symfony.lock ./

# INSTALL + PAS DE SCRIPTS + SUPERUSER ACTIVÉ
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install \
    --no-dev --optimize-autoloader --no-interaction

# Copier tout le projet
COPY . .

# Permissions
RUN chown -R www-data:www-data /var/www/html/var

# Port utilisé par Apache
EXPOSE 8080
ENV APACHE_LISTEN_PORT=8080
RUN sed -i 's/80/${APACHE_LISTEN_PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-enabled/000-default.conf

CMD ["apache2-foreground"]
