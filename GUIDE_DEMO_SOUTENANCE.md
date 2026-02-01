# 🎯 Guide de Démonstration - Soutenance TheCrossLove

## Préparation (30 min avant)

### Commande unique de setup
```bash
# Windows
scripts\demo-setup.bat

# Linux/Mac
./scripts/demo-setup.sh
```

### Ou manuellement
```bash
docker-compose up -d
php bin/console doctrine:fixtures:load --no-interaction
php bin/console cache:clear
symfony server:start
```

### Vérifications
- [ ] Ouvrir http://localhost:8000 → Page d'accueil visible
- [ ] Ouvrir http://localhost:8000/health → Status "healthy"
- [ ] Préparer 2 onglets navigateur (un pour user, un pour admin)

---

## 🎬 Scénario de Démonstration (15-20 min)

### ACTE 1: Découverte publique (3 min)

**Narration:** *"TheCrossLove est une plateforme de gestion d'événements humanitaires, concentrée sur des missions au Sénégal et en RDC."*

| Étape | Action | Ce que vous montrez |
|-------|--------|---------------------|
| 1.1 | Aller à `/` | Page d'accueil avec 11 événements |
| 1.2 | Scroller | Design responsive, images événements |
| 1.3 | Cliquer filtre "Humanitaire" | Filtrage instantané côté client |
| 1.4 | Taper "Bukavu" dans recherche | Recherche en temps réel |
| 1.5 | Cliquer sur un événement | Page détail complète |

**Point clé à mentionner:** *"Filtrage et recherche instantanés en JavaScript, sans rechargement de page."*

---

### ACTE 2: Parcours utilisateur (5 min)

**Narration:** *"Voyons comment un utilisateur peut s'inscrire à un événement."*

| Étape | Action | URL/Bouton |
|-------|--------|------------|
| 2.1 | Cliquer "Connexion" | `/login` |
| 2.2 | Se connecter | `john.doe@example.com` / `password123` |
| 2.3 | Observer la navbar | Nom utilisateur affiché |
| 2.4 | Aller sur un événement | Cliquer sur carte événement |
| 2.5 | Cliquer "S'inscrire" | Bouton vert visible |
| 2.6 | Observer confirmation | Message de succès |
| 2.7 | Aller "Mes inscriptions" | `/my-registrations` |
| 2.8 | Montrer la liste | Événements inscrits visibles |
| 2.9 | Se désinscrire | Bouton "Annuler" sur un événement |

**Points clés:**
- *"Authentification sécurisée avec CSRF token"*
- *"Validation des places disponibles avant inscription"*
- *"L'utilisateur peut gérer ses inscriptions"*

---

### ACTE 3: Administration (7 min)

**Narration:** *"L'administrateur a accès à un dashboard complet."*

| Étape | Action | URL/Bouton |
|-------|--------|------------|
| 3.1 | Déconnexion | Bouton déconnexion |
| 3.2 | Connexion admin | `admin@thecrosslove.com` / `admin123` |
| 3.3 | Dashboard | `/admin/` |
| 3.4 | Montrer statistiques | Nombre events, inscriptions, taux |
| 3.5 | Cliquer "Événements" | `/admin/events` |
| 3.6 | Créer un événement | Bouton "Nouvel événement" |
| 3.7 | Remplir le formulaire | Titre, description, dates, image |
| 3.8 | Sauvegarder | Redirection vers liste |
| 3.9 | Voir participants | Icône "participants" sur un event |
| 3.10 | Montrer la liste | Tableau des inscrits |

**Points clés:**
- *"Dashboard avec statistiques temps réel via Doctrine"*
- *"CRUD complet avec validation des formulaires"*
- *"Upload d'images sécurisé"*
- *"Contrôle d'accès ROLE_ADMIN"*

---

### ACTE 4: Points techniques (5 min)

**Narration:** *"Quelques points techniques importants..."*

| Sujet | Action | Explication |
|-------|--------|-------------|
| Architecture | Montrer structure fichiers | `src/Entity`, `Controller`, `templates` |
| Sécurité | Montrer `security.yaml` | Firewalls, access_control |
| Tests | Lancer `php bin/phpunit` | Couverture tests |
| Responsive | DevTools → Mobile | Adaptation écran |
| Health | Aller `/health` | Monitoring production |

---

## 📝 Notes pour les explications orales

### Pourquoi Symfony ?
> "Framework PHP mature, utilisé par Blablacar, Spotify. Sécurité intégrée, Doctrine ORM puissant, excellente documentation."

### Sécurité implémentée
> "CSRF protection sur tous les formulaires, mots de passe hashés en bcrypt, firewall avec access_control par rôle, validation côté serveur."

### Scalabilité
> "Architecture Docker prête pour production avec Redis pour sessions, Traefik comme reverse proxy, health checks pour monitoring."

### Tests
> "Tests unitaires sur les entités, tests d'intégration sur les controllers. DatabaseTestTrait pour isolation."

---

## ⚠️ En cas de problème

### "Page blanche / Erreur 500"
```bash
php bin/console cache:clear
tail -f var/log/dev.log
```

### "Connection refused MySQL"
```bash
docker-compose down
docker-compose up -d
# Attendre 10 secondes
```

### "Login ne fonctionne pas"
```bash
php bin/console doctrine:fixtures:load --no-interaction
```

### "Images non affichées"
```bash
chmod -R 755 public/uploads/
```

---

## 🎯 Comptes de démo (à mémoriser)

| Rôle | Email | Password |
|------|-------|----------|
| **Admin** | `admin@thecrosslove.com` | `admin123` |
| **User** | `john.doe@example.com` | `password123` |

---

## ✅ Checklist finale

- [ ] Docker démarré
- [ ] Fixtures chargées (11 events, 9 users)
- [ ] 2 onglets navigateur prêts
- [ ] Ce guide imprimé ou sur un écran secondaire
- [ ] Connexion internet stable (pour les images si CDN)

---

**Bonne soutenance ! 🚀**
