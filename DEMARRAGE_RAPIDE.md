# Guide de Démarrage Rapide - TheCrossLove

Ce guide vous permet de faire fonctionner le projet en quelques minutes.

---

## Option A : Avec WAMP/XAMPP (MariaDB local)

### 1. Prérequis
- WAMP/XAMPP installé avec PHP 8.2+ et MariaDB/MySQL
- Composer installé globalement

### 2. Configuration

```bash
# Cloner le projet (si pas encore fait)
cd C:\wamp64\www  # ou votre dossier web
git clone <url-du-repo> TheCrossLove
cd TheCrossLove

# Installer les dépendances PHP
composer install
```

### 3. Vérifier le fichier .env

Ouvrez `.env` et vérifiez la configuration de la base de données :

```env
# Pour MariaDB sur WAMP (port 3307 typiquement)
DATABASE_URL="mysql://root:@127.0.0.1:3307/thecrosslove?serverVersion=11.5.2-MariaDB&charset=utf8mb4"

# Pour MySQL sur WAMP (port 3306 typiquement)
DATABASE_URL="mysql://root:@127.0.0.1:3306/thecrosslove?serverVersion=8.0&charset=utf8mb4"
```

> **Note** : Sur WAMP, le mot de passe root est souvent vide (pas de `root:root` mais `root:`)

### 4. Créer la base de données

```bash
# Créer la base de données
php bin/console doctrine:database:create --if-not-exists

# Exécuter les migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Charger les données de test
php bin/console doctrine:fixtures:load --no-interaction
```

### 5. Démarrer le serveur

```bash
# Option 1 : Serveur Symfony (recommandé)
symfony server:start

# Option 2 : Serveur PHP intégré
php -S localhost:8000 -t public/
```

### 6. Accéder à l'application

- **Application** : http://localhost:8000
- **phpMyAdmin** : http://localhost/phpmyadmin (via WAMP)

### 7. Comptes de test

| Email | Mot de passe | Rôle |
|-------|--------------|------|
| admin@thecrosslove.com | admin123 | Admin |
| john.doe@example.com | password123 | Utilisateur |

---

## Option B : Avec Docker

### 1. Prérequis
- Docker Desktop installé et démarré

### 2. Configuration

```bash
# Copier la configuration Docker
copy .env.docker .env.local

# OU sur Linux/Mac
cp .env.docker .env.local
```

### 3. Démarrer les conteneurs

```bash
# Démarrer MySQL, phpMyAdmin et MailCatcher
docker compose up -d

# Attendre que MySQL soit prêt (~60 secondes)
# Vérifier avec :
docker compose ps
```

### 4. Installer les dépendances et initialiser

```bash
# Installer les dépendances PHP
composer install

# Créer la base de données et les tables
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction

# Charger les données de test
php bin/console doctrine:fixtures:load --no-interaction
```

### 5. Démarrer le serveur

```bash
php -S localhost:8000 -t public/
```

### 6. Accéder aux services

| Service | URL | Identifiants |
|---------|-----|--------------|
| **Application** | http://localhost:8000 | voir comptes de test |
| **phpMyAdmin** | http://localhost:8080 | root / root |
| **MailCatcher** | http://localhost:1080 | - |

### 7. Arrêter Docker

```bash
# Arrêter les conteneurs (données conservées)
docker compose down

# Arrêter et supprimer les données
docker compose down -v
```

---

## Résolution de Problèmes

### Erreur : "Could not find driver"
```bash
# Vérifier les extensions PHP
php -m | findstr pdo_mysql
# Si absent, activer dans php.ini : extension=pdo_mysql
```

### Erreur : "Connection refused" (Docker)
```bash
# Vérifier que les conteneurs tournent
docker compose ps

# Voir les logs MySQL
docker compose logs database
```

### Erreur : "Access denied for user 'root'@'localhost'"
- WAMP : Le mot de passe root est souvent vide
- Docker : Le mot de passe est `root` (défini dans compose.yaml)

### phpMyAdmin ne se connecte pas (Docker)
```bash
# Attendre que MySQL soit prêt (healthcheck)
docker compose logs database | findstr "ready for connections"
```

### Réinitialiser complètement la base de données
```bash
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction
```

---

## Commandes Utiles

```bash
# Voir toutes les routes
php bin/console debug:router

# Vérifier la configuration
php bin/console debug:config doctrine

# Créer une nouvelle migration après modification d'entité
php bin/console make:migration

# Exécuter les tests
php bin/phpunit

# Vider le cache
php bin/console cache:clear
```

---

## Structure des Comptes de Test

Les fixtures créent automatiquement :

**1 Admin :**
- admin@thecrosslove.com / admin123

**8 Utilisateurs :**
- john.doe@example.com / password123
- marie.martin@example.com / password123
- pierre.bernard@example.com / password123
- sophie.dubois@example.com / password123
- lucas.petit@example.com / password123
- emma.robert@example.com / password123
- thomas.richard@example.com / password123
- julie.moreau@example.com / password123

**4 Catégories :**
- Conférences, Ateliers, Sensibilisation, Humanitaire

**13 Événements :**
- 5 au Sénégal (Fatick)
- 5 en RDC (Bukavu/Sud Kivu)
- 1 événement passé
- 1 événement annulé
- 1 conférence internationale
