<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(ShiftSeeder::class);

        // Akun contoh hanya dibuat di lingkungan lokal. Di staging/produksi,
        // akun admin harus dibuat manual lewat menu Petugas atau perintah
        // khusus agar tidak ada kredensial default yang bocor.
        if (! app()->environment('local')) {
            return;
        }

        $superAdminRole = Role::where('role_name', 'Super Admin')->first();

        User::firstOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Admin Lokal',
                'email' => 'superadmin@localhost',
                'password' => 'password',
                'role_id' => $superAdminRole->id,
                'status' => 'active',
            ]
        );

        $adminRole = Role::where('role_name', 'Admin')->first();

        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin Lokal',
                'email' => 'admin@localhost',
                'password' => 'password',
                'role_id' => $adminRole->id,
                'status' => 'active',
            ]
        );
    }
}
