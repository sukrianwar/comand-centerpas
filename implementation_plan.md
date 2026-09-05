# Rencana Sinkronisasi Dashboard & Fitur Incidents

Dokumen ini merangkum hasil audit visual dan rencana eksekusi untuk Bagian B (Tabel Incidents), C (DashboardController), dan D (Denah Area), sesuai dengan instruksi yang diberikan. 

## BAGIAN A: Hasil Audit Visual (Admin vs Member)

| Elemen | Admin (`dashboard.blade.php`) | Member (`member/dashboard.blade.php`) | Rencana Penyelarasan |
| :--- | :--- | :--- | :--- |
| **1. Warna Badge (Selesai/Terlewat)** | **Selesai:** `bg-emerald-50 text-emerald-700 border-emerald-200`<br>**Terlewat:** `bg-amber-50 text-amber-700 border-amber-200` | **Selesai:** `bg-emerald-50 text-emerald-600 border-emerald-100`<br>**Sync Queue:** `bg-orange-50 text-orange-700 border-orange-200` | Menggunakan **standar Admin** untuk badge (700 untuk text, 200 untuk border) di kedua area. *Sync queue* di member akan diubah dari `orange` ke `amber` agar konsisten secara semantik. |
| **2. Border Radius Card** | Dominan `rounded-xl` (12px) untuk semua card statistik dan panel. | Kombinasi `rounded-3xl` (24px) untuk card utama, dan `rounded-2xl` (16px) untuk elemen/tombol di dalamnya. | **Standarisasi `rounded-2xl` (16px)** untuk card utama di Admin agar selaras dengan desain *modern/bubbly* Member Area, tetapi tidak terlalu memakan ruang (data-dense). Sub-elemen akan menggunakan `rounded-xl`. |
| **3. Penggunaan Warna Tailwind** | Banyak menggunakan default (misal: `bg-emerald-400`, `text-blue-600`, `bg-error`). Ada inkonsistensi (misal: tabel row highlight menggunakan `#F9FAFB` hardcode). | Menggunakan *gradient* (`from-primary to-blue-700`), default (`emerald-400`, `red-500`), dan background semi-transparan. | Menghapus kode warna *hardcode* `#F9FAFB` di Admin dan menggantinya dengan `bg-surface-container-low`. Menyelaraskan *gradient* dan penggunaan warna *error* (merah). |

> [!NOTE]
> Setelah rencana ini disetujui, saya akan langsung mengimplementasikan standarisasi desain di atas ke `dashboard.blade.php`.

---

## BAGIAN B: Tabel Incidents Minimal

Saya akan membuat:
1. **Migration** `create_incidents_table` sesuai skema yang diberikan (relasi ke `users`, `areas`, `checkpoints`, dll).
2. **Model** `Incident` dengan relasi `belongsTo` yang sesuai.
3. **Seeder** `IncidentSeeder` berisi 2 insiden realistis dengan user "Citra Lestari", status "open" dan "in_progress".

---

## BAGIAN C: DashboardController Query Real

Saya akan memperbarui (atau membuat jika belum ada) `DashboardController@index` dengan logika berikut:

1. **Total Rute:** `PatrolSchedule::whereDate('patrol_date', today())->count()`
2. **Selesai Tepat Waktu & Persentase:** Menghitung `PatrolLog` hari ini dengan status `on_time`. Persentase dihitung dengan membagi angka ini terhadap total *expected checkpoints* (didapat dari `withCount('checkpoints')` pada rute hari ini).
3. **Titik Terlewat:**
   - **Logika:** Mengambil semua `PatrolSchedule` hari ini yang `end_time`-nya *sudah lewat* dari jam sekarang.
   - Mengumpulkan seluruh `checkpoint_id` yang wajib discan dari jadwal-jadwal tersebut.
   - Menghitung berapa banyak `checkpoint_id` yang **TIDAK ADA** di tabel `PatrolLog` untuk jadwal yang sama.
   - Saya akan menempatkan logika ini di method terpisah `getMissedCheckpointsCount()` di Controller atau Service agar rapi.
4. **Insiden Hari Ini:** `Incident::whereDate('reported_at', today())->count()`
5. **Petugas Aktif:** `User::where('status','active')->whereHas('role', ...)->count()`
6. **Log Patroli & Peringatan Insiden:** Query `latest()` dengan `limit()`, serta relasi lengkap.

Data ini akan di-*pass* ke `dashboard.blade.php` dan me-replace semua angka *hardcode*.

---

## BAGIAN D & VERIFIKASI
- **Peta Statis:** Tidak akan disentuh dan dibiarkan *fallback* statis.
- **Verifikasi:** Akan ada konfirmasi log performa untuk memastikan tidak ada query N+1 pada data log patroli dan insiden.

> [!IMPORTANT]
> **User Review Required**
> Mohon periksa hasil Audit Visual di Bagian A dan logika *Titik Terlewat* di Bagian C. Jika Anda sudah setuju dengan rencana ini, saya akan segera mengeksekusi semua kodenya (Bagian A - D) sekaligus.
