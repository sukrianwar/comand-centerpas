<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canManageMasterData();
    }

    public function view(User $user, User $petugas): bool
    {
        return $user->canManageMasterData();
    }

    public function create(User $user): bool
    {
        return $user->canManageMasterData();
    }

    public function update(User $user, User $petugas): bool
    {
        return $user->canManageMasterData();
    }

    public function delete(User $user, User $petugas): bool
    {
        return $user->canManageMasterData();
    }

    public function toggleStatus(User $user, User $petugas): bool
    {
        return $user->canManageMasterData();
    }
}
