FROM php:8.3-fpm
WORKDIR /var/www
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip git curl
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY . .
RUN composer install --optimize-autoloader --no-dev
RUN php artisan key:generate
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
EXPOSE 9000
CMD ["php-fpm"]