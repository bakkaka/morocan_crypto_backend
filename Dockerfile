# Étape 1 : Base PHP + Apache
FROM php:8.3-apache

# Activer mod_rewrite pour Symfony
RUN a2enmod rewrite

# Installer les extensions nécessaires
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
 && docker-php-ext-install pdo pdo_pgsql intl zip

# Copier Composer depuis l’image officielle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le dossier de l’application
WORKDIR /var/www/html

# Copier les fichiers Composer
COPY composer.json composer.lock ./

# Installer les dépendances
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copier tout le projet
COPY . .

# Donner les droits à Symfony pour le cache
RUN mkdir -p var/cache var/log && \
    chmod -R 777 var

# Configuration Apache pour Symfony
RUN echo "<Directory /var/www/html/public/> \
    AllowOverride All \
</Directory>" > /etc/apache2/conf-available/symfony.conf \
 && a2enconf symfony

# Exposer le port Apache
EXPOSE 80

# Lancer Apache
CMD ["apache2-foreground"]
