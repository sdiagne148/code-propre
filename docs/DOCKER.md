# Docker – Lancer l’application en conteneurs

Ce document décrit comment faire tourner le projet Laravel avec **Docker** et **Docker Compose** (app PHP-FPM, Nginx, MySQL, Redis, queue, scheduler).

## Prérequis

- [Docker](https://docs.docker.com/get-docker/) et [Docker Compose](https://docs.docker.com/compose/install/) installés
- En ligne de commande : `docker --version` et `docker compose version`

## 1. Configuration `.env` pour Docker

Copie `.env.example` vers `.env` et adapte les variables pour que l’app parle aux conteneurs (hosts = noms des services) :

```bash
cp .env.example .env
```

Dans `.env`, assure-toi d’avoir au minimum :

```dotenv
APP_NAME=TodoApp
APP_ENV=local
APP_KEY=                    # sera généré à l’étape suivante
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql               # nom du service dans docker-compose
DB_PORT=3306
DB_DATABASE=todo_app
DB_USERNAME=root            # ou l’utilisateur défini dans MYSQL_USER
DB_PASSWORD=                # même valeur que MYSQL_PASSWORD / MYSQL_ROOT_PASSWORD

# Optionnel : si tu utilises Redis pour cache/queue
REDIS_HOST=redis
REDIS_PORT=6379

# Optionnel : mot de passe root MySQL (sinon = DB_PASSWORD)
# MYSQL_ROOT_PASSWORD=secret
```

Les services **mysql** et **redis** sont accessibles via les noms `mysql` et `redis` sur le réseau Docker.

## 2. Premier lancement

L’**entrypoint** du conteneur `app` automatise au démarrage :

| Action | Quand |
|--------|--------|
| `composer install --no-dev` | Si `vendor/autoload.php` n’existe pas (premier run ou volume vide). |
| Création de `.env` | Si `.env` absent → copie de `.env.example`. |
| `php artisan key:generate` | Si `APP_KEY` est vide dans `.env`. |
| Attente MySQL | Boucle jusqu’à ce que `php artisan db:show` réussisse. |
| Permissions | `storage` et `bootstrap/cache` (utilisateur `todo`). |
| `package:discover`, caches | Nettoyage puis config/route/view cache. |
| `php artisan migrate --force` | À chaque démarrage du conteneur. |
| Démarrage PHP-FPM | En fin de script. |

### À faire une seule fois

1. **Préparer `.env` pour Docker** (voir section 1) : au minimum `DB_HOST=mysql`, `DB_PORT=3306`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`. Si tu n’as pas de `.env`, tu peux démarrer sans : l’entrypoint copiera `.env.example` vers `.env` et générera `APP_KEY` ; tu pourras éditer `.env` ensuite et redémarrer.
2. **Lancer les services** :  
   `docker compose up -d`

Aucune commande manuelle obligatoire : dépendances, clé, migrations sont gérées par l’entrypoint. L’application est accessible sur **http://localhost** (port 80).

## 3. Commandes utiles

| Action | Commande |
|--------|----------|
| Démarrer les conteneurs | `docker compose up -d` |
| Arrêter les conteneurs | `docker compose down` |
| Voir les logs | `docker compose logs -f` (tous) ou `docker compose logs -f app` (un service) |
| Shell dans le conteneur Laravel | `docker compose exec app sh` |
| Exécuter une commande Artisan | `docker compose exec app php artisan <commande>` |
| Reconstruire l’image après modification du Dockerfile | `docker compose build --no-cache` puis `docker compose up -d` |
| Installer des dépendances Composer | `docker compose run --rm app composer require <package>` |

## 4. Structure des fichiers Docker

| Fichier / dossier | Rôle |
|-------------------|------|
| **`Dockerfile`** | Build de l’image PHP 8.2-FPM (Composer + extensions + utilisateur `todo`). |
| **`docker-compose.yml`** | Définition des services : `app` (Laravel), `nginx`, `mysql`, `redis`, `queue`, `scheduler`. |
| **`docker-compose.pull.yml`** | Même stack en utilisant l’image Docker Hub pour `app` (sans build). Pour testeurs / démo. |
| **`docker/nginx/default.conf`** | Configuration Nginx (racine = `public/`, PHP vers `app:9000`). |
| **`docker/nginx/ssl/`** | Dossier pour certificats HTTPS (optionnel). |
| **`docker/mysql/my.cnf`** | Configuration MySQL (utf8mb4, collation). |
| **`entrypoint.sh`** | Script au démarrage du conteneur **app** : attente MySQL, permissions, `package:discover`, nettoyage et mise en cache Laravel, puis lancement de PHP-FPM. |
| **`.dockerignore`** | Fichiers exclus du contexte de build (`.git`, `vendor`, `.env`, etc.). |

## 5. Services décrits dans `docker-compose.yml`

- **app** : Laravel (PHP-FPM), port 9000 en interne.
- **nginx** : Serveur web, ports 80 et 443 exposés.
- **mysql** : MySQL 8.0, port 3306, volume persistant `mysql_data`.
- **redis** : Redis 7, port 6379, volume `redis_data`.
- **queue** : Worker Laravel `php artisan queue:work`.
- **scheduler** : Boucle qui exécute `php artisan schedule:run` toutes les minutes.

Pour utiliser la **queue** en Redis, mets dans `.env` :  
`QUEUE_CONNECTION=redis` et `REDIS_HOST=redis`.

## 6. Tester avec l’image Docker Hub (sans builder)

Si l’image est déjà publiée sur Docker Hub (ex. `dserigne/todo-app:latest`), un utilisateur peut faire tourner **toute la stack** (app + MySQL + Nginx + Redis) sans construire l’image lui‑même.

**Important :** `docker pull dserigne/todo-app:latest` ne récupère que l’image de l’application. Pour que l’app fonctionne, il faut aussi MySQL, Nginx et Redis. Il faut donc cloner le dépôt (pour la config Nginx et le `.env`) et lancer le Compose qui utilise l’image.

### Étapes pour le testeur

1. **Cloner le dépôt** (pour avoir `docker-compose.pull.yml`, `docker/nginx/`, `.env.example`) :
   ```bash
   git clone <url-du-dépôt>   # ex. https://github.com/dserigne/code-propre.git
   cd code-propre
   ```

2. **Créer le fichier `.env`** à partir de `.env.example` et renseigner au minimum la base de données :
   ```bash
   cp .env.example .env
   ```
   Dans `.env`, vérifier notamment :
   - `DB_HOST=mysql`
   - `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (cohérents avec les variables `MYSQL_*` du Compose)

3. **Lancer toute la stack** avec le Compose « pull » (Compose télécharge l’image si besoin) :
   ```bash
   docker compose -f docker-compose.pull.yml up -d
   ```

4. Ouvrir **http://localhost**. L’entrypoint du conteneur `app` attend MySQL, applique les migrations, etc.

Pour arrêter : `docker compose -f docker-compose.pull.yml down`.

## 7. Dépannage rapide

- **Erreur « Connection refused » vers MySQL**  
  Vérifier que `DB_HOST=mysql`, `DB_PORT=3306` et que le conteneur `todo-mysql` est bien démarré (`docker compose ps`).

- **Page blanche ou 502**  
  Vérifier les logs : `docker compose logs app nginx`. S’assurer que `vendor/` existe (étape « composer install » ci‑dessus).

- **Permissions sur `storage` ou `bootstrap/cache`**  
  À l’intérieur du conteneur, les dossiers sont détenus par l’utilisateur `laravel`. En local, si besoin :  
  `docker compose exec app chown -R laravel:laravel storage bootstrap/cache`.

- **Réinitialiser tout (volumes compris)**  
  `docker compose down -v` puis refaire les étapes du « Premier lancement ».

---

## 8. Entrypoint du conteneur `app`

Au démarrage du conteneur **app**, le script **`entrypoint.sh`** exécute dans l’ordre :

1. **Composer** : si `vendor/autoload.php` absent → `composer install --no-dev`.
2. **`.env`** : si absent → copie de `.env.example` vers `.env`.
3. **Clé Laravel** : si `APP_KEY` vide → `php artisan key:generate --force`.
4. **Attente MySQL** : boucle `php artisan db:show` jusqu’à connexion.
5. **Permissions** : `chown` / `chmod` sur `storage` et `bootstrap/cache`.
6. **Package discover** puis nettoyage des caches (config, cache, vues).
7. **Optimisation** : `config:cache`, `route:cache`, `view:cache`.
8. **Migrations** : `php artisan migrate --force`.
9. **PHP-FPM** : lancement sous l’utilisateur `todo` via `su-exec`.

Pour désactiver les migrations automatiques, commenter ou supprimer dans `entrypoint.sh` les deux lignes « Exécution des migrations ».
