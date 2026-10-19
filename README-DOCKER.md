# Docker Setup untuk Command Center PAS

## Persyaratan
- Docker & Docker Compose (terinstall)
- Port 8000, 5173, 3306 tersedia di lokal

## Langkah-langkah

### 1. Copy file environment
```bash
cp .env.docker .env
```

### 2. Jalankan semua container
```bash
docker compose up -d --build
```

### 3. Install dependencies & setup aplikasi
```bash
# Install composer dependencies
docker compose exec app composer install

# Generate app key
docker compose exec app php artisan key:generate

# Jalankan migrasi & seeder
docker compose exec app php artisan migrate --seed
```

### 4. Install frontend dependencies (jika belum)
```bash
docker compose exec vite npm install
```

## Akses Aplikasi
- **Web App**: http://localhost:8000
- **Vite HMR**: http://localhost:5173

## User default (dari seeder)
- **Email**: test@example.com
- **Password**: password

## Perintah berguna
```bash
# Cek status container
docker compose ps

# Lihat logs
docker compose logs -f app
docker compose logs -f nginx
docker compose logs -f vite

# Masuk ke container
docker compose exec app bash

# Jalankan artisan
docker compose exec app php artisan <command>

# Hentikan container
docker compose down

# Hentikan + hapus volume (data MySQL akan hilang)
docker compose down -v
```

## Catatan
- `APP_LOCALE` diset ke `id` dan `APP_TIMEZONE` ke `Asia/Jakarta`
- `DEV_SKIP_GPS=true` untuk mempermudah testing lokal tanpa validasi GPS
- Queue, cache, session menggunakan driver `database` (sudah diset di .env.docker)
