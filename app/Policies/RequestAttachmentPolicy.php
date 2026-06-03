<?php

namespace App\Policies;

use App\Models\RequestAttachment;
use App\Models\SupplyRequest;
use App\Models\User;

class RequestAttachmentPolicy
{
    public function before(User $user, string $_ability): bool|null
    {
        return $user->isActive ? null : false;
    }

    public function create(User $user, SupplyRequest $sr): bool
    {
        return $user->isBuyerOrAdmin();
    }

    public function delete(User $user, RequestAttachment $attachment): bool
    {
        return $user->isBuyerOrAdmin();
    }

    public function download(User $user, RequestAttachment $attachment): bool
    {
        $sr = $attachment->supplyRequest;
        return $user->isBuyerOrAdmin() || $sr->user_id === $user->id;
    }
}
