# Plan de Mise en Route - TheCrossLove

## Objectif
Faire fonctionner le projet TheCrossLove (Symfony 7.3) sur les deux environnements (WAMP + Docker) pour la préparation du jury CDA.

---

## Étape 1 : Vérification des Prérequis

**Commandes à exécuter :**
```cmd
cd C:\Users\Administrateur\Documents\DOSSIER_PROJET_CDA\TheCrossLove
php -v
composer --version
```

**Vérifications :**
- PHP 8.2+ installé
- Composer disponible
- WAMP actif (icône verte)

---

## Étape 2 : Installation des Dépendances

```cmd
composer install
```

Cela installera toutes les dépendances Symfony et exécutera automatiquement :
- `cache:clear`
- `assets:install`
- `importmap:install`

---

## Étape 3 : Configuration Base de Données WAMP

**Vérifier le fichier `.env.local` :**
```env
DATABASE_URL="mysql://root:root@127.0.0.1:3307/thecrosslove?serverVersion=11.5.2-MariaDB&charset=utf8mb4"
```

Si le mot de passe root est vide sur WAMP, utiliser :
```env
DATABASE_URL="mysql://root:@127.0.0.1:3307/thecrosslove?serverVersion=11.5.2-MariaDB&charset=utf8mb4"
```

---

## Étape 4 : Création et Migration BDD

```cmd
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction
```

**Résultat attendu :**
- Base `thecrosslove` créée
- 5 migrations exécutées
- Données de test chargées (9 users, 4 catégories, 12 événements)

---

## Étape 5 : Démarrage du Serveur

**Option A (recommandée) :**
```cmd
symfony server:start
```

**Option B (alternative) :**
```cmd
php -S localhost:8000 -t public
```

**Accès :** http://localhost:8000

---

## Étape 6 : Tests de Vérification

| Test | URL | Action |
|------|-----|--------|
| Page d'accueil | http://localhost:8000 | Vérifier affichage |
| Connexion admin | http://localhost:8000/login | `admin@thecrosslove.com` / `admin123` |
| Dashboard admin | http://localhost:8000/admin | Vérifier accès |
| Connexion user | http://localhost:8000/login | `john.doe@example.com` / `password123` |
| Liste événements | http://localhost:8000/events | Vérifier les événements |

---

## Configuration Docker (Alternative)

Pour basculer vers Docker :

```cmd
docker-compose up -d
```

Modifier `.env.local` :
```env
DATABASE_URL="mysql://root:root@127.0.0.1:3308/thecrosslove?serverVersion=8.0&charset=utf8mb4"
```

Puis recréer la BDD Docker :
```cmd
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction
```

**Services Docker :**
- phpMyAdmin : http://localhost:8080 (root/root)
- MailCatcher : http://localhost:1081

---

## Fichiers Critiques à Modifier

| Fichier | Rôle |
|---------|------|
| `.env.local` | Configuration BDD active |
| `config/packages/security.yaml` | Authentification |
| `src/DataFixtures/AppFixtures.php` | Données de test |

---

## Commande Rapide (Tout-en-un WAMP)

```cmd
cd C:\Users\Administrateur\Documents\DOSSIER_PROJET_CDA\TheCrossLove
composer install
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction
php bin/console cache:clear
symfony server:start
```

---

## Vérification Finale

```cmd
php bin/console debug:router | findstr "login admin events"
php bin/console doctrine:schema:validate
```

---

## Comptes de Test

| Email | Mot de passe | Rôle |
|-------|--------------|------|
| admin@thecrosslove.com | admin123 | ROLE_ADMIN |
| john.doe@example.com | password123 | ROLE_USER |
| marie.martin@example.com | password123 | ROLE_USER |

---

## Dépannage

| Problème | Solution |
|----------|----------|
| Connexion BDD refusée | Vérifier port (3307 WAMP / 3308 Docker) |
| Mot de passe root | Essayer vide `:@` ou `root:root` |
| Cache corrompu | `php bin/console cache:clear` |
| Port 8000 occupé | `symfony server:start --port=8001` |
| Template manquant | Vérifier `templates/` et relancer `cache:clear` |

---

## Structure du Projet

```
TheCrossLove/
├── config/                 # Configuration Symfony
├── public/                 # Fichiers publics (images, assets)
│   └── images/            # Logos et images
├── src/
│   ├── Controller/        # Contrôleurs
│   ├── Entity/            # Entités Doctrine
│   ├── Repository/        # Repositories
│   └── DataFixtures/      # Données de test
├── templates/             # Templates Twig
│   ├── admin/            # Dashboard admin
│   ├── event/            # Pages événements
│   ├── partials/         # Header, footer, nav
│   └── security/         # Login, register
├── .env.local            # Configuration locale
└── composer.json         # Dépendances PHP
```

---

## URLs Importantes

| Page | URL |
|------|-----|
| Accueil | http://localhost:8000 |
| Événements | http://localhost:8000/events |
| Calendrier | http://localhost:8000/calendar |
| Connexion | http://localhost:8000/login |
| Inscription | http://localhost:8000/register |
| Admin | http://localhost:8000/admin |

---

*Document généré le 31/01/2026 pour la préparation du jury CDA*
