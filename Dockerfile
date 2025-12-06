FROM php:8.3-fpm

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libpq-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libssl-dev \
    postgresql-client \
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

# Vérification extensions
RUN php -r "if (!extension_loaded('pdo_pgsql')) { echo '❌ PDO_PGSQL NOT LOADED'; exit(1); } echo '✅ PDO_PGSQL OK';"

# Installer Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# 1. Copier UNIQUEMENT les fichiers Composer d'abord
COPY composer.json composer.lock symfony.lock ./

# 2. Installer les dépendances AVANT de copier tout
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# 3. Maintenant copier le reste
COPY . .

# 4. Exécuter les scripts Symfony
RUN composer run-script post-install-cmd

# Permissions
RUN mkdir -p var/cache var/log var/sessions \
    && chown -R www-data:www-data var public \
    && chmod -R 775 var

EXPOSE 8080

# Commande simple pour éviter les erreurs
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]