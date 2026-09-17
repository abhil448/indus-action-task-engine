FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git zip unzip \
    && docker-php-ext-install pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

EXPOSE 8000
CMD php artisan serve --host=0.0.0.0 --port=8000