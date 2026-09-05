<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checkpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('areas');
            $table->string('checkpoint_name', 150);
            $table->string('qr_code', 100)->unique(); // QR statis
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->unsignedSmallInteger('radius')->default(20); // meter
            $table->string('photo')->nullable(); // [fix] wajib di PRD bab 11
            $table->enum('status', ['active', 'inactive'])->default('active'); // [fix] nonaktifkan tanpa hapus histori
            $table->string('description', 255)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('area_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkpoints');
    }
};
