<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * QR Code checkpoint tidak dipakai lagi: petugas cukup memilih checkpoint
     * dari daftar di halaman patroli. Kolom qr_code tetap dipertahankan
     * (tanpa di-drop) demi keamanan data lama, namun dibuat nullable karena
     * checkpoint baru tidak lagi menghasilkan QR.
     */
    public function up(): void
    {
        Schema::table('checkpoints', function (Blueprint $table) {
            $table->string('qr_code', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('checkpoints', function (Blueprint $table) {
            $table->string('qr_code', 100)->nullable(false)->change();
        });
    }
};
