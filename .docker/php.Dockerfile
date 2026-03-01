ARG PHP_VERSION

FROM php:${PHP_VERSION}-cli-alpine

RUN apk add --no-cache $PHPIZE_DEPS \
    && rm -rf /tmp/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
