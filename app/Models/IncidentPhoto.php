<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentPhoto extends Model
{
    protected $table = 'incident_photos';

    protected $fillable = [
        'incident_id',
        'photo_path',
    ];

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class, 'incident_id');
    }
}
