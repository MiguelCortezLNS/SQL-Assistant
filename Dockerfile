FROM php:8.3-cli

WORKDIR /app

COPY . .

RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libzip-dev \
    zip

RUN docker-php-ext-install zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader

RUN chmod -R 777 storage bootstrap/cache

EXPOSE 10000

CMD touch database/database.sqlite && php artisan migrate --force && php -S 0.0.0.0:10000 -t public