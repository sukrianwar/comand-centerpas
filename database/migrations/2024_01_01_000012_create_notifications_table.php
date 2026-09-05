<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('title', 200);
            $table->text('message')->nullable();
            $table->enum('type', [
                'checkpoint_missed', 'patrol_not_started', 'critical_incident',
                'panic_button', 'sync_failed', 'suspicious_timestamp',
            ]);
            $table->enum('channel', ['in_app', 'dashboard', 'email', 'browser_push']); // [fix] matriks eskalasi bab 15
            $table->boolean('is_read')->default(false);
            $table->string('related_table', 50)->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
