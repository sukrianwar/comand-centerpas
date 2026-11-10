<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')
                ->constrained('incidents')
                ->cascadeOnDelete();
            $table->string('photo_path');
            $table->timestamps();
        });

        // Pindahkan foto lama (satu foto per insiden) ke tabel relasi baru.
        DB::table('incidents')
            ->whereNotNull('photo')
            ->where('photo', '!=', '')
            ->orderBy('id')
            ->select('id', 'photo')
            ->each(function ($incident) {
                DB::table('incident_photos')->insert([
                    'incident_id' => $incident->id,
                    'photo_path' => $incident->photo,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_photos');
    }
};
