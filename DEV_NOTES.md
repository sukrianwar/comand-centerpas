## ✅ Status Keamanan & Auth (diperbarui 2026-07-27)

- [x] Middleware `auth` aktif di semua route group — dashboard, incidents, guards, shifts, settings, areas, checkpoints, patrol-schedules, petugas, dan seluruh /member/*
- [x] Redirect role-based setelah login — Security Guard → `member.dashboard`, Admin/Supervisor → `dashboard` Command Center
- [x] Middleware `RestrictGuardToMemberArea` aktif — Security Guard terkunci ke /member/*, Admin/Supervisor akses penuh ke semua route
- [x] `APP_DEBUG=false` dan `APP_ENV=production` di `.env`
- [x] Banner "MODE DEVELOPMENT" sudah dihapus dari semua view
- [x] Policy `PatrolSchedulePolicy` aktif — create/edit/delete hanya untuk Admin & Supervisor
