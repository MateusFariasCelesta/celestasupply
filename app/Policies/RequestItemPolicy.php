<?php

namespace App\Policies;

use App\Enums\ItemStatus;
use App\Enums\RequestStatus;
use App\Models\SupplyRequest;
use App\Models\SupplyRequestItem;
use App\Models\User;

class RequestItemPolicy
{
    public function before(User $user, string $_ability): bool|null
    {
        if (!$user->isActive) {
            return false;
        }
        return null;
    }

    public function updateStatus(User $user, SupplyRequestItem $item): bool
    {
        if (!$user->isBuyerOrAdmin() || $item->status->nextStatus() === null) {
            return false;
        }

        // Request cancelado: só itens já pagos continuam modificáveis
        if ($item->supplyRequest->status === RequestStatus::Cancelled) {
            return $this->isPurchasedOrAbove($item->status);
        }

        return $item->supplyRequest->status !== RequestStatus::Completed;
    }

    public function setSupplier(User $user, SupplyRequestItem $item): bool
    {
        if (!$user->isBuyerOrAdmin() || $item->status === ItemStatus::Cancelled) {
            return false;
        }

        if ($item->supplyRequest->status === RequestStatus::Cancelled) {
            return $this->isPurchasedOrAbove($item->status);
        }

        return $item->supplyRequest->status !== RequestStatus::Completed;
    }

    public function cancel(User $user, SupplyRequestItem $item): bool
    {
        $uncancellable = [ItemStatus::Received, ItemStatus::Cancelled, ItemStatus::CancelRequested];
        $closedRequest = [RequestStatus::Completed, RequestStatus::Cancelled];

        return $user->isBuyerOrAdmin()
            && !in_array($item->status, $uncancellable)
            && !in_array($item->supplyRequest->status, $closedRequest);
    }

    public function requestCancellation(User $user, SupplyRequestItem $item): bool
    {
        $blocked       = [ItemStatus::Received, ItemStatus::Cancelled, ItemStatus::CancelRequested];
        $closedRequest = [RequestStatus::Completed, RequestStatus::Cancelled];

        return $item->supplyRequest->user_id === $user->id
            && !in_array($item->status, $blocked)
            && !in_array($item->supplyRequest->status, $closedRequest);
    }

    public function approveCancellation(User $user, SupplyRequestItem $item): bool
    {
        return $user->isBuyerOrAdmin() && $item->status === ItemStatus::CancelRequested;
    }

    public function refuseCancellation(User $user, SupplyRequestItem $item): bool
    {
        return $user->isBuyerOrAdmin() && $item->status === ItemStatus::CancelRequested;
    }

    public function registerDelivery(User $user, SupplyRequestItem $item): bool
    {
        return $user->isBuyerOrAdmin()
            && $item->status === ItemStatus::AwaitingDelivery
            && $item->supplyRequest->status === RequestStatus::InProgress;
    }

    public function jumpStatus(User $user, SupplyRequestItem $_item): bool
    {
        return $user->isAdmin();
    }

    private function isPurchasedOrAbove(ItemStatus $status): bool
    {
        return in_array($status, [
            ItemStatus::AwaitingDelivery,
            ItemStatus::Received,
        ]);
    }
}
