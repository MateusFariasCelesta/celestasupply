<?php

namespace App\Policies;

use App\Models\ItemAttachment;
use App\Models\SupplyRequestItem;
use App\Models\User;

class ItemAttachmentPolicy
{
    public function before(User $user, string $_ability): bool|null
    {
        return $user->isActive ? null : false;
    }

    public function create(User $user, SupplyRequestItem $item): bool
    {
        return $user->isBuyerOrAdmin();
    }

    public function delete(User $user, ItemAttachment $attachment): bool
    {
        return $user->isBuyerOrAdmin();
    }

    public function download(User $user, ItemAttachment $attachment): bool
    {
        $sr = $attachment->item->supplyRequest;
        return $user->isBuyerOrAdmin() || $sr->user_id === $user->id;
    }
}
