FROM php:8.3-cli-alpine AS dev

RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS

RUN rm -rf /var/cache/apk/* /tmp/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

FROM dev
