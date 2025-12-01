FROM php:8.2-apache

# Activer Apache mod_rewrite
RUN a2enmod rewrite

# Installer les dépendances pour intl et MySQL
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    intl \
    zip \
    opcache

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier les fichiers de l'application
COPY . /var/www/html/

# Définir le répertoire de travail
WORKDIR /var/www/html

# Installer les dépendances PHP
RUN composer install --optimize-autoloader --no-scripts --no-interaction

# Configurer Apache pour utiliser le dossier public/
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Exposer le port 8080
EXPOSE 8080

# Démarrer Apache
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]