FROM php:8.3-apache

# Modules et extensions PHP
RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev libicu-dev unzip git curl \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip intl opcache bcmath xml soap \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copier composer.json et installer dépendances
COPY composer.json composer.lock symfony.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copier le reste
COPY . .

# Scripts Symfony
RUN composer run-script post-install-cmd

# Configurer Apache pour /public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && a2enmod rewrite

EXPOSE 8080

# Démarrage avec Apache en prod
CMD ["apache2-foreground"]
