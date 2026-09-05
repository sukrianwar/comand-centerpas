<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patrol_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('schedule_name', 150); // [fix] wajib di PRD bab 12
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('area_id')->constrained('areas');
            $table->enum('shift', ['pagi', 'siang', 'malam']);
            $table->date('patrol_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('interval')->nullable(); // [fix] interval antar putaran (menit)
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'missed'])->default('scheduled');
            $table->timestamps();

            $table->index('user_id');
            $table->index('area_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patrol_schedules');
    }
};
