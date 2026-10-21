<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Validasi GPS tidak dipakai lagi: petugas cukup memilih checkpoint dari
     * daftar di halaman patroli. Kolom latitude/longitude/radius tetap
     * dipertahankan (tanpa di-drop) demi keamanan data lama, namun dibuat
     * nullable karena checkpoint baru tidak lagi mengisi koordinat.
     */
    public function up(): void
    {
        Schema::table('checkpoints', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->change();
            $table->decimal('longitude', 10, 7)->nullable()->change();
            $table->unsignedSmallInteger('radius')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('checkpoints', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable(false)->change();
            $table->decimal('longitude', 10, 7)->nullable(false)->change();
            $table->unsignedSmallInteger('radius')->nullable(false)->change();
        });
    }
};
