# Plan d'Adaptation des Tips de Boris Cherny pour TheCrossLove

## Contexte

Application des 10 tips de Boris Cherny (créateur de Claude Code) au projet TheCrossLove - une plateforme Symfony 7.3 de gestion d'événements.

---

## Tip 1: Invest in CLAUDE.md / docs

### Etat actuel
- CLAUDE.md existe avec une bonne base (architecture, commandes, conventions)
- Manque: règles d'erreurs communes, patterns spécifiques Symfony

### Actions proposées

1. **Enrichir CLAUDE.md avec les erreurs récurrentes**
   ```markdown
   ## Common Mistakes to Avoid
   - Ne jamais oublier `cascade: ['persist']` sur les relations OneToMany
   - Toujours utiliser `->getResult()` vs `->getOneOrNullResult()` selon le contexte
   - Vérifier les contraintes d'unicité avant INSERT (ToRegister: user+event)
   ```

2. **Créer un dossier `.claude/notes/` par domaine**
   ```
   .claude/
   ├── notes/
   │   ├── events.md          # Règles spécifiques Event entity
   │   ├── registrations.md   # Logique d'inscription
   │   ├── security.md        # Patterns auth/access control
   │   └── testing.md         # Conventions de test
   └── skills/                # Tip 2
   ```

3. **Ajouter une section "Learning from errors"**
   - Après chaque bug fix, demander: "Update CLAUDE.md so you don't make that mistake again"

---

## Tip 2: Create your own skills

### Skills à créer pour TheCrossLove

1. **`/symfony-entity`** - Génération d'entité avec conventions projet
   ```yaml
   # .claude/skills/symfony-entity.md
   Crée une entité Doctrine avec:
   - ID nommé id_<entity>
   - Timestamps created_at/updated_at
   - Repository associé
   - Tests unitaires skeleton
   ```

2. **`/test-feature`** - Workflow complet de test
   ```yaml
   # .claude/skills/test-feature.md
   1. Crée le test d'intégration
   2. Lance les tests
   3. Vérifie la couverture
   4. Propose des edge cases
   ```

3. **`/deploy-check`** - Vérification pré-déploiement
   ```yaml
   # .claude/skills/deploy-check.md
   1. composer audit
   2. php bin/phpunit
   3. Vérifier les migrations pending
   4. Vérifier les variables d'environnement
   ```

4. **`/techdebt`** - Nettoyage de code dupliqué
   ```yaml
   # .claude/skills/techdebt.md
   Analyse le codebase pour:
   - Code dupliqué dans les controllers
   - Méthodes de repository similaires
   - Templates Twig redondants
   ```

5. **`/event-workflow`** - Création complète d'un event
   ```yaml
   # .claude/skills/event-workflow.md
   Workflow end-to-end:
   Entity → Form → Controller → Template → Tests
   ```

---

## Tip 3: Verification loops / feedback

### Implémentation pour TheCrossLove

1. **Tests automatiques après chaque changement**
   ```
   Après chaque modification de code:
   → php bin/phpunit tests/Unit/
   → Si entity modifiée: tests Integration aussi
   ```

2. **Validation Symfony**
   ```
   Après modification config:
   → php bin/console lint:container
   → php bin/console lint:twig templates/
   → php bin/console lint:yaml config/
   ```

3. **Browser MCP pour UI**
   - Tester les formulaires d'inscription
   - Vérifier le rendu des événements
   - Valider les redirections après actions

4. **Checklist de vérification**
   ```markdown
   ## Verification Checklist (ajouter à CLAUDE.md)
   - [ ] Tests passent: `php bin/phpunit`
   - [ ] Pas d'erreur PHP: `php -l src/**/*.php`
   - [ ] Container valide: `php bin/console lint:container`
   - [ ] Migrations sync: `php bin/console doctrine:schema:validate`
   ```

---

## Tip 4: Run parallel instances

### Scénarios parallèles pour TheCrossLove

1. **Feature development**
   - Instance 1: Développe la feature
   - Instance 2: Écrit les tests
   - Instance 3: Met à jour la documentation

2. **Bug investigation**
   - Instance 1: Analyse les logs
   - Instance 2: Reproduit le bug en test
   - Instance 3: Cherche des patterns similaires

3. **Refactoring**
   - Instance 1: Refactor controllers
   - Instance 2: Refactor repositories
   - Instance 3: Update templates

---

## Tip 5: Slash commands & custom workflows

### Commands spécifiques TheCrossLove

1. **`/new-controller`**
   ```
   Crée un controller Symfony avec:
   - Route annotations
   - Security attributes
   - Test d'intégration associé
   ```

2. **`/migration-safe`**
   ```
   Génère une migration Doctrine en:
   1. Comparant le schéma
   2. Vérifiant la réversibilité
   3. Testant sur DB de test
   ```

3. **`/sync-context`**
   ```
   Synchronise le contexte depuis:
   - GitHub issues récentes
   - Derniers commits
   - PRs en review
   ```

4. **`/fixture-gen`**
   ```
   Génère des fixtures Doctrine pour:
   - Users (admin, regular)
   - Events (upcoming, past, full)
   - Registrations variées
   ```

---

## Tip 6: Analytics & data queries (MySQL)

### Requêtes utiles pour TheCrossLove

1. **Skill `/db-stats`**
   ```sql
   -- Statistiques événements
   SELECT
     COUNT(*) as total_events,
     SUM(CASE WHEN date > NOW() THEN 1 ELSE 0 END) as upcoming,
     AVG(max_participants) as avg_capacity
   FROM event;
   ```

2. **Skill `/user-activity`**
   ```sql
   -- Activité utilisateurs
   SELECT u.email, COUNT(r.id_to_register) as registrations
   FROM user u
   LEFT JOIN to_register r ON u.id_user = r.user_id
   GROUP BY u.id_user
   ORDER BY registrations DESC;
   ```

3. **Intégration CLI MySQL**
   ```bash
   # Ajouter à CLAUDE.md
   mysql -u root -p thecrosslove -e "QUERY"
   # Ou via Doctrine:
   php bin/console dbal:run-sql "QUERY"
   ```

---

## Tip 7: Explanatory modes

### Configuration pour TheCrossLove

Activer dans `/config`:
```
output_style: explanatory
```

Pour chaque changement, Claude expliquera:
- **Pourquoi** cette approche Symfony
- **Alternatives** considérées
- **Impact** sur les autres composants

---

## Tip 8: Visual explanations (HTML)

### Use cases TheCrossLove

1. **Architecture diagram** - Présentation HTML des entités et relations
2. **Flow d'inscription** - Visualisation du parcours utilisateur
3. **Security flow** - Diagramme de l'authentification
4. **Event lifecycle** - États d'un événement (draft → published → past)

Commande: "Generate an HTML presentation explaining the Event registration flow"

---

## Tip 9: ASCII diagrams

### Diagrammes utiles

1. **Entity relationships**
   ```
   ┌──────────┐     ┌────────────┐     ┌──────────┐
   │   User   │────<│ ToRegister │>────│  Event   │
   └──────────┘     └────────────┘     └──────────┘
                                             │
                                             │
                                       ┌──────────┐
                                       │ Category │
                                       └──────────┘
   ```

2. **Request flow**
   ```
   Browser → Controller → Form → Entity → Repository → Database
              ↓                    ↑
           Template ←─────────────┘
   ```

3. **CI/CD pipeline**
   ```
   Push → Tests → Build → Docker → Deploy
           ↓
       Coverage report
   ```

---

## Tip 10: Spaced-repetition learning

### Skill `/learn-symfony`

```markdown
## Spaced Repetition pour TheCrossLove

1. Quiz sur les concepts:
   - "Explique le cycle de vie d'une requête Symfony"
   - "Comment fonctionne le Security Voter?"

2. Follow-up questions:
   - "Et si l'utilisateur n'est pas authentifié?"
   - "Que se passe-t-il avec un event full?"

3. Stockage des gaps:
   .claude/learning/
   ├── symfony-forms.md
   ├── doctrine-relations.md
   └── security-voters.md
```

---

## Plan d'implémentation

### Phase 1: Fondations (immédiat)
- [ ] Enrichir CLAUDE.md avec les erreurs communes
- [ ] Créer structure `.claude/notes/` et `.claude/skills/`
- [ ] Ajouter verification checklist

### Phase 2: Skills essentiels (semaine 1)
- [ ] Créer `/test-feature`
- [ ] Créer `/deploy-check`
- [ ] Créer `/techdebt`

### Phase 3: Workflows avancés (semaine 2)
- [ ] Créer `/symfony-entity`
- [ ] Créer `/db-stats`
- [ ] Configurer parallel instances patterns

### Phase 4: Learning & docs (continu)
- [ ] ASCII diagrams de l'architecture
- [ ] HTML presentation du projet
- [ ] Spaced repetition skill

---

## Fichiers à créer/modifier

```
.claude/
├── skills/
│   ├── symfony-entity.md
│   ├── test-feature.md
│   ├── deploy-check.md
│   ├── techdebt.md
│   ├── db-stats.md
│   └── event-workflow.md
├── notes/
│   ├── events.md
│   ├── registrations.md
│   ├── security.md
│   └── testing.md
├── learning/
│   └── gaps.md
└── plans/
    └── boris-cherny-tips-adaptation.md (ce fichier)

CLAUDE.md (à enrichir)
```

---

## Métriques de succès

1. **Réduction des erreurs** - Moins de bugs répétitifs
2. **Temps de développement** - Features livrées plus vite
3. **Qualité des tests** - Couverture > 80%
4. **Onboarding** - Nouveau dev productif en < 1 jour
