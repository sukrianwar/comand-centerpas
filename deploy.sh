#!/usr/bin/env bash
# Deploy Command Center (manual). Jalankan dari server ini.
#
#   ./deploy.sh              # pull, build, recreate, migrate, smoke test
#   ./deploy.sh --no-migrate # lewati migrasi database
#
set -euo pipefail

cd "$(dirname "$0")"

COMPOSE=(docker compose -f docker-compose.prod.yml)
APP_URL_LOCAL="http://127.0.0.1:8000"
RUN_MIGRATE=1

for arg in "$@"; do
    case "$arg" in
        --no-migrate) RUN_MIGRATE=0 ;;
        *) echo "Argumen tidak dikenal: $arg" >&2; exit 2 ;;
    esac
done

echo "==> Pull kode terbaru (main)"
git pull --ff-only origin main

echo "==> Build image"
"${COMPOSE[@]}" build app

echo "==> Recreate app, worker, nginx"
"${COMPOSE[@]}" up -d --force-recreate app worker nginx

echo "==> Tunggu aplikasi sehat"
for i in $(seq 1 30); do
    code="$(curl -s -o /dev/null -w '%{http_code}' -m 5 "$APP_URL_LOCAL/up" || true)"
    [ "$code" = "200" ] && break
    sleep 2
done
if [ "${code:-000}" != "200" ]; then
    echo "ERROR: aplikasi tidak sehat setelah recreate (periksa: docker logs commandcenter-app)" >&2
    exit 1
fi

if [ "$RUN_MIGRATE" = "1" ]; then
    echo "==> Migrasi database"
    "${COMPOSE[@]}" exec -T app php artisan migrate --force
else
    echo "==> Migrasi dilewati (--no-migrate)"
fi

echo "==> Smoke test"
for path in /login /up; do
    code="$(curl -s -o /dev/null -w '%{http_code}' -m 10 "$APP_URL_LOCAL$path")"
    printf '    %-8s %s\n' "$path" "$code"
    [ "$code" = "200" ] || { echo "ERROR: $path -> $code" >&2; exit 1; }
done

echo
echo "Deploy selesai."
echo "Rollback image: docker tag commandcenter-app:rollback-<commit> commandcenter-app:latest"
echo "                docker compose -f docker-compose.prod.yml up -d --force-recreate app worker nginx"
echo "Rollback DB   : gunakan dump di /root/backups/"
