FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    postgresql-dev \
    libpng-dev \
    zip \
    unzip \
    git

RUN docker-php-ext-install pdo pdo_pgsql gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

CMD composer install && \
    php artisan key:generate --force && \
    php artisan migrate --force --seed && \
    php artisan serve --host=0.0.0.0 --port=8000
