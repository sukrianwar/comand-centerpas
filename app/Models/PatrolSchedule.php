<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class PatrolSchedule extends Model
{
    use SoftDeletes;

    protected $table = 'patrol_schedules';

    protected $fillable = [
        'schedule_name',
        'area_id',
        'shift',
        'round_number',
        'start_time',
        'end_time',
        'interval',
        'status',
    ];

    protected $casts = [
        'round_number' => 'integer',
        'interval' => 'integer',
    ];

    // ─── Relasi ──────────────────────────────────────────────────────────────

    /**
     * Area tempat patroli berlangsung.
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    /**
     * Daftar checkpoint dalam jadwal patroli ini,
     * diurutkan berdasarkan sequence_number di tabel pivot.
     */
    public function checkpoints(): BelongsToMany
    {
        return $this->belongsToMany(
            Checkpoint::class,
            'patrol_schedule_checkpoints',
            'patrol_schedule_id',
            'checkpoint_id'
        )
        ->withPivot('sequence_number')
        ->orderByPivot('sequence_number', 'asc')
        ->withTimestamps();
    }

    // ─── Scopes Shift ────────────────────────────────────────────────────────

    public function scopePagi($query)
    {
        return $query->where('shift', 'pagi');
    }

    public function scopeMalam($query)
    {
        return $query->where('shift', 'malam');
    }

    // ─── Scope Jadwal Aktif ───────────────────────────────────────────────────

    /**
     * Jadwal yang sedang aktif saat ini:
     * - Status 'ongoing': SELALU aktif (tanpa batas jam) — admin menyalakan
     *   jadwal secara manual via edit status.
     * - Status 'scheduled': aktif hanya jika waktu sekarang berada di antara
     *   start_time dan end_time — termasuk shift malam lintas tengah malam.
     */
    public function scopeActiveNow($query, ?Carbon $now = null)
    {
        $now ??= Carbon::now();
        $time = $now->format('H:i:s');

        return $query->where(function ($q) use ($time) {
            // Status 'ongoing': selalu aktif
            $q->where('status', 'ongoing')
              // Status 'scheduled': aktif hanya dalam window jam
              ->orWhere(function ($q2) use ($time) {
                  $q2->where('status', 'scheduled')
                     ->where(function ($q3) use ($time) {
                         // Kasus normal (start <= end): now di antara start dan end
                         $q3->whereRaw('start_time <= end_time')
                            ->whereTime('start_time', '<=', $time)
                            ->whereTime('end_time', '>=', $time)
                            // Kasus lintas tengah malam (end < start): now >= start ATAU now <= end
                            ->orWhere(function ($q4) use ($time) {
                                $q4->whereRaw('end_time < start_time')
                                   ->where(function ($q5) use ($time) {
                                       $q5->whereTime('start_time', '<=', $time)
                                          ->orWhereTime('end_time', '>=', $time);
                                   });
                            });
                     });
              });
        });
    }
}
