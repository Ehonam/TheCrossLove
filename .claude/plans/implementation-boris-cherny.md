# Plan d'Implémentation des Recommandations de Boris Cherny pour TheCrossLove

**Date de création:** 2026-02-01
**Statut:** À implémenter

## Contexte

Application des 10 tips de Boris Cherny (créateur de Claude Code) au projet TheCrossLove - plateforme Symfony 7.3 de gestion d'événements humanitaires.

**Source:** `.claude/plans/boris-cherny-tips-adaptation.md`

---

## État Actuel

### Structure `.claude/` existante
```
.claude/
├── plans/
│   ├── boris-cherny-tips-adaptation.md  ✅
│   ├── soutenance-demo-plan.md          ✅
│   └── implementation-boris-cherny.md   ✅ (ce fichier)
└── settings.local.json                   ✅
```

### Ce qui manque
- `skills/` - Aucun skill personnalisé
- `notes/` - Aucune note par domaine
- `learning/` - Pas de spaced repetition

---

## Plan d'Implémentation

### Phase 1: Structure de Base (Priorité Haute)

#### 1.1 Créer les dossiers manquants
```
.claude/
├── skills/      # À créer
├── notes/       # À créer
└── learning/    # À créer
```

#### 1.2 Créer les notes par domaine (4 fichiers)

**`.claude/notes/events.md`**
- Règles Entity Event (dates, statuts, capacité)
- Méthodes métier (isUpcoming, isFull, canRegister)
- Relations avec Category et ToRegister

**`.claude/notes/registrations.md`**
- Logique ToRegister (user + event unique)
- Statuts d'inscription (confirmed, cancelled)
- Gestion WhatsApp et GPS

**`.claude/notes/security.md`**
- Patterns auth (ROLE_USER, ROLE_ADMIN)
- CSRF protection
- Access control par route

**`.claude/notes/testing.md`**
- Conventions tests unitaires vs intégration
- DatabaseTestTrait
- Patterns de test existants

### Phase 2: Skills Essentiels (5 skills)

#### 2.1 `/test-feature` - Workflow de test
```markdown
# Skill: Test Feature
Lance le workflow complet de test:
1. Tests unitaires: php bin/phpunit tests/Unit/
2. Tests d'intégration: php bin/phpunit tests/Integration/
3. Lint container: php bin/console lint:container
4. Schema validation: php bin/console doctrine:schema:validate
```

#### 2.2 `/deploy-check` - Vérification pré-déploiement
```markdown
# Skill: Deploy Check
Vérifie avant déploiement:
1. composer audit (sécurité)
2. php bin/phpunit (tous les tests)
3. php bin/console doctrine:migrations:status (migrations pending)
4. Variables d'environnement requises
```

#### 2.3 `/techdebt` - Analyse dette technique
```markdown
# Skill: Tech Debt Analysis
Analyse le codebase pour:
- Code dupliqué dans les controllers
- Méthodes de repository similaires
- Templates Twig redondants
- Patterns non respectés
```

#### 2.4 `/symfony-entity` - Génération d'entité
```markdown
# Skill: Symfony Entity
Crée une entité avec les conventions TheCrossLove:
- ID nommé id_<entity>
- Timestamps created_at/updated_at
- Repository associé dans src/Repository/
- Tests unitaires skeleton dans tests/Unit/Entity/
```

#### 2.5 `/db-stats` - Statistiques base de données
```markdown
# Skill: Database Stats
Requêtes SQL utiles:
- SELECT COUNT(*) FROM event (total événements)
- SELECT COUNT(*) FROM to_register (total inscrits)
- Taux de remplissage moyen
- Top utilisateurs par inscriptions
```

### Phase 3: Amélioration CLAUDE.md

#### 3.1 Ajouter section "Learning from Errors"
```markdown
## Learning from Errors

Après chaque bug fix, documenter ici pour éviter de répéter l'erreur.

### Erreurs Doctrine
- ...

### Erreurs Formulaires
- ...

### Erreurs Sécurité
- ...
```

#### 3.2 Enrichir "Common Mistakes"
- Ajouter les erreurs Doctrine spécifiques découvertes
- Patterns de formulaires à éviter
- Pièges Symfony courants

### Phase 4: Skills Avancés (Bonus)

#### 4.1 `/event-workflow` - Création complète d'un event
```markdown
# Skill: Event Workflow
Workflow end-to-end:
1. Créer Entity avec relations
2. Créer FormType
3. Créer Controller avec routes
4. Créer Templates Twig
5. Créer Tests unitaires et intégration
```

#### 4.2 Diagrammes ASCII de l'architecture
```
┌──────────┐     ┌────────────┐     ┌──────────┐
│   User   │────<│ ToRegister │>────│  Event   │
└──────────┘     └────────────┘     └──────────┘
                                          │
                                    ┌──────────┐
                                    │ Category │
                                    └──────────┘
```

---

## Fichiers à Créer/Modifier

### Nouveaux fichiers (11 fichiers)

| Fichier | Description |
|---------|-------------|
| `.claude/skills/test-feature.md` | Skill workflow de test |
| `.claude/skills/deploy-check.md` | Skill vérification déploiement |
| `.claude/skills/techdebt.md` | Skill analyse dette technique |
| `.claude/skills/symfony-entity.md` | Skill génération entité |
| `.claude/skills/db-stats.md` | Skill statistiques SQL |
| `.claude/skills/event-workflow.md` | Skill workflow complet |
| `.claude/notes/events.md` | Notes domaine événements |
| `.claude/notes/registrations.md` | Notes domaine inscriptions |
| `.claude/notes/security.md` | Notes domaine sécurité |
| `.claude/notes/testing.md` | Notes domaine tests |
| `.claude/learning/gaps.md` | Spaced repetition topics |

### Fichiers à modifier (1 fichier)

| Fichier | Modification |
|---------|-------------|
| `CLAUDE.md` | Ajouter section "Learning from Errors" |

---

## Commande pour Reprendre

Pour reprendre l'implémentation plus tard, utiliser cette commande:

```
Implémenter le plan d'intégration des recommandations de Boris Cherny
selon le fichier .claude/plans/implementation-boris-cherny.md
```

---

## Vérification Finale

Après implémentation complète, vérifier:

1. **Structure créée**
   ```bash
   ls -la .claude/skills/
   ls -la .claude/notes/
   ls -la .claude/learning/
   ```

2. **Skills fonctionnels**
   - Tester chaque skill avec `/skill-name`

3. **CLAUDE.md mis à jour**
   - Section "Learning from Errors" présente

---

## Ordre d'Exécution Recommandé

1. ☐ Créer dossiers `skills/`, `notes/`, `learning/`
2. ☐ Créer les 4 fichiers de notes
3. ☐ Créer les 5 skills essentiels
4. ☐ Modifier CLAUDE.md
5. ☐ Créer le skill event-workflow (bonus)
6. ☐ Créer learning/gaps.md
