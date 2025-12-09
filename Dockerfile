# ----------------------------
# Base image PHP avec Apache
# ----------------------------
FROM php:8.3-apache

# ----------------------------
# Activer Apache mod_rewrite pour Symfony/API Platform
# ----------------------------
RUN a2enmod rewrite

# ----------------------------
# Installer les dépendances système et PostgreSQL
# ----------------------------
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

# ----------------------------
# Installer Composer
# ----------------------------
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ----------------------------
# Définir le répertoire de travail
# ----------------------------
WORKDIR /var/www/html

# ----------------------------
# Copier uniquement les fichiers Composer pour installer les dépendances d'abord
# ----------------------------
COPY composer.json composer.lock symfony.lock ./

# Installer les dépendances Symfony
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# ----------------------------
# Copier tout le reste du projet
# ----------------------------
COPY . .

# ----------------------------
# Exécuter les scripts post-installation Symfony
# ----------------------------
RUN composer run-script post-install-cmd

# ----------------------------
# Configurer Apache pour pointer vers le dossier public/
# ----------------------------
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# ----------------------------
# Vérifier que PDO_PGSQL est chargé
# ----------------------------
RUN php -r "if (!extension_loaded('pdo_pgsql')) { echo '❌ PDO_PGSQL NOT LOADED'; exit(1); } echo '✅ PDO_PGSQL OK';"

# ----------------------------
# Exposer le port HTTP standard
# ----------------------------
EXPOSE 80

# ----------------------------
# Commande de démarrage Apache (fonctionne automatiquement avec Railway)
# ----------------------------
CMD ["apache2-foreground"]
