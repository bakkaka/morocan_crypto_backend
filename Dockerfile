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
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    mysqli \
    zip \
    intl \
    opcache \
    bcmath \
    xml \
    soap \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configuration PHP optimisée
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory.ini \
    && echo "upload_max_filesize=50M" >> /usr/local/etc/php/conf.d/memory.ini \
    && echo "post_max_size=50M" >> /usr/local/etc/php/conf.d/memory.ini \
    && echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/memory.ini

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail (racine du projet Symfony)
WORKDIR /app

# Copier composer files
COPY composer.json composer.lock symfony.lock ./

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --prefer-dist

# Copier le reste de l'application
COPY . .

# Finaliser l'installation Composer
RUN composer dump-autoload --optimize --no-dev

# Créer les répertoires nécessaires et définir les permissions
RUN mkdir -p var/cache var/log var/sessions \
    && chown -R www-data:www-data var public \
    && chmod -R 775 var

# Exposer le port (Railway utilise la variable $PORT)
EXPOSE 8000

# Commande de démarrage
# -t public : définit public/ comme document root (où se trouve index.php)
CMD php bin/console cache:clear --env=prod --no-debug && \
    php bin/console cache:warmup --env=prod && \
    php bin/console doctrine:schema:update --force && \
    php -S 0.0.0.0:${PORT:-8000} -t public