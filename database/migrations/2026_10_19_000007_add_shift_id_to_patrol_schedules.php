<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jadwal patroli kini mereferensikan master shift lewat `shift_id`.
     * Kolom lama `shift` (enum pagi/malam) dipertahankan sebagai warisan
     * data dan dibuat nullable — tidak dipakai lagi oleh kode baru.
     */
    public function up(): void
    {
        // Ubah kolom lama menjadi nullable lebih dulu (sendiri), agar rebuild
        // tabel di SQLite tidak bentrok dengan penambahan kolom baru.
        Schema::table('patrol_schedules', function (Blueprint $table) {
            $table->enum('shift', ['pagi', 'malam'])->nullable()->change();
        });

        Schema::table('patrol_schedules', function (Blueprint $table) {
            $table->foreignId('shift_id')
                ->nullable()
                ->after('area_id')
                ->constrained('shifts')
                ->nullOnDelete();
        });

        $this->backfillShiftIds();
    }

    public function down(): void
    {
        Schema::table('patrol_schedules', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shift_id');
        });

        Schema::table('patrol_schedules', function (Blueprint $table) {
            $table->enum('shift', ['pagi', 'malam'])->nullable(false)->change();
        });
    }

    /**
     * Petakan nilai shift lama (pagi/malam) ke master shift yang sesuai,
     * membuat baris shift bawaan bila belum tersedia.
     */
    private function backfillShiftIds(): void
    {
        $defaults = [
            'pagi'  => ['shift_name' => 'Pagi',  'start' => '07:00:00', 'end' => '15:00:00'],
            'siang' => ['shift_name' => 'Siang', 'start' => '15:00:00', 'end' => '23:00:00'],
            'malam' => ['shift_name' => 'Malam', 'start' => '23:00:00', 'end' => '07:00:00'],
        ];

        $legacyValues = DB::table('patrol_schedules')
            ->whereNotNull('shift')
            ->distinct()
            ->pluck('shift');

        foreach ($legacyValues as $legacy) {
            $meta = $defaults[$legacy] ?? [
                'shift_name' => ucfirst((string) $legacy),
                'start'      => '00:00:00',
                'end'        => '00:00:00',
            ];

            $shift = DB::table('shifts')->where('shift_name', $meta['shift_name'])->first();

            if (! $shift) {
                $id = DB::table('shifts')->insertGetId([
                    'shift_name' => $meta['shift_name'],
                    'start_time' => $meta['start'],
                    'end_time'   => $meta['end'],
                    'status'     => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('shifts')->where('id', $id)->update([
                    'shift_code' => 'SFT-'.str_pad((string) $id, 2, '0', STR_PAD_LEFT),
                ]);

                $shift = DB::table('shifts')->where('id', $id)->first();
            }

            DB::table('patrol_schedules')
                ->where('shift', $legacy)
                ->update(['shift_id' => $shift->id]);
        }
    }
};
