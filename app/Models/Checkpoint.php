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
        'photo',
        'status',
        'description',
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
}
