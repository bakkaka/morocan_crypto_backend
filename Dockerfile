FROM php:8.3-apache

RUN a2enmod rewrite

# Installer les extensions nécessaires
RUN apt-get update && apt-get install -y \
    libpq-dev libicu-dev libzip-dev zip unzip \
 && docker-php-ext-install pdo pdo_pgsql intl zip

# Copier Composer depuis l'image officielle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le dossier de l'application
WORKDIR /var/www/html

# Copier tout le projet **avant** composer install
COPY . .

# Installer les dépendances
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Donner les droits à Symfony
RUN mkdir -p var/cache var/log && chmod -R 777 var

# Config Apache pour Symfony
RUN echo "<Directory /var/www/html/public/> \
    AllowOverride All \
</Directory>" > /etc/apache2/conf-available/symfony.conf \
 && a2enconf symfony

EXPOSE 80
CMD ["apache2-foreground"]
