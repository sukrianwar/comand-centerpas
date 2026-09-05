<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patrol_schedule_checkpoints', function (Blueprint $table) {
            $table->id();

            $table->foreignId('patrol_schedule_id')
                ->constrained('patrol_schedules')
                ->cascadeOnDelete();

            $table->foreignId('checkpoint_id')
                ->constrained('checkpoints');

            $table->unsignedSmallInteger('sequence_number')->default(1);

            $table->timestamps();

            // Nama index dipersingkat
            $table->unique(
                ['patrol_schedule_id', 'checkpoint_id'],
                'pscp_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patrol_schedule_checkpoints');
    }
};