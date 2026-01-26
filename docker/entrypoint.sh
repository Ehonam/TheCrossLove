#!/bin/sh
# =============================================================================
# TheCrossLove - Script d'entree Docker
# =============================================================================
# Ce script initialise l'application au demarrage du conteneur
# Inclut des timeouts et une gestion d'erreurs amelioree

set -e

# Configuration
MAX_DB_RETRIES=30
DB_RETRY_INTERVAL=3
STARTUP_TIMEOUT=300

echo "=========================================="
echo "TheCrossLove - Demarrage du conteneur"
echo "=========================================="
echo "Environnement: ${APP_ENV:-dev}"
echo "Date: $(date)"
echo "=========================================="

# Fonction de log avec timestamp
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1"
}

# Fonction de gestion d'erreur
error_exit() {
    log "ERREUR: $1"
    exit 1
}

# =============================================================================
# ETAPE 1: Creation des repertoires
# =============================================================================
log "[1/5] Creation des repertoires..."
mkdir -p var/cache var/log public/uploads || error_exit "Impossible de creer les repertoires"
chown -R appuser:appgroup var public/uploads 2>/dev/null || log "Avertissement: Impossible de changer le proprietaire des repertoires"
log "Repertoires crees avec succes"

# =============================================================================
# ETAPE 2: Attente de la base de donnees avec timeout
# =============================================================================
log "[2/5] Attente de la base de donnees..."
if [ -n "$DATABASE_URL" ]; then
    retry_count=0
    db_ready=false

    while [ $retry_count -lt $MAX_DB_RETRIES ]; do
        if php bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; then
            db_ready=true
            break
        fi

        retry_count=$((retry_count + 1))
        remaining=$((MAX_DB_RETRIES - retry_count))
        log "Base de donnees non disponible, tentative $retry_count/$MAX_DB_RETRIES (encore $remaining essais)..."
        sleep $DB_RETRY_INTERVAL
    done

    if [ "$db_ready" = false ]; then
        error_exit "Base de donnees non disponible apres $MAX_DB_RETRIES tentatives. Verifiez la configuration DATABASE_URL et l'etat du serveur MySQL."
    fi

    log "Base de donnees connectee avec succes!"
else
    log "Avertissement: DATABASE_URL non defini, saut de la verification de la base de donnees"
fi

# =============================================================================
# ETAPE 3: Execution des migrations (production uniquement)
# =============================================================================
log "[3/5] Execution des migrations..."
if [ "$APP_ENV" = "prod" ]; then
    log "Environnement production detecte, execution des migrations..."

    if ! php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration; then
        error_exit "Echec des migrations de base de donnees. Verifiez les fichiers de migration et l'etat de la base de donnees."
    fi

    log "Migrations executees avec succes"
else
    log "Environnement $APP_ENV: saut des migrations automatiques"
fi

# =============================================================================
# ETAPE 4: Preparation du cache
# =============================================================================
log "[4/5] Preparation du cache..."
APP_ENV_VALUE=${APP_ENV:-dev}

if ! php bin/console cache:clear --env=$APP_ENV_VALUE --no-debug; then
    log "Avertissement: Echec du nettoyage du cache, tentative de suppression manuelle..."
    rm -rf var/cache/* 2>/dev/null || true
fi

if ! php bin/console cache:warmup --env=$APP_ENV_VALUE --no-debug; then
    error_exit "Echec du prechauffage du cache. Verifiez la configuration Symfony."
fi

log "Cache prepare avec succes"

# =============================================================================
# ETAPE 5: Configuration des permissions finales
# =============================================================================
log "[5/5] Configuration des permissions..."
chown -R appuser:appgroup var 2>/dev/null || log "Avertissement: Impossible de configurer les permissions sur var/"
chmod -R 775 var/cache var/log 2>/dev/null || true
chmod -R 775 public/uploads 2>/dev/null || true
log "Permissions configurees"

# =============================================================================
# VERIFICATION FINALE
# =============================================================================
log "=========================================="
log "TheCrossLove - Verification finale"
log "=========================================="

# Verifier que le cache existe
if [ -d "var/cache/$APP_ENV_VALUE" ]; then
    log "Cache: OK"
else
    log "Avertissement: Repertoire de cache non trouve"
fi

# Verifier les logs
if [ -w "var/log" ]; then
    log "Logs: OK (ecriture autorisee)"
else
    log "Avertissement: Repertoire de logs non accessible en ecriture"
fi

# Verifier uploads
if [ -w "public/uploads" ]; then
    log "Uploads: OK (ecriture autorisee)"
else
    log "Avertissement: Repertoire uploads non accessible en ecriture"
fi

echo "=========================================="
echo "TheCrossLove - Pret!"
echo "Demarrage de l'application..."
echo "=========================================="

# Executer la commande passee en argument
exec "$@"
