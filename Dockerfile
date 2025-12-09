# Utilisez php:cli au lieu de php:apache
FROM php:8.3-cli

# Installer PostgreSQL seulement
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    zip

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copier composer
COPY composer.json composer.lock symfony.lock ./

# Installer dépendances
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copier tout
COPY . .

# Clear cache
RUN php bin/console cache:clear --env=prod --no-warmup --no-debug

# Permissions
RUN chmod -R 775 var

EXPOSE 8080

# Utilisez le serveur PHP intégré (plus simple)
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]