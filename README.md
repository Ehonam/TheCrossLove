Déploiement

Le projet TheCrossLove est déployé automatiquement à l’aide de GitHub Actions et hébergé sur Render.

Processus de déploiement
- Le code source est hébergé sur GitHub
- Chaque push sur la branche `main` déclenche un workflow GitHub Actions
- Le workflow lance le build et le déploiement sur la plateforme Render
- Render met automatiquement à jour l’application en production

Ce système permet d’assurer un déploiement continu, fiable et sécurisé.

