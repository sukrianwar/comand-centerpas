<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use SoftDeletes;

    protected $table = 'areas';

    protected $fillable = [
        'area_name',
        'area_code',
        'description',
    ];

    // ─── Relasi ──────────────────────────────────────────────────────────────

    /**
     * Semua jadwal patroli yang berlangsung di area ini.
     */
    public function patrolSchedules(): HasMany
    {
        return $this->hasMany(PatrolSchedule::class, 'area_id');
    }

    /**
     * Semua checkpoint yang berada di area ini.
     */
    public function checkpoints(): HasMany
    {
        return $this->hasMany(Checkpoint::class, 'area_id');
    }
}
