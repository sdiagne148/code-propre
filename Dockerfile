# Dockerfile

# Stage 1: Build des dépendances
FROM composer:2.9 as build
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
FROM php:8.3-fpm

# Installation des extensions PHP + outils (git, unzip, gosu, mysql-client)
RUN apt-get update && apt-get install -y \
    git unzip curl gosu default-mysql-client \
    libpng-dev libonig-dev libxml2-dev libzip-dev libicu-dev \
    libfreetype6-dev libjpeg62-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo intl pdo_mysql mbstring exif pcntl bcmath gd zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer (pour l’entrypoint : composer install si vendor/ absent)
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Configuration PHP optimisée pour production
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.max_accelerated_files=20000'; \
    echo 'opcache.validate_timestamps=0'; \
} > /usr/local/etc/php/conf.d/opcache.ini

# PHP-FPM : écouter sur 0.0.0.0:9000 pour Nginx
RUN sed -i 's/^listen = .*/listen = 0.0.0.0:9000/' /usr/local/etc/php-fpm.d/www.conf
# Forcer error_log vers un fichier (évite "Permission denied" sur /proc/self/fd/2)
RUN sed -i 's|^;*error_log =.*|error_log = /var/log/php-fpm.log|' /usr/local/etc/php-fpm.conf && \
    touch /var/log/php-fpm.log && chmod 666 /var/log/php-fpm.log

# Créer utilisateur non-root (syntaxe Debian)
RUN groupadd -g 1000 todo && \
    useradd -m -u 1000 -g todo -s /bin/sh todo

WORKDIR /var/www/html

# Copier les fichiers depuis le stage build
COPY --from=build --chown=todo:todo /app /var/www/html

# Entrypoint (lance les commandes artisan puis PHP-FPM)
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]

EXPOSE 9000

CMD ["php-fpm"]
