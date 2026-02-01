#!/bin/bash
# =============================================================================
# TheCrossLove - Script de préparation démo
# Usage: ./scripts/demo-setup.sh
# =============================================================================

set -e

echo "🚀 TheCrossLove - Préparation de la démo..."
echo "============================================"

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Fonction pour afficher le statut
status() {
    echo -e "${GREEN}✓${NC} $1"
}

error() {
    echo -e "${RED}✗${NC} $1"
    exit 1
}

warning() {
    echo -e "${YELLOW}!${NC} $1"
}

# 1. Vérifier Docker
echo ""
echo "📦 Étape 1/6: Vérification Docker..."
if docker info > /dev/null 2>&1; then
    status "Docker est disponible"
else
    error "Docker n'est pas démarré. Lancez Docker Desktop d'abord."
fi

# 2. Démarrer les containers
echo ""
echo "🐳 Étape 2/6: Démarrage des containers..."
docker-compose up -d
sleep 5
status "Containers démarrés (MySQL + MailCatcher)"

# 3. Installer les dépendances
echo ""
echo "📚 Étape 3/6: Vérification des dépendances..."
if [ -d "vendor" ]; then
    status "Dépendances déjà installées"
else
    composer install --no-interaction
    status "Dépendances installées"
fi

# 4. Préparer la base de données
echo ""
echo "🗄️ Étape 4/6: Configuration base de données..."
php bin/console doctrine:database:create --if-not-exists --no-interaction 2>/dev/null || true
php bin/console doctrine:migrations:migrate --no-interaction
status "Migrations appliquées"

# 5. Charger les fixtures
echo ""
echo "📊 Étape 5/6: Chargement des données de démo..."
php bin/console doctrine:fixtures:load --no-interaction
status "Fixtures chargées (11 événements, 9 utilisateurs)"

# 6. Vider le cache
echo ""
echo "🧹 Étape 6/6: Nettoyage du cache..."
php bin/console cache:clear --no-interaction
status "Cache vidé"

# Résumé
echo ""
echo "============================================"
echo -e "${GREEN}✅ DÉMO PRÊTE !${NC}"
echo "============================================"
echo ""
echo "📌 Comptes de démonstration:"
echo "   Admin: admin@thecrosslove.com / admin123"
echo "   User:  john.doe@example.com / password123"
echo ""
echo "🌐 Pour démarrer le serveur:"
echo "   symfony server:start"
echo "   ou: php -S localhost:8000 -t public/"
echo ""
echo "🔗 URLs utiles:"
echo "   App:        http://localhost:8000"
echo "   MailCatcher: http://localhost:1080"
echo "   Health:     http://localhost:8000/health"
echo ""
echo "🎯 Bon courage pour la soutenance ! 💪"
