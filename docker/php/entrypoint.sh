#!/bin/sh
set -e

SRC_DIR="/opt/src"
APP_DIR="/var/www/html"

# Sync freshly built code into the shared volume (keeps storage/ and .env)
if [ -d "$SRC_DIR" ]; then
    rsync -a --delete \
        --exclude 'storage/' \
        --exclude 'bootstrap/cache/' \
        --exclude '.env' \
        --exclude '.git/' \
        "$SRC_DIR"/ "$APP_DIR"/
fi

cd "$APP_DIR"

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

if [ ! -f .env ]; then
    echo "WARN: .env not mounted at $APP_DIR/.env" >&2
fi

if [ "$1" = "php-fpm" ] || [ "$1" = "php-fpm83" ]; then
    if [ -n "$RUN_MIGRATIONS" ]; then
        php artisan migrate --force || true
    fi
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
    php artisan event:cache || true
fi

chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache

exec "$@"
