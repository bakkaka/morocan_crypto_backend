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

# Vérification extension PDO_PGSQL
RUN php -r "if (!extension_loaded('pdo_pgsql')) { echo '❌ PDO_PGSQL NOT LOADED'; exit(1); } echo '✅ PDO_PGSQL OK';"

# Installer Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copier Composer files
COPY composer.json composer.lock symfony.lock ./

# Installer dépendances
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copier le reste du projet
COPY . .

# Scripts Symfony
RUN composer run-script post-install-cmd

# Permissions
RUN mkdir -p var/cache var/log var/sessions \
    && chown -R www-data:www-data var public \
    && chmod -R 775 var

EXPOSE 8080

# CHOISISSEZ UNE DES TROIS SOLUTIONS CI-DESSOUS :

# Solution 1 (shell format) :
CMD php -S 0.0.0.0:${PORT:-8080} -t public

# OU Solution 2 (exec format avec sh) :
# CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t public"]

# OU Solution 3 (port fixe) :
# CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]