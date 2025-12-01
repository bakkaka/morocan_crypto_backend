FROM php:8.2-fpm

# Packages nécessaires
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    git

# Extensions PHP
RUN docker-php-ext-install intl
RUN docker-php-ext-install pdo pdo_mysql

# Copier le projet dans le container
COPY . /app
WORKDIR /app

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-interaction --no-dev --optimize-autoloader

CMD ["php-fpm"]
