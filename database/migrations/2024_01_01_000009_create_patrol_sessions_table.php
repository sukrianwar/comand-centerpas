<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patrol_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patrol_schedule_id')->constrained('patrol_schedules');
            $table->foreignId('user_id')->constrained('users');
            $table->dateTime('start_time'); // server time saat Start Patrol
            $table->dateTime('end_time')->nullable();
            $table->enum('status', ['ongoing', 'completed', 'incomplete', 'late'])->default('ongoing');
            $table->unsignedSmallInteger('total_checkpoint')->default(0);
            $table->unsignedSmallInteger('checkpoint_completed')->default(0);
            $table->unsignedSmallInteger('checkpoint_missed')->default(0);
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patrol_sessions');
    }
};
