<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatrolLogPhoto extends Model
{
    protected $table = 'patrol_log_photos';

    protected $fillable = [
        'patrol_log_id',
        'photo_path',
    ];

    public function patrolLog(): BelongsTo
    {
        return $this->belongsTo(PatrolLog::class, 'patrol_log_id');
    }
}
