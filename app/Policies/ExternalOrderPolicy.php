<?php

namespace App\Policies;

use App\Models\ExternalOrder;
use App\Models\SupplyRequest;
use App\Models\User;

class ExternalOrderPolicy
{
    public function before(User $user, string $_ability): bool|null
    {
        return $user->isActive ? null : false;
    }

    public function create(User $user, SupplyRequest $sr): bool
    {
        return $user->isBuyerOrAdmin();
    }

    public function delete(User $user, ExternalOrder $order): bool
    {
        return $user->isBuyerOrAdmin();
    }

    public function download(User $user, ExternalOrder $order): bool
    {
        $sr = $order->supplyRequest;
        return $user->isBuyerOrAdmin() || $sr->user_id === $user->id;
    }
}
