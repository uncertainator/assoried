#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")"

echo "==> Pull des dernières modifications..."
git pull origin main

echo "==> Installation des dépendances Composer..."
composer install --no-dev --optimize-autoloader

echo "==> Exécution des migrations..."
php artisan migrate --force

echo "==> Vidage des caches..."
php artisan optimize:clear

echo "==> Déploiement terminé."
