# Instruksi Proyek (Memory)

## Aturan Penting — Wajib Minta Izin Dulu

Sebelum melakukan aksi berikut, **SELALU minta izin ke user terlebih dahulu** (jangan langsung eksekusi):

1. **Perubahan data di database** — perintah:
   - `php artisan migrate`, `migrate:fresh`, `migrate:refresh`, `migrate:reset`, `db:seed`
   - `INSERT`, `UPDATE`, `DELETE`, `DROP`, `TRUNCATE`, `ALTER` ke MySQL
   - akses mysql via: `docker compose exec mysql mysql ...`
2. **Menghapus / menghentikan container Docker** — perintah:
   - `docker rm`, `docker rmi`, `docker stop`, `docker kill`
   - `docker compose down` (tanpa flag aman), `docker system prune`
   - menghapus volume container

Jika user sudah memberikan perintah tersebut secara eksplisit di sesi ini, anggap sudah izin. Jika tidak, jelaskan rencana perubahan dan tunggu konfirmasi sebelum eksekusi.

## Konteks Infrastruktur

- Stack: Laravel + Docker Compose (`docker-compose.yml`)
- Container: `commandcenter-mysql` (MySQL 8.0), `commandcenter-app` (PHP-FPM + Nginx)
- Menjalankan artisan: `docker compose exec app php artisan ...`
