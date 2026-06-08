<?php

namespace App\Policies;

use App\Models\CostCenter;
use App\Models\User;

class CostCenterPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if (!$user->isActive) {
            return false;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isBuyerOrAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isBuyerOrAdmin();
    }

    public function update(User $user, CostCenter $costCenter): bool
    {
        return $user->isBuyerOrAdmin();
    }
}
