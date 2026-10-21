<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, \Illuminate\Database\Eloquent\SoftDeletes, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'name',
        'username',
        'email',
        'password',
        'phone',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Get the role that owns the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Cek apakah user memiliki role tertentu.
     */
    public function hasRole(string ...$roleNames): bool
    {
        return in_array($this->role?->role_name, $roleNames, true);
    }

    /**
     * Super Admin memiliki akses tertinggi ke seluruh sistem.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super Admin');
    }

    /**
     * Role yang boleh mengakses Command Center (semua selain Security Guard).
     */
    public function canAccessCommandCenter(): bool
    {
        return ! $this->hasRole('Security Guard');
    }

    /**
     * Role yang boleh mengelola data master (jadwal, area, checkpoint, petugas).
     */
    public function canManageMasterData(): bool
    {
        return $this->hasRole('Super Admin', 'Admin', 'Supervisor');
    }
}
