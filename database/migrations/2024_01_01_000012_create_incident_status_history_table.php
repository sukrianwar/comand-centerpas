<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained('incidents')->cascadeOnDelete();
            $table->string('status', 30);
            $table->foreignId('changed_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->dateTime('changed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_status_history');
    }
};
