<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jadwal patroli kini bersifat global: tidak terikat petugas (user_id)
     * dan tanggal tertentu (patrol_date) — berlaku setiap hari selama
     * waktu sekarang berada di antara start_time dan end_time.
     * Kolom tetap ada namun dibuat nullable agar data lama tidak hilang.
     */
    public function up(): void
    {
        Schema::table('patrol_schedules', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->date('patrol_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('patrol_schedules', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
            $table->date('patrol_date')->nullable(false)->change();
        });
    }
};
