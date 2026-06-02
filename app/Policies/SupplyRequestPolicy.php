<?php

namespace App\Policies;

use App\Enums\RequestStatus;
use App\Models\SupplyRequest;
use App\Models\User;
use App\Services\RequestStatusService;

class SupplyRequestPolicy
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
        return true;
    }

    public function view(User $user, SupplyRequest $sr): bool
    {
        return $user->isBuyerOrAdmin() || $sr->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, SupplyRequest $sr): bool
    {
        return $sr->user_id === $user->id && $sr->status === RequestStatus::Draft;
    }

    public function submit(User $user, SupplyRequest $sr): bool
    {
        return $sr->user_id === $user->id && $sr->status === RequestStatus::Draft;
    }

    public function cancelRequest(User $user, SupplyRequest $sr): bool
    {
        $allowed = [RequestStatus::Pending, RequestStatus::Quoting, RequestStatus::AwaitingPayment];
        return $sr->user_id === $user->id && in_array($sr->status, $allowed);
    }

    public function advanceStatus(User $user, SupplyRequest $sr): bool
    {
        if ($user->isBuyerOrAdmin()) {
            return isset(RequestStatusService::TRANSITIONS[$sr->status->value]);
        }

        // Requester pode confirmar recebimento (review → completed)
        return $sr->user_id === $user->id && $sr->status === RequestStatus::Review;
    }

    public function approveCancellation(User $user, SupplyRequest $sr): bool
    {
        return $user->isBuyerOrAdmin() && $sr->status === RequestStatus::CancelRequested;
    }

    public function refuseCancellation(User $user, SupplyRequest $sr): bool
    {
        return $user->isBuyerOrAdmin() && $sr->status === RequestStatus::CancelRequested;
    }
}
