<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patrol_session_id')->nullable()->constrained('patrol_sessions');
            $table->foreignId('user_id')->constrained('users'); // pelapor
            $table->foreignId('area_id')->constrained('areas');
            $table->string('title', 200);
            $table->enum('category', [ // [fix] menentukan auto-eskalasi - PRD bab 17
                'kebakaran', 'kebocoran', 'orang_mencurigakan', 'kerusakan', 'kecelakaan', 'lainnya',
            ]);
            $table->text('description')->nullable();
            $table->string('photo')->nullable();
            $table->string('video')->nullable(); // [fix] field eksplisit di PRD bab 17
            $table->decimal('latitude', 10, 7)->nullable(); // [fix]
            $table->decimal('longitude', 10, 7)->nullable(); // [fix]
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['new', 'in_review', 'approved', 'rejected', 'follow_up_required', 'closed'])->default('new');
            $table->foreignId('approved_by')->nullable()->constrained('users'); // [fix]
            $table->dateTime('approved_at')->nullable(); // [fix]
            $table->text('approval_notes')->nullable(); // [fix]
            $table->enum('sync_status', ['synced', 'pending_sync'])->default('synced'); // [fix] foto offline ikut antrian - PRD bab 14
            $table->dateTime('reported_at');
            $table->timestamps();

            $table->index('status');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
