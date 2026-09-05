<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::updateOrCreate(
            ['role_name' => 'Admin'], 
            ['description' => 'Akses penuh ke seluruh sistem']
        );
        
        Role::updateOrCreate(
            ['role_name' => 'Supervisor'], 
            ['description' => 'Mengelola jadwal, area, dan checkpoint patroli']
        );
        
        Role::updateOrCreate(
            ['role_name' => 'Security Guard'], 
            ['description' => 'Menjalankan patroli sesuai jadwal yang ditugaskan']
        );
    }
}
