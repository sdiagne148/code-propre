#!/bin/sh
set -e

# Installer les dépendances si vendor/ absent (ex. premier run avec volume monté)
if [ ! -f /var/www/html/vendor/autoload.php ]; then
    echo "Installation des dépendances Composer..."
    composer install --no-dev --optimize-autoloader
fi

# Créer .env à partir de .env.example si absent (premier run)
if [ ! -f /var/www/html/.env ]; then
    echo "Création de .env depuis .env.example..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Générer APP_KEY si vide (Laravel ne l'écrase pas s'il existe déjà)
if ! grep -q '^APP_KEY=base64:' /var/www/html/.env 2>/dev/null; then
    echo "Génération de la clé Laravel..."
    php artisan key:generate --force
fi

echo "Attente de la base de données..."
until php artisan db:show 2>/dev/null; do
    echo "⏳ En attente de la base de données..."
    sleep 3
done
echo "Base de données connectée"

echo "Exécution des migrations..."
php artisan migrate --force

echo "Configuration des permissions..."
chown -R todo:todo /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "Exécution des scripts Composer post-autoload..."
php artisan package:discover --ansi

echo "Nettoyage du cache..."
php artisan config:clear
php artisan cache:clear 2>/dev/null || true
php artisan view:clear

echo "Optimisation Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Démarrage de PHP-FPM..."
exec php-fpm
