<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shift extends Model
{
    use SoftDeletes;

    protected $table = 'shifts';

    protected $fillable = [
        'shift_code',
        'shift_name',
        'start_time',
        'end_time',
        'status',
        'description',
    ];

    // ─── Relasi ──────────────────────────────────────────────────────────────

    /**
     * Semua jadwal patroli yang memakai shift ini.
     */
    public function patrolSchedules(): HasMany
    {
        return $this->hasMany(PatrolSchedule::class, 'shift_id');
    }

    // ─── Scope ───────────────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    // ─── Helper ──────────────────────────────────────────────────────────────

    /**
     * Shift yang jam selesainya lebih awal dari jam mulai berarti melewati
     * tengah malam (mis. 23:00 → 07:00).
     */
    public function isOvernight(): bool
    {
        return $this->start_time > $this->end_time;
    }

    /**
     * Durasi shift dalam jam (dibulatkan 2 desimal).
     */
    public function getDurationHoursAttribute(): float
    {
        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);

        if ($end->lessThanOrEqualTo($start)) {
            $end->addDay();
        }

        return round($start->diffInMinutes($end) / 60, 2);
    }
}
