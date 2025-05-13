FROM php:8.2-fpm-alpine

RUN apk update && \
    apk add --no-cache \
    postgresql-dev \
    unzip \
    git \
    curl \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    supervisor

RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) \
    gd \
    pdo \
    pdo_pgsql \
    zip \
    opcache

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./

COPY . .

RUN composer install --optimize-autoloader --no-interaction --no-progress --prefer-dist

RUN chown -R www-data:www-data var/