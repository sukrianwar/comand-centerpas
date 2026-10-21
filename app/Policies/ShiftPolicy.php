<?php

namespace App\Policies;

use App\Models\Shift;
use App\Models\User;

class ShiftPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Shift $shift): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->canManageMasterData();
    }

    public function update(User $user, Shift $shift): bool
    {
        return $user->canManageMasterData();
    }

    public function delete(User $user, Shift $shift): bool
    {
        return $user->canManageMasterData();
    }

    public function restore(User $user, Shift $shift): bool
    {
        return $user->canManageMasterData();
    }

    public function forceDelete(User $user, Shift $shift): bool
    {
        return $user->canManageMasterData();
    }
}
