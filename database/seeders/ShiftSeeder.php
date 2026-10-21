<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    /**
     * Seed shift standar. Memakai firstOrCreate agar perubahan yang sudah
     * dilakukan admin tidak tertimpa saat seeder dijalankan ulang.
     */
    public function run(): void
    {
        $shifts = [
            ['shift_code' => 'SFT-01', 'shift_name' => 'Pagi',  'start_time' => '07:00', 'end_time' => '15:00'],
            ['shift_code' => 'SFT-02', 'shift_name' => 'Siang', 'start_time' => '15:00', 'end_time' => '23:00'],
            ['shift_code' => 'SFT-03', 'shift_name' => 'Malam', 'start_time' => '23:00', 'end_time' => '07:00'],
        ];

        foreach ($shifts as $shift) {
            Shift::firstOrCreate(
                ['shift_name' => $shift['shift_name']],
                [
                    'shift_code' => $shift['shift_code'],
                    'start_time' => $shift['start_time'],
                    'end_time'   => $shift['end_time'],
                    'status'     => 'active',
                ]
            );
        }
    }
}
