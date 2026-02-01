# Plan Soutenance Blanche - TheCrossLove

## Date: Demain
## Objectif: Démonstration live fonctionnelle

---

## CHECKLIST PRÉ-SOUTENANCE (30 min avant)

```bash
# 1. Démarrer les services Docker
docker-compose up -d

# 2. Vérifier la base de données
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction

# 3. Charger les données de démo
php bin/console doctrine:fixtures:load --no-interaction

# 4. Vider le cache
php bin/console cache:clear

# 5. Démarrer le serveur
symfony server:start
# ou: php -S localhost:8000 -t public/

# 6. Vérifier que tout fonctionne
curl http://localhost:8000/health
```

---

## COMPTES DE DÉMONSTRATION

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| **Admin** | `admin@thecrosslove.com` | `admin123` |
| **User 1** | `john.doe@example.com` | `password123` |
| **User 2** | `marie.dupont@example.com` | `password123` |

---

## SCÉNARIO DE DÉMONSTRATION (15-20 min)

### Acte 1: Découverte Publique (3 min)
1. **Page d'accueil** → Montrer les 11 événements
2. **Filtrer** par catégorie "Humanitaire"
3. **Rechercher** "Sénégal" → Montrer le filtre intelligent
4. **Cliquer** sur un événement → Détails complets

### Acte 2: Parcours Utilisateur (5 min)
1. **Inscription** → Créer un nouveau compte (ou login `john.doe@example.com`)
2. **S'inscrire** à un événement
3. **Voir "Mes inscriptions"** → Liste personnalisée
4. **Se désinscrire** d'un événement

### Acte 3: Administration (7 min)
1. **Login admin** → `admin@thecrosslove.com` / `admin123`
2. **Dashboard** → Montrer les statistiques en temps réel
   - Nombre d'événements
   - Total inscriptions
   - Taux de remplissage
3. **Créer un événement** → Formulaire complet avec upload image
4. **Voir participants** → Liste des inscrits à un événement
5. **Modifier un événement** → Changer la date ou le nombre de places

### Acte 4: Points Techniques (5 min)
1. **Architecture MVC** → Expliquer Entity/Controller/Template
2. **Sécurité** → Rôles ROLE_USER / ROLE_ADMIN
3. **Responsive** → Montrer sur mobile (DevTools)
4. **Health check** → `/health` endpoint

---

## POINTS FORTS À METTRE EN AVANT

### Fonctionnalités
- ✅ Authentification sécurisée (CSRF, bcrypt)
- ✅ Gestion complète des événements (CRUD)
- ✅ Inscription/désinscription avec validations
- ✅ Dashboard admin avec statistiques temps réel
- ✅ Filtrage et recherche côté client
- ✅ Upload d'images pour événements
- ✅ Design responsive

### Technique
- ✅ Symfony 7.3 + PHP 8.2
- ✅ Doctrine ORM avec migrations
- ✅ Tests unitaires et d'intégration
- ✅ Docker pour développement et production
- ✅ CI/CD GitHub Actions
- ✅ Health checks pour monitoring

### Contexte Métier
- ✅ Thématique humanitaire (Sénégal/RDC)
- ✅ Données réalistes (événements, participants)
- ✅ Cas d'usage concrets

---

## EN CAS DE PROBLÈME

### Base de données ne répond pas
```bash
docker-compose down && docker-compose up -d
# Attendre 30 secondes puis réessayer
```

### Erreur 500
```bash
php bin/console cache:clear
tail -f var/log/dev.log
```

### Images ne s'affichent pas
```bash
chmod -R 755 public/uploads/
```

### Login ne fonctionne pas
```bash
# Recharger les fixtures
php bin/console doctrine:fixtures:load --no-interaction
```

---

## QUESTIONS PROBABLES DU JURY

1. **"Pourquoi Symfony ?"**
   → Framework robuste, sécurisé, bien documenté, utilisé en entreprise

2. **"Comment gérez-vous la sécurité ?"**
   → CSRF tokens, bcrypt pour passwords, voters pour autorisations, firewall config

3. **"Comment scaleriez-vous ?"**
   → Docker + Redis (déjà configuré prod), load balancer Traefik

4. **"Et les tests ?"**
   → Tests unitaires (Entity) + Tests d'intégration (Controllers)
   → `php bin/phpunit` pour démonstration

5. **"Quelles améliorations futures ?"**
   → Notifications email, paiement en ligne, calendrier iCal, API REST

---

## TIMING SUGGÉRÉ

| Phase | Durée | Contenu |
|-------|-------|---------|
| Introduction | 2 min | Contexte projet, problématique |
| Démo live | 15 min | Scénarios 1-4 ci-dessus |
| Architecture | 5 min | Schéma technique, choix |
| Questions | 8 min | Réponses au jury |
| **Total** | **30 min** | |

---

## BACKUP PLAN

Si le serveur local ne fonctionne pas:
1. Avoir des **screenshots** des pages clés
2. Montrer le **code source** (Entity, Controller, Template)
3. Montrer les **tests qui passent**
4. Expliquer l'**architecture** avec diagrammes

---

*Fichier généré pour la soutenance blanche de TheCrossLove*
