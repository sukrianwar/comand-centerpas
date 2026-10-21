# Runbook — Command Center PAS

Dokumen operasional deployment yang berjalan di server ini (akses LAN:
`http://172.17.154.174:8000`).

## Arsitektur

| Komponen | Container | Keterangan |
|---|---|---|
| PHP-FPM | `commandcenter-app` | image `commandcenter-app:latest`, port internal 9000 |
| Queue worker | `commandcenter-worker` | image yang sama, `queue:work` |
| Nginx | `commandcenter-nginx` | publish `0.0.0.0:8000 -> 8000` |
| MySQL | `commandcenter-mysql` | volume `mysql_data` |

- Compose file: `docker-compose.prod.yml` (project `comand-centerpas`).
- Kode aplikasi ada di volume `comand-centerpas_app_root` (`/var/www/html`).
  Image menyimpan salinan bersih di `/opt/src`; saat container start,
  `docker/php/entrypoint.sh` melakukan `rsync` ke volume (kecuali
  `storage/`, `bootstrap/cache/`, `.env`, `.git`), lalu cache config/route/view/event.
- Environment runtime: `docker/.env.production` (di-mount read-only sebagai `/var/www/html/.env`, tidak di-commit).
- Session: driver `database`, `SESSION_DOMAIN=null` (cookie host-only, aman untuk akses IP maupun domain).

> Catatan: file `Dockerfile`, `.env.docker`, `nginx.conf`, dan `docker-compose.yml`
> di root repo adalah setup alternatif (port 8001, PHP 8.2) dan **tidak dipakai**
> oleh deployment ini.

## Deploy

```bash
cd /root/Commandcenter/comand-centerpas
./deploy.sh                 # pull + build + recreate + migrate + smoke test
./deploy.sh --no-migrate    # bila tidak ada perubahan skema
```

Deploy manual tanpa script:

```bash
docker compose -f docker-compose.prod.yml build app
docker compose -f docker-compose.prod.yml up -d --force-recreate app worker nginx
docker compose -f docker-compose.prod.yml exec -T app php artisan migrate --force
```

## Rollback

```bash
# 1. Kembalikan image (tag rollback dibuat sebelum deploy besar)
docker tag commandcenter-app:rollback-<commit> commandcenter-app:latest
docker compose -f docker-compose.prod.yml up -d --force-recreate app worker nginx

# 2. Kembalikan database bila perlu
gunzip -c /root/backups/commandcenter-<stamp>.sql.gz \
  | docker exec -i commandcenter-mysql mysql -uroot -pcommandcenter commandcenter
```

## Backup

- Script: `/root/scripts/backup-commandcenter.sh`
- Jadwal: cron `/etc/cron.d/commandcenter` — harian 02:00, retensi 7 hari.
- Lokasi: `/root/backups/` (`commandcenter-*.sql.gz`, `env.production-*.bak`).
- Uji manual: `/root/scripts/backup-commandcenter.sh`

Restore:

```bash
gunzip -c /root/backups/commandcenter-<stamp>.sql.gz \
  | docker exec -i commandcenter-mysql mysql -uroot -pcommandcenter commandcenter
```

## Log & troubleshooting

```bash
docker logs commandcenter-app --tail 100
docker logs commandcenter-worker --tail 100
docker exec commandcenter-app tail -100 storage/logs/laravel.log
docker exec commandcenter-nginx tail -50 /var/log/nginx/laravel_error.log
```

- **419 Page Expired**: cookie session ditolak browser. Pastikan
  `SESSION_DOMAIN=null` (bukan domain tertentu) di `docker/.env.production`,
  lalu `docker compose -f docker-compose.prod.yml exec -T app php artisan config:clear`
  dan recreate container.
- **Build gagal di `pecl install redis`**: diganti kompilasi dari source GitHub
  (phpredis 6.3.0) di `docker/Dockerfile`.
- **Seeder shift bentrok (`Duplicate entry 'SFT-01'`)**: migrasi backfill sudah
  membuat shift dari jadwal lama. Tambahkan shift yang kurang tanpa menimpa:
  gunakan menu Shift di UI atau `firstOrCreate` per `shift_name`.
- **Kode lama muncul lagi setelah restart**: entrypoint menyalin `/opt/src` dari
  image. Pastikan image sudah di-rebuild, bukan hanya mengedit volume.

## Maintenance

- Build cache dibersihkan otomatis mingguan (`docker builder prune`, cache > 7 hari).
- Log container dibatasi `max-size=10m`, `max-file=3` (json-file).
- Volume `.git` dipertahankan hanya sebagai jejak kode; sumber kebenaran tetap repo.
