<?php

namespace App\Policies;

use App\Models\User;

class IncidentPolicy
{
    /**
     * Role yang boleh melihat daftar laporan insiden.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Super Admin', 'Admin', 'Supervisor');
    }

    /**
     * Hanya Security Guard yang boleh membuat laporan dari portal member.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Security Guard');
    }
}
