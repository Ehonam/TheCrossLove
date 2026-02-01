# Guide Complet - Projet TheCrossLove
## Document de Préparation pour la Défense CDA

---

# Table des Matières

1. [Présentation du Projet](#1-présentation-du-projet)
2. [Architecture de l'Application](#2-architecture-de-lapplication)
3. [Choix Technologiques](#3-choix-technologiques)
4. [Structure du Code](#4-structure-du-code)
5. [Modèle de Données](#5-modèle-de-données)
6. [Sécurité](#6-sécurité)
7. [Migrations](#7-migrations)
8. [Tests](#8-tests)
9. [Configuration Docker & phpMyAdmin](#9-configuration-docker--phpmyadmin)
10. [Déploiement](#10-déploiement)
11. [Questions Types du Jury](#11-questions-types-du-jury)

---

# 1. Présentation du Projet

## 1.1 Contexte

**TheCrossLove** est une plateforme de gestion d'événements humanitaires développée avec Symfony 7.3. Elle permet :
- Aux **utilisateurs** : de consulter et s'inscrire à des événements
- Aux **administrateurs** : de créer, modifier et gérer les événements et participants

## 1.2 Périmètre Fonctionnel

| Fonctionnalité | Utilisateur | Admin |
|----------------|:-----------:|:-----:|
| Voir les événements | ✅ | ✅ |
| S'inscrire à un événement | ✅ | ✅ |
| Voir ses inscriptions | ✅ | ✅ |
| Créer/Modifier un événement | ❌ | ✅ |
| Supprimer un événement | ❌ | ✅ |
| Voir les participants | ❌ | ✅ |
| Gérer les catégories | ❌ | ✅ |

---

# 2. Architecture de l'Application

## 2.1 Pattern MVC (Model-View-Controller)

```
┌─────────────────────────────────────────────────────────────────┐
│                        NAVIGATEUR                               │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                     FRONT CONTROLLER                            │
│                     public/index.php                            │
│  Point d'entrée unique de l'application                         │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                         KERNEL                                  │
│                      src/Kernel.php                             │
│  - Initialise Symfony                                           │
│  - Charge la configuration                                      │
│  - Gère le cycle de vie de la requête                          │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                         ROUTER                                  │
│                    config/routes.yaml                           │
│  Analyse l'URL et détermine le contrôleur à appeler            │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                      CONTROLLER (C)                             │
│                     src/Controller/                             │
│  - Reçoit la requête HTTP                                       │
│  - Coordonne la logique métier                                  │
│  - Retourne une réponse                                         │
└─────────────────────────────────────────────────────────────────┘
           │                                      │
           ▼                                      ▼
┌─────────────────────┐              ┌─────────────────────────────┐
│      MODEL (M)      │              │         VIEW (V)            │
│   src/Entity/       │              │       templates/            │
│   src/Repository/   │              │                             │
│                     │              │  Fichiers Twig (.html.twig) │
│ - Entités Doctrine  │              │  qui génèrent le HTML       │
│ - Accès aux données │              │                             │
└─────────────────────┘              └─────────────────────────────┘
           │
           ▼
┌─────────────────────┐
│     BASE DE         │
│     DONNÉES         │
│   MySQL/MariaDB     │
└─────────────────────┘
```

## 2.2 Flux d'une Requête HTTP

**Exemple : Affichage d'un événement (`GET /event/1`)**

```
1. Navigateur → GET /event/1
2. public/index.php (Front Controller)
3. Kernel analyse la requête
4. Router → EventController::show($id=1)
5. Controller → EventRepository::find(1)
6. Repository → SQL: SELECT * FROM event WHERE id_event = 1
7. Base de données → Retourne les données
8. Repository → Crée un objet Event
9. Controller → Passe $event au template
10. Twig → Génère le HTML (templates/event/show.html.twig)
11. Controller → Retourne Response avec HTML
12. Navigateur ← Affiche la page
```

## 2.3 Injection de Dépendances

**Principe fondamental de Symfony** : les objets ne créent pas leurs dépendances, elles leur sont injectées.

```php
// src/Controller/AdminController.php
class AdminController extends AbstractController
{
    // Injection via les paramètres de méthode (Autowiring)
    public function dashboard(EventRepository $eventRepository): Response
    {
        // Symfony injecte automatiquement le EventRepository
        $events = $eventRepository->findAll();
        // ...
    }
}
```

**Pourquoi ?**
- **Testabilité** : On peut injecter des mocks dans les tests
- **Découplage** : Le contrôleur ne dépend pas d'une implémentation concrète
- **Flexibilité** : On peut changer l'implémentation sans modifier le code

---

# 3. Choix Technologiques

## 3.1 Tableau Récapitulatif

| Technologie | Version | Justification |
|-------------|---------|---------------|
| **PHP** | 8.2+ | Typage strict, attributes, match expressions |
| **Symfony** | 7.3 | Framework enterprise, LTS, communauté active |
| **Doctrine ORM** | 3.5 | Abstraction BDD, migrations, relations |
| **MySQL/MariaDB** | 8.0/11.5 | SGBD relationnel robuste, UTF8MB4 |
| **Twig** | 3.0 | Moteur de templates sécurisé par défaut |
| **Bootstrap** | 5.3 | CSS responsive, composants prêts à l'emploi |
| **PHPUnit** | 12.4 | Tests unitaires et d'intégration |
| **Docker** | - | Conteneurisation, environnement reproductible |

## 3.2 Justifications Détaillées

### Pourquoi Symfony plutôt que Laravel ?

| Critère | Symfony | Laravel |
|---------|---------|---------|
| Architecture | Composants découplés | Monolithique |
| Entreprise | Standard en France | Plus startup/US |
| Doctrine | ORM Data Mapper | Eloquent (Active Record) |
| Flexibilité | Très configurable | Convention over configuration |
| Complexité | Courbe d'apprentissage + raide | Plus accessible |

**Choix Symfony** : Architecture professionnelle, utilisé dans les grandes entreprises françaises, meilleur pour un projet CDA qui doit démontrer des compétences avancées.

### Pourquoi Doctrine (Data Mapper) plutôt qu'Active Record ?

**Active Record** (comme Eloquent) :
```php
// L'entité gère elle-même sa persistence
$user = User::find(1);
$user->name = 'John';
$user->save();  // L'entité se sauvegarde elle-même
```

**Data Mapper** (Doctrine) :
```php
// L'EntityManager gère la persistence
$user = $entityManager->find(User::class, 1);
$user->setName('John');
$entityManager->flush();  // Le manager sauvegarde
```

**Avantages Data Mapper** :
- Séparation des responsabilités (SRP)
- Entités = objets PHP purs (POPO)
- Plus testable
- Gestion des transactions plus fine

### Pourquoi MySQL/MariaDB plutôt que PostgreSQL ?

| MySQL/MariaDB | PostgreSQL |
|---------------|------------|
| Plus répandu en hébergement mutualisé | Moins disponible |
| Performance lectures simples | Meilleur pour requêtes complexes |
| Plus simple à administrer | Plus de fonctionnalités |
| Compatible WAMP/MAMP natif | Nécessite configuration |

**Choix MySQL** : Compatibilité maximale, plus accessible pour un projet CDA, facilité de déploiement.

---

# 4. Structure du Code

## 4.1 Arborescence Complète

```
TheCrossLove/
│
├── bin/
│   └── console              # CLI Symfony (commandes)
│
├── config/                  # CONFIGURATION
│   ├── packages/            # Config des bundles
│   │   ├── doctrine.yaml    # Base de données
│   │   ├── security.yaml    # Authentification/Autorisation
│   │   ├── twig.yaml        # Moteur de templates
│   │   └── ...
│   ├── routes.yaml          # Définition des routes
│   └── services.yaml        # Services et injection
│
├── migrations/              # Versions du schéma BDD
│   └── Version*.php
│
├── public/                  # RACINE WEB (seul dossier accessible)
│   ├── index.php            # Front Controller
│   ├── css/                 # Feuilles de style
│   ├── images/              # Images statiques
│   └── uploads/             # Fichiers uploadés
│
├── src/                     # CODE SOURCE
│   ├── Controller/          # Contrôleurs (C du MVC)
│   ├── Entity/              # Entités Doctrine (M du MVC)
│   ├── Form/                # Types de formulaires
│   ├── Repository/          # Requêtes personnalisées
│   ├── DataFixtures/        # Données de test
│   └── Kernel.php           # Noyau Symfony
│
├── templates/               # TEMPLATES TWIG (V du MVC)
│   ├── base.html.twig       # Layout principal
│   ├── admin/               # Vues admin
│   ├── event/               # Vues événements
│   └── security/            # Vues authentification
│
├── tests/                   # TESTS
│   ├── Unit/                # Tests unitaires
│   └── Integration/         # Tests d'intégration
│
├── var/                     # Fichiers générés (cache, logs)
├── vendor/                  # Dépendances (Composer)
│
├── .env                     # Variables d'environnement
├── compose.yaml             # Docker Compose (dev)
├── Dockerfile               # Image Docker (prod)
├── composer.json            # Dépendances PHP
└── phpunit.dist.xml         # Configuration tests
```

## 4.2 Responsabilités des Composants

### Contrôleurs (`src/Controller/`)

| Contrôleur | Responsabilité | Routes Principales |
|------------|----------------|-------------------|
| `DefaultController` | Page d'accueil | `/` |
| `EventController` | CRUD événements public | `/events`, `/event/{id}` |
| `AdminController` | Administration | `/admin/*` |
| `SecurityController` | Authentification | `/login`, `/logout` |
| `RegistrationController` | Inscription utilisateur | `/register` |
| `HealthController` | Healthcheck API | `/health` |

### Entités (`src/Entity/`)

| Entité | Table | Description |
|--------|-------|-------------|
| `User` | `user` | Utilisateurs et authentification |
| `Event` | `event` | Événements humanitaires |
| `Category` | `category` | Catégories d'événements |
| `ToRegister` | `to_register` | Inscriptions (table pivot) |

### Repositories (`src/Repository/`)

```php
// Exemple : EventRepository.php
class EventRepository extends ServiceEntityRepository
{
    // Méthode personnalisée
    public function findUpcomingEvents(): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.dateStart > :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('e.dateStart', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
```

---

# 5. Modèle de Données

## 5.1 Diagramme Entité-Relation

```
┌─────────────────────────────────────────────────────────────────┐
│                           USER                                  │
├─────────────────────────────────────────────────────────────────┤
│ PK  id_user         INT AUTO_INCREMENT                         │
│     email           VARCHAR(180) UNIQUE NOT NULL               │
│     password        VARCHAR(255) NOT NULL                      │
│     firstName       VARCHAR(100) NOT NULL                      │
│     lastName        VARCHAR(100) NOT NULL                      │
│     roles           JSON NOT NULL                              │
│     creationUser    DATETIME NOT NULL                          │
└─────────────────────────────────────────────────────────────────┘
         │ 1
         │
         │ créé par (createdBy)
         │
         │ *
┌─────────────────────────────────────────────────────────────────┐
│                           EVENT                                 │
├─────────────────────────────────────────────────────────────────┤
│ PK  id_event        INT AUTO_INCREMENT                         │
│     title           VARCHAR(160) NOT NULL                      │
│     slug            VARCHAR(255) UNIQUE NOT NULL               │
│     description     TEXT NOT NULL                              │
│     image           VARCHAR(255) NULL                          │
│     dateStart       DATETIME NOT NULL                          │
│     dateEnd         DATETIME NOT NULL                          │
│     address         VARCHAR(160) NOT NULL                      │
│     postalCode      VARCHAR(10) NOT NULL                       │
│     city            VARCHAR(100) NOT NULL                      │
│     country         VARCHAR(100) NOT NULL                      │
│     organizer       VARCHAR(160) NOT NULL                      │
│     maxParticipants INT NULL                                   │
│     status          VARCHAR(20) DEFAULT 'active'               │
│     createdAt       DATETIME NOT NULL                          │
│     updatedAt       DATETIME NULL                              │
│ FK  id_user         INT NOT NULL                               │
│ FK  id_category     INT NULL                                   │
└─────────────────────────────────────────────────────────────────┘
         │ 1                                    │ *
         │                                      │
         │ appartient à (category)              │ catégorise
         │                                      │
         │ *                                    │ 1
┌─────────────────────────────────────────────────────────────────┐
│                         CATEGORY                                │
├─────────────────────────────────────────────────────────────────┤
│ PK  id_category     INT AUTO_INCREMENT                         │
│     name            VARCHAR(255) NOT NULL                      │
│     slug            VARCHAR(255) UNIQUE NOT NULL               │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                       TO_REGISTER                               │
│                    (Table d'association)                        │
├─────────────────────────────────────────────────────────────────┤
│ PK  id_registration INT AUTO_INCREMENT                         │
│     registeredAt    DATETIME NOT NULL                          │
│     status          VARCHAR(20) DEFAULT 'confirmed'            │
│     whatsappNumber  VARCHAR(20) NULL                           │
│     latitude        DECIMAL(10,7) NULL                         │
│     longitude       DECIMAL(10,7) NULL                         │
│     locationUpdatedAt DATETIME NULL                            │
│ FK  id_user         INT NOT NULL                               │
│ FK  id_event        INT NOT NULL (CASCADE DELETE)              │
│                                                                 │
│ UNIQUE (id_user, id_event) -- Un user ne peut s'inscrire 1x   │
└─────────────────────────────────────────────────────────────────┘
         ▲ *                           ▲ *
         │                             │
         │ inscrit                     │ inscrit
         │                             │
         │ 1                           │ 1
      USER                          EVENT
```

## 5.2 Relations Doctrine

### User → Event (OneToMany)
```php
// User.php
#[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'createdBy')]
private Collection $events;

// Event.php
#[ORM\ManyToOne(inversedBy: 'events')]
#[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user', nullable: false)]
private ?User $createdBy = null;
```

### Event → ToRegister (OneToMany avec CASCADE)
```php
// Event.php
#[ORM\OneToMany(targetEntity: ToRegister::class, mappedBy: 'event', cascade: ['remove'])]
private Collection $registrations;

// ToRegister.php
#[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'registrations')]
#[ORM\JoinColumn(name: 'id_event', referencedColumnName: 'id_event', nullable: false, onDelete: 'CASCADE')]
private ?Event $event = null;
```

## 5.3 Validations

```php
// Event.php - Exemples de contraintes
#[Assert\NotBlank(message: "Le titre ne peut pas être vide")]
#[Assert\Length(min: 5, max: 160)]
private ?string $title = null;

#[Assert\GreaterThan(value: "now", message: "La date doit être dans le futur")]
private ?\DateTimeInterface $dateStart = null;

#[Assert\GreaterThan(propertyPath: "dateStart", message: "Date fin > date début")]
private ?\DateTimeInterface $dateEnd = null;
```

---

# 6. Sécurité

## 6.1 Authentification

### Configuration (`config/packages/security.yaml`)

```yaml
security:
    # Hashage des mots de passe (auto = bcrypt ou argon2)
    password_hashers:
        App\Entity\User:
            algorithm: auto

    # Provider : comment charger les utilisateurs
    providers:
        app_user_provider:
            entity:
                class: App\Entity\User
                property: email  # Identifiant de connexion

    # Firewall principal
    firewalls:
        main:
            lazy: true
            provider: app_user_provider
            form_login:
                login_path: default_login
                check_path: default_login
                enable_csrf: true  # Protection CSRF
            logout:
                path: default_logout
                target: default_home  # Redirection après déconnexion
```

### Flux d'Authentification

```
1. Utilisateur → GET /login
2. SecurityController::login() → Affiche formulaire
3. Utilisateur soumet email + password
4. Symfony intercepte POST /login
5. UserProvider charge User par email
6. PasswordHasher vérifie le hash
7. ✅ Succès → Session créée → Redirection
   ❌ Échec → Retour formulaire avec erreur
```

## 6.2 Autorisation (RBAC)

### Rôles Définis

| Rôle | Description | Hérite de |
|------|-------------|-----------|
| `ROLE_USER` | Utilisateur standard | - |
| `ROLE_ADMIN` | Administrateur | `ROLE_USER` |

### Contrôle d'Accès

```yaml
# security.yaml
access_control:
    - { path: ^/admin, roles: ROLE_ADMIN }
    - { path: ^/login, roles: PUBLIC_ACCESS }
    - { path: ^/register, roles: PUBLIC_ACCESS }
```

### Vérification dans les Contrôleurs

```php
// Avec attribut (recommandé)
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController

// Dans une méthode
public function edit(Event $event): Response
{
    $this->denyAccessUnlessGranted('ROLE_ADMIN');
    // ...
}
```

## 6.3 Protections Implémentées

| Vulnérabilité | Protection | Implémentation |
|---------------|------------|----------------|
| **SQL Injection** | ORM Doctrine | Requêtes paramétrées automatiques |
| **XSS** | Twig autoescaping | `{{ variable }}` escape par défaut |
| **CSRF** | Tokens | `enable_csrf: true` + `csrf_token()` |
| **Password cracking** | Bcrypt/Argon2 | `algorithm: auto` |
| **Session hijacking** | Cookies sécurisés | Configuration prod |

### Exemple Protection CSRF

```html
<!-- templates/admin/event_form.html.twig -->
<form method="POST">
    <input type="hidden" name="_token" value="{{ csrf_token('delete' ~ event.id) }}">
    <!-- ... -->
</form>
```

```php
// AdminController.php
if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
    $entityManager->remove($event);
    // ...
}
```

---

# 7. Migrations

## 7.1 Qu'est-ce qu'une Migration ?

Une migration est un **script PHP** qui modifie le schéma de la base de données de manière versionnée et réversible.

```
État Initial    →    Migration 1    →    Migration 2    →    État Final
(pas de tables)      (crée user)         (ajoute event)      (schéma complet)
```

## 7.2 Fichiers de Migration

```
migrations/
├── Version20251010080244.php  # Création tables initiales
├── Version20251010150910.php  # Ajout colonnes
├── Version20251010151310.php  # Modifications
├── Version20251010151633.php  # Clés étrangères
└── Version20251010151827.php  # Ajout status to_register
```

## 7.3 Anatomie d'une Migration

```php
// Version20251010151827.php
final class Version20251010151827 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute la colonne status à to_register';
    }

    // Applique la modification
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE to_register ADD status VARCHAR(20) DEFAULT NULL');
    }

    // Annule la modification (rollback)
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE to_register DROP status');
    }
}
```

## 7.4 Commandes Essentielles

```bash
# Créer une migration depuis les différences entités/BDD
php bin/console doctrine:migrations:diff

# Voir le statut des migrations
php bin/console doctrine:migrations:status

# Exécuter les migrations non appliquées
php bin/console doctrine:migrations:migrate

# Annuler la dernière migration
php bin/console doctrine:migrations:migrate prev

# Recréer la BDD complète (dev uniquement)
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load --no-interaction
```

## 7.5 Table de Suivi

Doctrine crée une table `doctrine_migration_versions` :

| version | executed_at | execution_time |
|---------|-------------|----------------|
| DoctrineMigrations\Version20251010080244 | 2025-10-10 08:02:45 | 125 |
| DoctrineMigrations\Version20251010150910 | 2025-10-10 15:09:11 | 89 |
| ... | ... | ... |

---

# 8. Tests

## 8.1 Stratégie de Tests

```
┌─────────────────────────────────────────────────────────────────┐
│                    PYRAMIDE DES TESTS                           │
│                                                                 │
│                          /\                                     │
│                         /  \        Tests E2E (manuels)         │
│                        /    \       Interface utilisateur       │
│                       /──────\                                  │
│                      /        \     Tests d'Intégration         │
│                     / 7 tests  \    Controllers + BDD           │
│                    /────────────\                               │
│                   /              \   Tests Unitaires            │
│                  /    5 tests     \  Entités + Repositories     │
│                 /──────────────────\                            │
│                                                                 │
│  Plus on monte : Plus lent, plus coûteux, moins de tests       │
│  Plus on descend : Plus rapide, moins cher, plus de tests      │
└─────────────────────────────────────────────────────────────────┘
```

## 8.2 Tests Unitaires

**Objectif** : Tester la logique métier isolément, sans base de données.

```php
// tests/Unit/Entity/EventTest.php

class EventTest extends TestCase
{
    private Event $event;

    protected function setUp(): void
    {
        $this->event = new Event();
        $this->event->setDateStart(new \DateTime('+1 week'));
        $this->event->setDateEnd(new \DateTime('+1 week +2 hours'));
    }

    public function testIsUpcoming(): void
    {
        $this->assertTrue($this->event->isUpcoming());
    }

    public function testIsPast(): void
    {
        $this->event->setDateStart(new \DateTime('-1 week'));
        $this->event->setDateEnd(new \DateTime('-1 week +2 hours'));

        $this->assertTrue($this->event->isPast());
    }

    public function testIsFull(): void
    {
        $this->event->setMaxParticipants(null);
        $this->assertFalse($this->event->isFull()); // Illimité

        $this->event->setMaxParticipants(0);
        $this->assertTrue($this->event->isFull()); // 0 place = complet
    }

    public function testCanRegister(): void
    {
        $this->event->setMaxParticipants(50);
        $this->assertTrue($this->event->canRegister());
    }
}
```

## 8.3 Tests d'Intégration

**Objectif** : Tester les contrôleurs avec une vraie base de données.

```php
// tests/Integration/Controller/SecurityControllerTest.php

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
    }

    public function testLoginFormHasCsrfToken(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertSelectorExists('input[name="_csrf_token"]');
    }

    public function testAdminAccessIsProtected(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/');

        // Utilisateur non connecté → redirection vers login
        $this->assertResponseRedirects();
    }

    public function testSuccessfulLogin(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'admin@thecrosslove.com']);

        // Simule une connexion
        $client->loginUser($user);
        $client->request('GET', '/admin/');

        $this->assertResponseIsSuccessful();
    }
}
```

## 8.4 Exécution des Tests

```bash
# Tous les tests
php bin/phpunit

# Tests unitaires uniquement
php bin/phpunit tests/Unit/

# Tests d'intégration uniquement
php bin/phpunit tests/Integration/

# Un fichier spécifique
php bin/phpunit tests/Unit/Entity/EventTest.php

# Avec couverture de code
php bin/phpunit --coverage-html var/coverage
```

## 8.5 Configuration PHPUnit

```xml
<!-- phpunit.dist.xml -->
<phpunit bootstrap="tests/bootstrap.php">
    <php>
        <server name="APP_ENV" value="test" force="true"/>
    </php>

    <testsuites>
        <testsuite name="Project Test Suite">
            <directory>tests</directory>
        </testsuite>
    </testsuites>

    <source>
        <include>
            <directory>src</directory>
        </include>
    </source>
</phpunit>
```

---

# 9. Configuration Docker & phpMyAdmin

## 9.1 Architecture Docker

```
┌─────────────────────────────────────────────────────────────────┐
│                     DOCKER COMPOSE                              │
│                     (compose.yaml)                              │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐         │
│  │   database   │  │  phpmyadmin  │  │  mailcatcher │         │
│  │   MySQL 8.0  │  │     5.2      │  │              │         │
│  │   :3306      │  │   :8080      │  │  :1025/:1080 │         │
│  └──────────────┘  └──────────────┘  └──────────────┘         │
│         │                  │                                    │
│         └──────────────────┘                                    │
│              PMA_HOST: database                                 │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## 9.2 Fichier compose.yaml Expliqué

```yaml
services:
  # SERVICE 1 : Base de données MySQL
  database:
    image: mysql:8.0                    # Image officielle MySQL 8.0
    environment:
      MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD:-root}  # Mot de passe root
      MYSQL_DATABASE: ${MYSQL_DATABASE:-thecrosslove}    # BDD créée au démarrage
      MYSQL_USER: ${MYSQL_USER:-thecrosslove}            # Utilisateur applicatif
      MYSQL_PASSWORD: ${MYSQL_PASSWORD:-thecrosslove}    # Son mot de passe
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
      timeout: 5s
      retries: 5
      start_period: 60s                 # Attend 60s avant les checks
    ports:
      - "3306:3306"                      # Expose sur le port 3306
    volumes:
      - database_data:/var/lib/mysql    # Persistance des données

  # SERVICE 2 : phpMyAdmin
  phpmyadmin:
    image: phpmyadmin:5.2
    environment:
      PMA_HOST: database                # Nom du service MySQL
      PMA_PORT: 3306
      PMA_USER: root
      PMA_PASSWORD: ${MYSQL_ROOT_PASSWORD:-root}
      UPLOAD_LIMIT: 100M
    ports:
      - "8080:80"                        # Accessible sur http://localhost:8080
    depends_on:
      database:
        condition: service_healthy       # Attend que MySQL soit prêt

  # SERVICE 3 : Serveur mail de test
  mailcatcher:
    image: schickling/mailcatcher
    ports:
      - "1025:1025"                      # SMTP
      - "1080:1080"                      # Interface web

volumes:
  database_data:                         # Volume nommé pour persistance
```

## 9.3 Commandes Docker

```bash
# Démarrer tous les services
docker compose up -d

# Voir les logs
docker compose logs -f database

# Arrêter les services
docker compose down

# Arrêter et supprimer les données
docker compose down -v

# Vérifier l'état
docker compose ps

# Accéder au shell MySQL
docker compose exec database mysql -u root -proot thecrosslove
```

## 9.4 Configuration .env pour Docker

```env
# .env - Pour utiliser avec Docker
DATABASE_URL="mysql://root:root@127.0.0.1:3306/thecrosslove?serverVersion=8.0&charset=utf8mb4"
```

## 9.5 Configuration .env pour WAMP/MariaDB Local

```env
# .env - Pour utiliser avec WAMP (MariaDB port 3307)
DATABASE_URL="mysql://root:root@127.0.0.1:3307/thecrosslove?serverVersion=11.5.2-MariaDB&charset=utf8mb4"
```

## 9.6 Accès phpMyAdmin

1. Démarrer Docker : `docker compose up -d`
2. Attendre ~60 secondes (healthcheck)
3. Ouvrir : `http://localhost:8080`
4. Connexion automatique (configurée dans compose.yaml)

---

# 10. Déploiement

## 10.1 Environnements

```
┌─────────────────────────────────────────────────────────────────┐
│                    PIPELINE DE DÉPLOIEMENT                      │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   LOCAL          →     STAGING        →     PRODUCTION         │
│   (develop)            (develop)             (main)             │
│                                                                 │
│   WAMP/Docker          Serveur test          Serveur prod       │
│   .env.dev             .env.staging          .env (secrets)     │
│   APP_DEBUG=1          APP_DEBUG=0           APP_DEBUG=0        │
│   Tests manuels        Tests auto            Monitoring         │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## 10.2 Dockerfile Multi-Stage

```dockerfile
# STAGE 1 : Build (compilation)
FROM php:8.2-apache AS builder

# Installation des dépendances de build
RUN apt-get update && apt-get install -y git curl libpng-dev...
RUN docker-php-ext-install pdo pdo_mysql intl opcache

# Installation Composer et dépendances
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# Copie du code et build
COPY . .
RUN php bin/console cache:warmup --env=prod

# STAGE 2 : Production (image finale légère)
FROM php:8.2-apache

# Seulement les extensions nécessaires au runtime
RUN docker-php-ext-install pdo pdo_mysql intl opcache

# Copie depuis le builder (pas de Composer, pas de git)
COPY --from=builder /var/www/html /var/www/html

# Configuration Apache et PHP pour production
RUN a2enmod rewrite headers
```

**Avantages Multi-Stage** :
- Image finale plus petite (pas d'outils de build)
- Plus sécurisée (pas de git, composer en prod)
- Build reproductible

## 10.3 CI/CD avec GitHub Actions

```yaml
# .github/ci.yml (simplifié)
name: CI/CD Pipeline

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main, develop]

jobs:
  # JOB 1 : Qualité du code
  code-quality:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Validate composer
        run: composer validate
      - name: Security audit
        run: composer audit

  # JOB 2 : Tests
  tests:
    runs-on: ubuntu-latest
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: thecrosslove_test
    steps:
      - uses: actions/checkout@v4
      - name: Install dependencies
        run: composer install
      - name: Run migrations
        run: php bin/console doctrine:migrations:migrate --no-interaction --env=test
      - name: Run tests
        run: php bin/phpunit --coverage-clover coverage.xml

  # JOB 3 : Build Docker
  build:
    needs: [code-quality, tests]
    runs-on: ubuntu-latest
    steps:
      - name: Build and push Docker image
        run: |
          docker build -t thecrosslove:${{ github.sha }} .
          docker push thecrosslove:${{ github.sha }}

  # JOB 4 : Déploiement production (seulement sur main)
  deploy-production:
    needs: build
    if: github.ref == 'refs/heads/main'
    runs-on: ubuntu-latest
    steps:
      - name: Deploy to production
        run: |
          ssh user@server "docker pull thecrosslove:${{ github.sha }}"
          ssh user@server "docker compose up -d"
          ssh user@server "docker exec app php bin/console doctrine:migrations:migrate --no-interaction"
```

## 10.4 Variables d'Environnement Production

```env
# NE JAMAIS COMMITER - Configurer sur le serveur
APP_ENV=prod
APP_DEBUG=0
APP_SECRET=une_clé_aléatoire_très_longue_et_sécurisée
DATABASE_URL="mysql://user:password@db.server.com:3306/thecrosslove_prod"
MAILER_DSN=smtp://user:pass@smtp.server.com:587
```

---

# 11. Questions Types du Jury

## 11.1 Architecture & Conception

**Q : Expliquez le pattern MVC et comment Symfony l'implémente.**

> Le MVC sépare l'application en trois couches :
> - **Model** : Les entités Doctrine (`src/Entity/`) qui représentent les données
> - **View** : Les templates Twig (`templates/`) qui génèrent le HTML
> - **Controller** : Les contrôleurs (`src/Controller/`) qui coordonnent
>
> Symfony utilise un **Front Controller** (`public/index.php`) qui reçoit toutes les requêtes, le Kernel les route vers le bon contrôleur.

**Q : Qu'est-ce que l'injection de dépendances ?**

> C'est un pattern où les objets reçoivent leurs dépendances plutôt que de les créer. Symfony utilise un **Service Container** qui instancie et injecte automatiquement les services. Avantages : testabilité, découplage, flexibilité.

**Q : Pourquoi avoir choisi Doctrine plutôt qu'Eloquent ?**

> Doctrine utilise le pattern **Data Mapper** où les entités sont des objets PHP purs (POPO) et l'EntityManager gère la persistance. Eloquent utilise **Active Record** où l'entité se sauvegarde elle-même. Data Mapper offre une meilleure séparation des responsabilités et est plus adapté aux architectures complexes.

## 11.2 Sécurité

**Q : Comment protégez-vous contre l'injection SQL ?**

> Doctrine ORM utilise des **requêtes paramétrées** automatiquement. Je n'écris jamais de SQL brut avec des variables concaténées.
> ```php
> // ✅ Sécurisé
> $qb->where('e.status = :status')->setParameter('status', $status);
>
> // ❌ Vulnérable (jamais fait)
> $qb->where("e.status = '$status'");
> ```

**Q : Expliquez la protection CSRF.**

> CSRF (Cross-Site Request Forgery) : un attaquant fait exécuter une action à un utilisateur connecté via un lien malveillant. Protection : un **token unique** généré côté serveur, inclus dans chaque formulaire, vérifié à la soumission. Symfony génère ces tokens via `csrf_token()` dans Twig.

**Q : Comment sont stockés les mots de passe ?**

> Les mots de passe sont **hashés** avec bcrypt ou argon2 (choix automatique via `algorithm: auto`). Le hash est irréversible. À la connexion, on hashe le mot de passe saisi et on compare les hashs.

## 11.3 Base de Données

**Q : Qu'est-ce qu'une migration et pourquoi c'est important ?**

> Une migration est un script de modification du schéma de BDD, versionné dans Git. Importance :
> - **Reproductibilité** : même schéma sur tous les environnements
> - **Historique** : on peut voir l'évolution du schéma
> - **Rollback** : on peut annuler une modification
> - **Collaboration** : chaque développeur applique les mêmes changements

**Q : Expliquez les relations entre vos entités.**

> - `User` **1-N** `Event` : un utilisateur crée plusieurs événements
> - `Event` **N-1** `Category` : un événement appartient à une catégorie
> - `User` **N-N** `Event` via `ToRegister` : table pivot pour les inscriptions avec attributs supplémentaires (date, statut, localisation)

## 11.4 Tests

**Q : Quelle est la différence entre test unitaire et test d'intégration ?**

> **Test unitaire** : teste une classe/méthode isolément, sans base de données, très rapide.
> ```php
> public function testIsUpcoming() {
>     $event = new Event();
>     $event->setDateStart(new \DateTime('+1 week'));
>     $this->assertTrue($event->isUpcoming());
> }
> ```
>
> **Test d'intégration** : teste plusieurs composants ensemble, avec base de données réelle.
> ```php
> public function testLoginPage() {
>     $client = static::createClient();
>     $client->request('GET', '/login');
>     $this->assertResponseIsSuccessful();
> }
> ```

## 11.5 Déploiement

**Q : Expliquez votre pipeline CI/CD.**

> 1. **Push** sur GitHub déclenche le workflow
> 2. **Qualité** : validation composer, audit sécurité
> 3. **Tests** : exécution PHPUnit avec couverture
> 4. **Build** : création image Docker multi-stage
> 5. **Deploy staging** (branch develop) : déploiement automatique
> 6. **Deploy production** (branch main) : backup BDD, maintenance mode, migration, healthcheck

**Q : Pourquoi Docker ?**

> - **Reproductibilité** : même environnement partout
> - **Isolation** : pas de conflits de dépendances
> - **Portabilité** : fonctionne sur tout OS
> - **Scalabilité** : facile à orchestrer (Kubernetes)

---

# Conclusion

Ce document couvre les fondamentaux théoriques et pratiques du projet TheCrossLove. Pour la défense :

1. **Maîtrisez le vocabulaire** : MVC, ORM, RBAC, CSRF, CI/CD
2. **Comprenez le flux** : requête → routeur → contrôleur → réponse
3. **Justifiez vos choix** : chaque technologie a une raison
4. **Démontrez la sécurité** : validation, hashage, tokens
5. **Expliquez les tests** : pyramide, unitaire vs intégration
