#!/usr/bin/env bash
set -euo pipefail

# Adapter PHP_BIN si le serveur requiert php8.2, php8.3, etc.
# Vérifier avec : which php && php --version
PHP_BIN="php"

# Vérification des binaires requis
command -v "$PHP_BIN" >/dev/null 2>&1 || { echo "ERREUR : binaire '$PHP_BIN' introuvable. Adapter PHP_BIN en tête de script."; exit 1; }
command -v composer    >/dev/null 2>&1 || { echo "ERREUR : composer introuvable dans le PATH."; exit 1; }

cd "$(dirname "$0")"

echo "==> [1/3] Pull des dernières modifications..."
git pull origin main

echo "==> [2/3] Vérification des dépendances Composer..."
if git diff HEAD@{1} -- composer.json | grep -q .; then
    echo "      composer.json modifié — installation des dépendances (--no-dev)..."
    composer install --no-dev --optimize-autoloader
else
    echo "      composer.json inchangé — étape Composer ignorée."
fi

echo "==> [3/3] Exécution des migrations..."
"$PHP_BIN" artisan migrate --force

echo ""
echo "✓ Déploiement terminé avec succès."
