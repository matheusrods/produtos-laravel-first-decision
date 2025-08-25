FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    bash git curl icu-dev oniguruma-dev libzip-dev zlib-dev \
    libpng libpng-dev libjpeg-turbo-dev freetype-dev \
    libxml2-dev autoconf build-base

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath intl zip gd

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www/html

RUN addgroup -g 1000 app && adduser -D -G app -u 1000 app \
 && mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
 && chown -R app:app /var/www/html

COPY --chown=app:app backend/composer.json backend/composer.lock ./

RUN composer install --no-scripts --no-autoloader --no-interaction --prefer-dist

COPY --chown=app:app backend ./

RUN composer dump-autoload --optimize

USER app