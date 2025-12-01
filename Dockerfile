FROM dunglas/frankenphp:php8.5

# Installer les dépendances pour intl
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libicu-dev \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    intl \
    zip \
    opcache

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier l'application
WORKDIR /app
COPY . .

# Installer les dépendances
RUN composer install --optimize-autoloader --no-scripts --no-interaction

# Port d'écoute
EXPOSE 8000

# Commande de démarrage
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]