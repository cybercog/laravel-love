# ----------------------
# The FPM base container
# ----------------------
FROM php:8.2-cli-alpine AS dev

RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS

# Cleanup apk cache and temp files
RUN rm -rf /var/cache/apk/* /tmp/*

# ----------------------
# Composer install step
# ----------------------

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ----------------------
# The FPM production container
# ----------------------
FROM dev
