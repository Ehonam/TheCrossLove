# Skill: Deploy Check

Verification pre-deploiement pour TheCrossLove.

## Checklist de deploiement

### 1. Audit de securite
```bash
composer audit
```
Verifie les vulnerabilites connues dans les dependances.

### 2. Tests complets
```bash
php bin/phpunit
```
Tous les tests doivent passer (Unit + Integration).

### 3. Statut des migrations
```bash
php bin/console doctrine:migrations:status
```
Verifier qu'il n'y a pas de migrations en attente.

### 4. Validation schema
```bash
php bin/console doctrine:schema:validate
```
Schema doit etre synchronise.

### 5. Variables d'environnement
Verifier la presence de:
- `DATABASE_URL` - Connexion MySQL
- `APP_SECRET` - Cle secrete Symfony
- `APP_ENV` - production

### 6. Cache
```bash
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
```

## Workflow

1. Executer chaque verification
2. Reporter les problemes trouves
3. Bloquer le deploiement si:
   - Vulnerabilites critiques
   - Tests echouent
   - Migrations en attente
   - Variables manquantes

## Checklist finale

- [ ] `composer audit` - Pas de vulnerabilites critiques
- [ ] `php bin/phpunit` - Tous tests passent
- [ ] `doctrine:migrations:status` - Aucune migration en attente
- [ ] `doctrine:schema:validate` - Schema OK
- [ ] Variables env configurees
- [ ] Cache warmup OK
