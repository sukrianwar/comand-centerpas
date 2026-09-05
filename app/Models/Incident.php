<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Incident extends Model
{
    protected $table = 'incidents';

    protected $fillable = [
        'patrol_session_id',
        'user_id',
        'area_id',
        'title',
        'category',
        'description',
        'photo',
        'video',
        'latitude',
        'longitude',
        'priority',
        'status',
        'approved_by',
        'approved_at',
        'approval_notes',
        'sync_status',
        'reported_at',
    ];

    protected $casts = [
        'latitude'    => 'decimal:7',
        'longitude'   => 'decimal:7',
        'reported_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public const CATEGORIES = [
        'kebakaran'          => 'Kebakaran',
        'kebocoran'          => 'Kebocoran',
        'orang_mencurigakan' => 'Orang Mencurigakan',
        'kerusakan'          => 'Kerusakan',
        'kecelakaan'         => 'Kecelakaan',
        'lainnya'            => 'Lainnya',
    ];

    public const STATUSES = [
        'new'                 => 'New',
        'in_review'           => 'In Review',
        'approved'            => 'Approved',
        'rejected'            => 'Rejected',
        'follow_up_required'  => 'Follow-up Required',
        'closed'              => 'Closed',
    ];

    public const PRIORITIES = [
        'critical' => 'Critical',
        'high'     => 'High',
        'medium'   => 'Medium',
        'low'      => 'Low',
    ];

    // ─── Relasi ──────────────────────────────────────────────────────────────

    /**
     * Petugas pelapor insiden.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Alias relasi pelapor — dipakai di view dashboard.
     */
    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    /**
     * User (Supervisor/Admin) yang menyetujui insiden.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Riwayat perubahan status insiden.
     */
    public function statusHistory(): HasMany
    {
        return $this->hasMany(IncidentStatusHistory::class, 'incident_id');
    }
}
