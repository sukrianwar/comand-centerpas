<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Composite index untuk query patrol() — filter by user, date, status
        Schema::table('patrol_schedules', function (Blueprint $table) {
            $table->index(['user_id', 'patrol_date', 'status'], 'patrol_schedules_user_date_status_index');
        });

        // Index untuk query where('patrol_date', $today) di patrol()
        Schema::table('patrol_schedules', function (Blueprint $table) {
            $table->index('patrol_date', 'patrol_schedules_patrol_date_index');
        });

        // Index untuk query where('user_id') di patrol_logs
        Schema::table('patrol_logs', function (Blueprint $table) {
            $table->index('user_id', 'patrol_logs_user_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('patrol_schedules', function (Blueprint $table) {
            $table->dropIndex('patrol_schedules_user_date_status_index');
            $table->dropIndex('patrol_schedules_patrol_date_index');
        });

        Schema::table('patrol_logs', function (Blueprint $table) {
            $table->dropIndex('patrol_logs_user_id_index');
        });
    }
};
