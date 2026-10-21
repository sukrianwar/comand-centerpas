<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Koordinat GPS device saat pencatatan checkpoint tidak dipakai lagi.
     * Kolom tetap dipertahankan (tanpa di-drop) demi keamanan data lama,
     * namun dibuat nullable agar pencatatan baru tidak mengisi koordinat.
     */
    public function up(): void
    {
        Schema::table('patrol_logs', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->change();
            $table->decimal('longitude', 10, 7)->nullable()->change();
            $table->decimal('distance_meters', 8, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('patrol_logs', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable(false)->change();
            $table->decimal('longitude', 10, 7)->nullable(false)->change();
            $table->decimal('distance_meters', 8, 2)->nullable(false)->change();
        });
    }
};
