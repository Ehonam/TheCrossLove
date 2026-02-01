# Skill: Test Feature

Lance le workflow complet de test pour TheCrossLove.

## Commandes executees

### 1. Tests unitaires
```bash
php bin/phpunit tests/Unit/
```
Teste la logique metier isolee (Entity, Repository).

### 2. Tests d'integration
```bash
php bin/phpunit tests/Integration/
```
Teste les flux HTTP complets avec base de donnees.

### 3. Validation container
```bash
php bin/console lint:container
```
Verifie que le container Symfony est valide.

### 4. Validation schema
```bash
php bin/console doctrine:schema:validate
```
Verifie la synchronisation entities/database.

## Workflow

1. Executer toutes les commandes ci-dessus
2. Reporter les erreurs trouvees
3. Si erreurs: proposer des corrections
4. Si succes: confirmer que tout est OK

## En cas d'echec

### Tests echouent
- Lire le message d'erreur
- Identifier le test qui echoue
- Analyser le code du test
- Corriger le code source ou le test

### Container invalide
- Verifier les services dans services.yaml
- Verifier les imports dans les controllers
- Verifier les types dans les constructeurs

### Schema invalide
- Generer une migration: `php bin/console make:migration`
- Executer: `php bin/console doctrine:migrations:migrate`
