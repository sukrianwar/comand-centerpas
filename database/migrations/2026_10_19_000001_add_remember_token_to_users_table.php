<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel users buatan sendiri tidak menyertakan kolom remember_token,
     * padahal Laravel memakainya untuk fitur "remember me" saat login.
     * Tanpa kolom ini, login dengan remember me akan error.
     */
    public function up(): void
    {
        if (Schema::hasColumn('users', 'remember_token')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->rememberToken()->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('remember_token');
        });
    }
};
