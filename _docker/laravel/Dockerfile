FROM php:8.2-fpm

WORKDIR /var/www/restaurant_api

RUN apt-get update && apt-get install -y \
    apt-utils \
    libpq-dev  \
    libpng-dev \
    libzip-dev \
    build-essential \
    locales \
    zip \
    unzip \
    git \
    curl \
    netcat-openbsd \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && docker-php-ext-install pdo_pgsql pgsql

RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin

CMD ["php-fpm"]
