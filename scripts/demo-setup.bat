@echo off
REM =============================================================================
REM TheCrossLove - Script de préparation démo (Windows)
REM Usage: scripts\demo-setup.bat
REM =============================================================================

echo.
echo ======================================================
echo    TheCrossLove - Preparation de la demo
echo ======================================================
echo.

REM 1. Vérifier Docker
echo [1/6] Verification Docker...
docker info >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo [ERREUR] Docker n'est pas demarre. Lancez Docker Desktop d'abord.
    pause
    exit /b 1
)
echo [OK] Docker disponible

REM 2. Démarrer les containers
echo.
echo [2/6] Demarrage des containers...
docker-compose up -d
timeout /t 5 /nobreak >nul
echo [OK] Containers demarres

REM 3. Vérifier les dépendances
echo.
echo [3/6] Verification des dependances...
if exist vendor (
    echo [OK] Dependances deja installees
) else (
    composer install --no-interaction
    echo [OK] Dependances installees
)

REM 4. Préparer la base de données
echo.
echo [4/6] Configuration base de donnees...
php bin/console doctrine:database:create --if-not-exists --no-interaction 2>nul
php bin/console doctrine:migrations:migrate --no-interaction
echo [OK] Migrations appliquees

REM 5. Charger les fixtures
echo.
echo [5/6] Chargement des donnees de demo...
php bin/console doctrine:fixtures:load --no-interaction
echo [OK] Fixtures chargees

REM 6. Vider le cache
echo.
echo [6/6] Nettoyage du cache...
php bin/console cache:clear --no-interaction
echo [OK] Cache vide

REM Résumé
echo.
echo ======================================================
echo    DEMO PRETE !
echo ======================================================
echo.
echo Comptes de demonstration:
echo    Admin: admin@thecrosslove.com / admin123
echo    User:  john.doe@example.com / password123
echo.
echo Pour demarrer le serveur:
echo    symfony server:start
echo    ou: php -S localhost:8000 -t public/
echo.
echo URLs utiles:
echo    App:         http://localhost:8000
echo    MailCatcher: http://localhost:1080
echo    Health:      http://localhost:8000/health
echo.
echo Bon courage pour la soutenance !
echo.
pause
