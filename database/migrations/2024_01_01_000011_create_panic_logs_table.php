<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('panic_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('area_id')->nullable()->constrained('areas');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('message')->nullable();
            $table->enum('handling_status', ['pending', 'in_progress', 'resolved'])->default('pending'); // [fix] wajib di PRD 21.2
            $table->foreignId('handled_by')->nullable()->constrained('users'); // [fix]
            $table->dateTime('handled_at')->nullable(); // [fix]
            $table->timestamps();

            $table->index('handling_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('panic_logs');
    }
};
