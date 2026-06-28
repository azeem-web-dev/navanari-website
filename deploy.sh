#!/usr/bin/env bash
#
# Navanari · Hostinger deploy helper
# Run this over SSH from inside the project folder after each git pull.
#
#   bash deploy.sh
#
set -e

echo "→ Installing PHP dependencies (production)…"
composer install --no-dev --optimize-autoloader

echo "→ Running database migrations…"
php artisan migrate --force

echo "→ Linking storage (for uploaded images)…"
php artisan storage:link 2>/dev/null || echo "  (storage link already exists — skipping)"

echo "→ Caching config, routes and views…"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✓ Deploy complete. Visit your site to verify."
