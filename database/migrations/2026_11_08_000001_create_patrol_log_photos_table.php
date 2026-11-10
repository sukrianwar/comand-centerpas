<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patrol_log_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patrol_log_id')
                ->constrained('patrol_logs')
                ->cascadeOnDelete();
            $table->string('photo_path');
            $table->timestamps();
        });

        // Pindahkan foto lama (satu foto per log) ke tabel relasi baru.
        DB::table('patrol_logs')
            ->whereNotNull('photo_path')
            ->where('photo_path', '!=', '')
            ->orderBy('id')
            ->select('id', 'photo_path')
            ->each(function ($log) {
                DB::table('patrol_log_photos')->insert([
                    'patrol_log_id' => $log->id,
                    'photo_path' => $log->photo_path,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('patrol_log_photos');
    }
};
