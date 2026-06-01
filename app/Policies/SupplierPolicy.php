<?php

namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;

class SupplierPolicy
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

    public function update(User $user, Supplier $supplier): bool
    {
        return $user->isBuyerOrAdmin();
    }
}
