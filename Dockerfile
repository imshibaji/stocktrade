# syntax=docker/dockerfile:1

# ---------------------------------------------------------------------------
# StockTrade — CodeIgniter 4 (PHP 8.2+, SQLite, Tailwind CSS v4)
#
# Multi-stage build:
#   1. assets  — compile Tailwind CSS with Node
#   2. base    — PHP 8.2 + Apache with the extensions CodeIgniter needs
#   3. vendor  — install Composer production dependencies
#   4. runtime — final image (app + vendor + compiled CSS)
#
# Build:  docker build -t stocktrade .
# Run:    docker run --rm -p 8080:80 stocktrade
# ---------------------------------------------------------------------------

ARG PHP_VERSION=8.2

# ---------------------------------------------------------------------------
# Stage 1 — Frontend assets (Tailwind CSS)
# ---------------------------------------------------------------------------
FROM node:22-alpine AS assets
WORKDIR /build
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund
COPY src ./src
RUN npm run build:css

# ---------------------------------------------------------------------------
# Stage 2 — PHP base image (extensions, Apache config, Composer)
# ---------------------------------------------------------------------------
FROM php:${PHP_VERSION}-apache AS base

# Extensions required by CodeIgniter and this app.
# The official PHP image already enables json, mbstring, curl, sqlite3 and
# pdo_sqlite, so only intl, zip and opcache need to be added.
#   intl     — framework requirement (locale/format helpers)
#   zip      — Composer / archive handling
#   opcache  — production performance
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libicu-dev \
        libzip-dev \
        unzip \
        curl \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j"$(nproc)" \
        intl \
        zip \
        opcache \
    && a2enmod rewrite headers \
    && printf 'ServerName localhost\n' > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Point Apache at the CodeIgniter front controller (public/) and allow
# the framework's .htaccess rewrite rules.
COPY docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf

# Production PHP settings (opcache, limits, timezone)
COPY docker/php.ini /usr/local/etc/php/conf.d/zz-stocktrade.ini

# Composer binary (reused by the vendor stage)
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html

# ---------------------------------------------------------------------------
# Stage 3 — PHP dependencies (production only)
# ---------------------------------------------------------------------------
FROM base AS vendor
COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --no-interaction \
        --no-progress \
        --prefer-dist \
        --optimize-autoloader \
        --no-scripts

# ---------------------------------------------------------------------------
# Stage 4 — Runtime image
# ---------------------------------------------------------------------------
FROM base AS runtime

# Application source (see .dockerignore for what is left out)
COPY . .

# Dependencies and compiled CSS come from the earlier stages so the final
# image never needs Node or Composer at runtime.
COPY --from=vendor /var/www/html/vendor ./vendor
COPY --from=assets /build/public/css/style.css ./public/css/style.css

# writable/ is the only path the app needs to write to: cache, logs,
# sessions, uploads, debugbar and the SQLite database file.
RUN mkdir -p \
        writable/cache \
        writable/logs \
        writable/session \
        writable/uploads \
        writable/debugbar \
        writable/database \
    && chown -R www-data:www-data writable \
    && chmod -R 775 writable

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=25s --retries=3 \
    CMD curl -fsS http://127.0.0.1/ >/dev/null || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
