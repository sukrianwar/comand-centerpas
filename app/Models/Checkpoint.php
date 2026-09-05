<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Checkpoint extends Model
{
    use SoftDeletes;

    protected $table = 'checkpoints';

    protected $fillable = [
        'area_id',
        'checkpoint_name',
        'qr_code',
        'latitude',
        'longitude',
        'radius',
        'photo',
        'status',
        'description',
    ];

    protected $casts = [
        'latitude'  => 'decimal:7',
        'longitude' => 'decimal:7',
        'radius'    => 'integer',
    ];

    // ─── Relasi ──────────────────────────────────────────────────────────────

    /**
     * Area tempat checkpoint ini berada.
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    /**
     * Semua jadwal patroli yang menyertakan checkpoint ini.
     */
    public function patrolSchedules(): BelongsToMany
    {
        return $this->belongsToMany(
            PatrolSchedule::class,
            'patrol_schedule_checkpoints',
            'checkpoint_id',
            'patrol_schedule_id'
        )
        ->withPivot('sequence_number')
        ->orderByPivot('sequence_number', 'asc')
        ->withTimestamps();
    }

    /**
     * Generate unique QR code dengan retry logic.
     */
    public static function generateUniqueQrCode(): string
    {
        $maxRetries = 5;
        $attempt = 0;

        while ($attempt < $maxRetries) {
            $qrCode = 'CP-' . strtoupper(\Illuminate\Support\Str::random(8));
            
            // Gunakan exists() agar lebih cepat
            if (!self::where('qr_code', $qrCode)->exists()) {
                return $qrCode;
            }

            $attempt++;
        }

        throw new \Exception('Gagal men-generate QR Code unik setelah ' . $maxRetries . ' percobaan.');
    }
}
