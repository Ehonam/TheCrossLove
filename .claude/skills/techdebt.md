# Skill: Tech Debt Analysis

Analyse de la dette technique dans TheCrossLove.

## Points d'analyse

### 1. Code duplique dans les Controllers
Rechercher:
- Logique de verification d'acces repetee
- Code de gestion des formulaires similaire
- Redirections avec flash messages identiques

```bash
# Chercher patterns repetitifs
grep -r "addFlash" src/Controller/
grep -r "denyAccessUnlessGranted" src/Controller/
```

### 2. Methodes de Repository similaires
Verifier dans `src/Repository/`:
- Queries quasi-identiques
- Filtres repetes
- Joins redondants

### 3. Templates Twig redondants
Analyser `templates/`:
- Blocs HTML repetes
- Inclusions manquantes
- Macros qui pourraient etre creees

### 4. Patterns non respectes
Verifier:
- Conventions de nommage ID (`id_<entity>`)
- Validation constraints sur les entities
- CSRF protection sur tous les forms
- Cascade correctement configure

## Workflow d'analyse

1. Scanner les Controllers pour duplication
2. Analyser les Repositories
3. Verifier les Templates
4. Lister les violations de patterns
5. Prioriser par impact

## Metriques a reporter

| Metrique | Seuil acceptable |
|----------|-----------------|
| Lignes de code dupliquees | < 5% |
| Methodes > 30 lignes | 0 |
| Classes > 300 lignes | 0 |
| Cyclomatic complexity | < 10 |

## Actions recommandees

### Haute priorite
- Extraire services pour logique repetee
- Creer traits pour code commun
- Factoriser templates avec includes

### Moyenne priorite
- Refactorer queries complexes
- Ajouter validation manquante
- Documenter le code obscur

### Basse priorite
- Renommer variables peu claires
- Reorganiser imports
- Nettoyer code mort
