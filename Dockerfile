# Dockerfile

# Stage 1: Build des dépendances
FROM composer:2.6 as build
WORKDIR /app
COPY composer.* ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist

COPY . .
RUN composer dump-autoload --optimize --classmap-authoritative

# Stage 2: Production
FROM php:8.32-fpm-alpine

# Composer (pour l’entrypoint : composer install si vendor/ absent)
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Installation des extensions PHP + su-exec (pour drop privileges dans entrypoint)
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    unzip \
    git \
    mysql-client \
    su-exec \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    gd \
    opcache \
    pcntl \
    zip

# Configuration PHP optimisée pour production
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.max_accelerated_files=20000'; \
    echo 'opcache.validate_timestamps=0'; \
} > /usr/local/etc/php/conf.d/opcache.ini

# Créer utilisateur non-root
RUN addgroup -g 1000 todo && \
    adduser -D -u 1000 -G todo todo

WORKDIR /var/www/html

# Copier les fichiers depuis le stage build
COPY --from=build --chown=todo:todo /app /var/www/html

# Permissions
RUN chown -R todo:todo storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# Entrypoint (lance les commandes artisan puis PHP-FPM)
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]

EXPOSE 9000

CMD ["php-fpm"]
