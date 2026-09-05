<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('patrol_logs');

        Schema::create('patrol_logs', function (Blueprint $table) {
            $table->id();

            // Relasi utama: ke jadwal patroli (bukan sesi)
            $table->foreignId('patrol_schedule_id')
                  ->constrained('patrol_schedules')
                  ->cascadeOnDelete();

            $table->foreignId('checkpoint_id')
                  ->constrained('checkpoints');

            $table->foreignId('user_id')
                  ->constrained('users'); // siapa yang scan

            // Waktu scan
            $table->dateTime('scanned_at');

            // Koordinat GPS device saat scan (BUKAN koordinat checkpoint)
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            // Jarak dari checkpoint saat scan — disimpan untuk audit
            $table->decimal('distance_meters', 8, 2);

            // Path foto bukti scan
            $table->string('photo_path');

            // on_time jika scanned_at masih dalam start_time–end_time, late jika sudah lewat
            $table->enum('status', ['on_time', 'late'])->default('on_time');

            $table->text('notes')->nullable();

            $table->timestamps();

            // Index biasa untuk query riwayat per jadwal
            $table->index(['patrol_schedule_id', 'checkpoint_id']);

            // Unique: 1 checkpoint hanya boleh discan 1x dalam 1 patrol_schedule
            $table->unique(['patrol_schedule_id', 'checkpoint_id'], 'patrol_logs_schedule_checkpoint_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patrol_logs');
    }
};

