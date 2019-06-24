ARG PHP_VERSION=7.1

FROM php:${PHP_VERSION}-cli

# libpng-dev needed by gd
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev

RUN docker-php-ext-install \
    mbstring \
    opcache \
    pdo \
    pdo_mysql \
    gd

WORKDIR /code
