## ✅ Status Keamanan & Auth (diperbarui 2026-10-19)

- [x] Middleware `auth` aktif di semua route group — dashboard, incidents, guards, shifts, settings, areas, checkpoints, patrol-schedules, petugas, dan seluruh /member/*
- [x] Redirect role-based setelah login — Security Guard → `member.dashboard`, Admin/Supervisor → `dashboard` Command Center
- [x] Middleware `RestrictGuardToMemberArea` aktif — Security Guard terkunci ke /member/*, Admin/Supervisor akses penuh ke semua route
- [x] Banner "MODE DEVELOPMENT" sudah dihapus dari semua view
- [x] Policy `PatrolSchedulePolicy` aktif — create/edit/delete hanya untuk Admin & Supervisor
- [x] Registrasi publik (`/register`) dinonaktifkan — akun petugas hanya dibuat Admin lewat menu Petugas
- [x] Verifikasi email dihapus (kolom `email_verified_at` sudah tidak ada di tabel `users`)

## ⚠️ Konfigurasi Lingkungan

- `.env` & `.env.docker` = **mode development** (`APP_ENV=local`, `APP_DEBUG=true`, `DEV_SKIP_GPS=true`). Ini disengaja untuk dev.
- `DEV_SKIP_GPS=true` akan **dipaksa menjadi false otomatis** saat `APP_ENV=production` (lihat `AppServiceProvider::boot()`), sehingga validasi GPS selalu aktif di produksi.
- Untuk deploy produksi, salin `.env.production.example` → `.env.production` lalu isi kredensial. Jangan pakai `.env` dev.
- Proxy tepercaya diatur lewat `TRUSTED_PROXIES` (dipisah koma). Kosongkan bila tidak di balik reverse proxy.
