# Base PHP avec Apache
FROM php:8.3-apache

# Activer mod_rewrite pour Symfony
RUN a2enmod rewrite

# Installer les dépendances système et extensions PHP nécessaires
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

# Autoriser Composer à exécuter les plugins en root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Ajouter le chemin global de Composer pour Symfony Flex
ENV PATH="$PATH:/root/.composer/vendor/bin"

WORKDIR /var/www/html

# Copier les fichiers composer
COPY composer.json composer.lock symfony.lock ./

# Installer les dépendances PHP sans exécuter les scripts (évite l'erreur symfony-cmd)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Installer Symfony Flex globalement pour que symfony-cmd soit disponible (optionnel)
RUN composer global require symfony/flex

# Copier tout le projet
COPY . .

# Optionnel : exécuter les scripts post-install si nécessaire
RUN composer run-script post-install-cmd || echo "Skipping symfony-cmd for root"

# Configurer Apache pour servir le dossier public/
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Exposer le port attendu par Fly.io
EXPOSE 8080

# Commande de lancement
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
