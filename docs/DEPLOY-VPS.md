# Déploiement initial sur le VPS

Le job GitHub Actions **Deploy to Production** suppose que le serveur est déjà configuré. À faire **une seule fois** sur le VPS.

## 1. Connexion au VPS

```bash
ssh root@157.180.45.17
```

## 2. Créer le répertoire et cloner le projet

```bash
# Chemin absolu (avec le / au début)
sudo mkdir -p /var/www/app
sudo chown root:root /var/www/app   # ou ton utilisateur dédié si tu en crées un
cd /var/www/app

# Cloner le dépôt (dans un sous-dossier, pratique si tu déploies plusieurs apps)
git clone https://github.com/sdiagne148/code-propre.git

```

## 3. Installer PHP, Composer, Nginx (si pas déjà fait)

Sur Ubuntu/Debian :

```bash
sudo apt update
sudo apt install -y software-properties-common
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update
sudo apt install -y php8.2-fpm php8.2-cli php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip unzip nginx
# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

Vérifier : `php -v` et `composer -V`.

Si `composer install` échoue avec des erreurs d'extensions manquantes, installe-les puis relance :

```bash
sudo apt update
sudo apt install -y php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-mysql unzip
```

## 4. Configurer MySQL et la base de données

Une erreur du type `SQLSTATE[HY000] [2002] Connection refused` signifie que MySQL n'est pas joignable (pas installé, pas démarré, ou mauvais host/port).

### Installer MySQL (si besoin)

```bash
sudo apt update
sudo apt install -y mysql-server
sudo systemctl enable --now mysql
sudo systemctl status mysql
```

### Créer la base et l'utilisateur

```bash
sudo mysql

CREATE DATABASE todo_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'todo_user'@'localhost' IDENTIFIED BY 'todo@2026';
GRANT ALL PRIVILEGES ON todo_app.* TO 'todo_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## 5. Environnement et dépendances

```bash
cd /var/www/app/code-propre
cp .env.example .env
nano .env
```
### Adapter `.env` de production (sur le VPS)

Dans `/var/www/app/code-propre/.env` :

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=http://157.180.45.17   # ou ton nom de domaine

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306                   # ne pas garder 8889 (MAMP) sur le serveur
DB_DATABASE=todo_app
DB_USERNAME=todo_user
DB_PASSWORD=motdepassefort
```

# IMPORTANT : artisan nécessite vendor/autoload.php → installer d'abord Composer
# (si tu exécutes Composer en root, supprime l'avertissement)
export COMPOSER_ALLOW_SUPERUSER=1
composer install --no-dev --optimize-autoloader

php artisan key:generate
php artisan migrate --force

# Permissions recommandées pour Laravel (logs, cache, sessions, vues compilées)
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache


## 6. Serveur web (Nginx) et PHP-FPM

Objectif : exposer Laravel sur HTTP(s) via Nginx, en pointant vers `public/`.

### 6.0. Rappel : qui fait quoi ?

- **Nginx** est le **serveur HTTP** : il reçoit les requêtes du navigateur, sert les fichiers statiques (CSS, JS, images) et transmet les requêtes PHP à un backend.
- **PHP-FPM** (*PHP FastCGI Process Manager*) est le **moteur qui exécute le code PHP** (par exemple `public/index.php` de Laravel).  
- Nginx **ne sait pas exécuter du PHP tout seul** : pour chaque requête dynamique, il envoie la requête à PHP-FPM (socket FastCGI), récupère la réponse HTML/JSON, puis la renvoie au navigateur.

Ce couple **Nginx + PHP-FPM** remplace l'ancien modèle *Apache + mod_php*, avec de meilleures performances et une configuration plus flexible (plusieurs pools FPM, tuning des workers, etc.).

### 6.1. Créer le vhost Nginx

```bash
sudo nano /etc/nginx/sites-available/code-propre.conf
```

Exemple minimal :

```nginx
server {
    listen 80;
    server_name 157.180.45.17; # ou ton nom de domaine

    root /var/www/app/code-propre/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

Activer le site et recharger Nginx :

```bash
sudo ln -s /etc/nginx/sites-available/code-propre.conf /etc/nginx/sites-enabled/code-propre.conf
sudo nginx -t
sudo systemctl reload nginx
sudo systemctl reload php8.2-fpm
```

# Nginx
sudo systemctl status nginx
sudo systemctl start nginx
sudo systemctl stop nginx
sudo systemctl restart nginx     # stop + start
sudo systemctl reload nginx      # recharge la config sans couper les connexions

# PHP-FPM 8.2
sudo systemctl status php8.2-fpm
sudo systemctl start php8.2-fpm
sudo systemctl stop php8.2-fpm
sudo systemctl restart php8.2-fpm
sudo systemctl reload php8.2-fpm

Ensuite, depuis ton navigateur : `http://157.180.45.17` (ou ton domaine) doit afficher l’application Laravel.

## 7. Secrets GitHub pour le job Deploy

Dans **Settings → Secrets and variables → Actions** du repo, définir :

| Secret        | Valeur              |
|---------------|---------------------|
| `SERVER_HOST` | `157.180.45.17`     |
| `SERVER_USER` | `root`             |
| `SERVER_SSH_KEY` | Clé privée SSH du serveur (ou d’une clé dédiée déploy) |
| `SERVER_PATH` | `/var/www/app/code-propre` |

**Important :** `SERVER_USER` doit être exactement `root` (sans espace avant ou après).

## 8. Après cette config

À chaque **push sur `main`**, le job fera :

- `cd $SERVER_PATH` (ex. `/var/www/app/code-propre`)
- `git pull origin main`
- `composer install --no-dev ...`
- `php artisan migrate --force`
- caches + `systemctl reload php8.2-fpm`

---
