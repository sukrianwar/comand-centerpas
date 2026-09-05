<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jadwal patroli kini bersifat GLOBAL dan berlaku harian (berulang tiap
     * hari). Constraint unik (patrol_schedule_id, checkpoint_id) — dan varian
     * per-user — akan memblokir petugas yang menscan checkpoint yang sama
     * pada hari yang berbeda di jadwal yang sama.
     *
     * Constraint unik dihapus; proteksi duplikat dialihkan ke validasi
     * aplikasi (PatrolLogController) yang dibatasi per hari (scanned_at).
     */
    public function up(): void
    {
        $indexes = collect(DB::select('SHOW INDEX FROM patrol_logs'))->pluck('Key_name');

        foreach (['patrol_logs_schedule_checkpoint_user_unique', 'patrol_logs_schedule_checkpoint_unique'] as $name) {
            if ($indexes->contains($name)) {
                Schema::table('patrol_logs', fn (Blueprint $table) => $table->dropUnique($name));
            }
        }
    }

    public function down(): void
    {
        $indexes = collect(DB::select('SHOW INDEX FROM patrol_logs'))->pluck('Key_name');

        if (! $indexes->contains('patrol_logs_schedule_checkpoint_unique')) {
            Schema::table('patrol_logs', function (Blueprint $table) {
                $table->unique(
                    ['patrol_schedule_id', 'checkpoint_id'],
                    'patrol_logs_schedule_checkpoint_unique'
                );
            });
        }
    }
};
