<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatrolLog extends Model
{
    protected $table = 'patrol_logs';

    protected $fillable = [
        'patrol_schedule_id',
        'checkpoint_id',
        'user_id',
        'scanned_at',
        'latitude',
        'longitude',
        'distance_meters',
        'photo_path',
        'status',
        'notes',
    ];

    protected $casts = [
        'scanned_at'       => 'datetime',
        'latitude'         => 'decimal:7',
        'longitude'        => 'decimal:7',
        'distance_meters'  => 'decimal:2',
    ];

    // ─── Relasi ───────────────────────────────────────────────────────────────

    public function patrolSchedule(): BelongsTo
    {
        return $this->belongsTo(PatrolSchedule::class, 'patrol_schedule_id');
    }

    public function checkpoint(): BelongsTo
    {
        return $this->belongsTo(Checkpoint::class, 'checkpoint_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
