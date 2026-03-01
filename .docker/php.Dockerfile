ARG PHP_VERSION

FROM php:${PHP_VERSION}-cli-alpine

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
