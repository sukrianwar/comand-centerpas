<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Perubahan pada tabel patrol_schedules:
     * 1. Ubah enum `shift` dari (pagi|siang|malam) menjadi (pagi|malam)
     * 2. Tambah kolom `round_number` (tinyInteger, nullable) setelah kolom `shift`
     * 3. Tambah kolom `deleted_at` untuk SoftDeletes
     */
    public function up(): void
    {
        // Langkah 1: Ubah enum shift menggunakan raw SQL
        // Laravel 12 mendukung ->change() natively, namun untuk mengubah ENUM
        // yang sudah punya data, pendekatan paling aman adalah via DB::statement
        // agar tidak ada data yang hilang.
        //
        // Catatan: Data existing dengan shift='siang' harus di-handle terlebih dahulu
        // (di production, sebaiknya migrate data 'siang' ke 'pagi' atau 'malam' sebelum jalankan ini).
        DB::statement("ALTER TABLE patrol_schedules MODIFY COLUMN `shift` ENUM('pagi', 'malam') NOT NULL");

        // Langkah 2 & 3: Tambah kolom round_number dan deleted_at
        Schema::table('patrol_schedules', function (Blueprint $table) {
            // Kolom round_number: menandai urutan patroli ke-1, 2, atau 3 dalam satu shift
            $table->tinyInteger('round_number')->nullable()->unsigned()->after('shift');

            // SoftDeletes: menambahkan kolom deleted_at
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patrol_schedules', function (Blueprint $table) {
            $table->dropColumn('round_number');
            $table->dropSoftDeletes();
        });

        // Kembalikan enum ke 3 nilai semula
        DB::statement("ALTER TABLE patrol_schedules MODIFY COLUMN `shift` ENUM('pagi', 'siang', 'malam') NOT NULL");
    }
};
