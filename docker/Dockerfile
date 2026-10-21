# syntax=docker/dockerfile:1

############################################
# Stage 1: build frontend assets
############################################
FROM node:22-alpine AS frontend-deps
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

FROM node:22-alpine AS frontend
WORKDIR /app
COPY --from=frontend-deps /app/node_modules ./node_modules
COPY . .
RUN npm run build

############################################
# Stage 2: composer dependencies
############################################
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --ignore-platform-req=ext-gd
COPY . .
RUN composer dump-autoload --optimize --no-dev

############################################
# Stage 3: php-fpm runtime
############################################
FROM php:8.3-fpm-alpine AS runtime

RUN apk add --no-cache icu-dev oniguruma-dev libzip-dev rsync \
        libpng-dev libjpeg-turbo-dev freetype-dev libwebp-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mbstring \
        intl \
        zip \
        gd \
        pcntl \
        bcmath \
        exif \
        opcache \
    && apk add --no-cache --virtual .redis-build-deps ${PHPIZE_DEPS} \
    && mkdir -p /tmp/phpredis \
    && curl -fsSL https://github.com/phpredis/phpredis/archive/refs/tags/6.3.0.tar.gz -o /tmp/phpredis.tar.gz \
    && tar -xzf /tmp/phpredis.tar.gz -C /tmp/phpredis --strip-components=1 \
    && (cd /tmp/phpredis && phpize && ./configure && make -j"$(nproc)" && make install) \
    && docker-php-ext-enable redis \
    && apk del .redis-build-deps \
    && rm -rf /tmp/* /var/cache/apk/*

COPY docker/php/conf.d/*.ini /usr/local/etc/php/conf.d/
RUN rm -f /usr/local/etc/php-fpm.d/www.conf
COPY docker/php/pool.d/www.conf /usr/local/etc/php-fpm.d/www.conf

# Application code (pristine copy in /opt/src, synced to volume on boot)
COPY --from=vendor /app /opt/src
COPY --from=frontend /app/public/build /opt/src/public/build
RUN rm -f /opt/src/.env

WORKDIR /var/www/html
COPY --chown=www-data:www-data docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh \
    && mkdir -p /var/www/html

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm", "-F"]
