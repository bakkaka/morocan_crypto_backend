# Base PHP + Apache
FROM php:8.3-apache

# Activer Apache mod_rewrite
RUN a2enmod rewrite

# Installer dépendances pour Symfony + PostgreSQL + extensions utiles
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pgsql \
        zip \
        intl \
        opcache \
        bcmath \
        xml \
        soap \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copier fichiers composer
COPY composer.json composer.lock symfony.lock ./

# Installer dépendances sans scripts (évite symfony-cmd)
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copier tout le projet
COPY . .

# Clear et warmup cache Symfony
RUN php bin/console cache:clear --env=prod --no-debug \
    && php bin/console cache:warmup --env=prod

# Configurer Apache pour le répertoire public/
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Port exposé
EXPOSE 8080

# Commande pour lancer Apache en premier plan
CMD ["apache2-foreground"]
