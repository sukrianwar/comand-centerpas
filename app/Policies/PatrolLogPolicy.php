<?php

namespace App\Policies;

use App\Models\User;

class PatrolLogPolicy
{
    /**
     * Role yang boleh memonitor seluruh riwayat patroli di Command Center.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Super Admin', 'Admin', 'Supervisor');
    }
}
