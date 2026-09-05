<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('file_storage', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('file_path');
            $table->enum('file_type', ['photo', 'video']);
            $table->unsignedInteger('file_size')->nullable(); // KB
            $table->foreignId('uploaded_by')->nullable()->constrained('users');
            $table->string('reference_table', 50); // 'patrol_logs', 'incidents', dst
            $table->unsignedBigInteger('reference_id');
            $table->dateTime('taken_at')->nullable(); // [fix] validasi keaslian - PRD bab 13
            $table->decimal('taken_lat', 10, 7)->nullable(); // [fix]
            $table->decimal('taken_lng', 10, 7)->nullable(); // [fix]
            $table->date('retention_until')->nullable(); // [fix] kebijakan retensi - PRD bab 20.7
            $table->timestamp('created_at')->useCurrent();

            $table->index(['reference_table', 'reference_id']);
            $table->index('retention_until');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('file_storage');
    }
};
