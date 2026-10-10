# syntax=docker/dockerfile:1

# ---- 1. Base: the PHP version and extensions the app really runs on -----------
# Composer runs in THIS image too (not in the stock composer image), so dependencies are
# resolved against the same PHP and extensions that serve requests.
FROM php:8.3-apache AS base

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpng-dev libjpeg-dev libfreetype6-dev libzip-dev libicu-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql gd zip intl bcmath opcache pcntl \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# ---- 2. PHP dependencies (no dev packages) -----------------------------------
FROM base AS vendor

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1 COMPOSER_NO_INTERACTION=1
WORKDIR /app

# dependency layer: only re-runs when the lock file changes
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-progress --prefer-dist --no-scripts --no-autoloader

# application code, then the optimized class map
COPY . .
RUN composer dump-autoload --no-dev --classmap-authoritative --no-scripts

# ---- 3. Runtime image ---------------------------------------------------------
FROM base AS runtime

# serve /public, never the project root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY docker/php.ini "$PHP_INI_DIR/conf.d/zz-app.ini"
COPY docker/apache-laravel.conf /etc/apache2/conf-available/laravel.conf
RUN a2enconf laravel

WORKDIR /var/www/html
COPY --from=vendor /app .

COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs storage/app/public bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

HEALTHCHECK --interval=30s --timeout=5s --start-period=40s --retries=3 \
    CMD curl -fsS http://127.0.0.1/up || exit 1

ENTRYPOINT ["entrypoint"]
CMD ["apache2-foreground"]
