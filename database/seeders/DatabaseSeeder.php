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

        // Akun contoh hanya dibuat di lingkungan lokal. Di staging/produksi,
        // akun admin harus dibuat manual lewat menu Petugas atau perintah
        // khusus agar tidak ada kredensial default yang bocor.
        if (! app()->environment('local')) {
            return;
        }

        $adminRole = Role::where('role_name', 'Admin')->first();

        User::factory()->create([
            'name' => 'Admin Lokal',
            'username' => 'admin',
            'email' => 'admin@localhost',
            'role_id' => $adminRole->id,
        ]);
    }
}
