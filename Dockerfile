FROM php:8.3-cli

WORKDIR /app

COPY . .

RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libzip-dev \
    zip \
    libpq-dev

RUN docker-php-ext-install zip pdo_pgsql pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader

RUN chmod -R 777 storage bootstrap/cache

EXPOSE 10000

CMD sh -c "php artisan migrate --force && { php artisan schedule:work & php artisan serve --host=0.0.0.0 --port=$PORT; }"