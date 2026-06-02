<?php

namespace App\Services;

use App\Enums\RequestStatus;
use App\Models\RequestStatusHistory;
use App\Models\SupplyRequest;
use App\Models\User;

class RequestStatusService
{
    public const TRANSITIONS = [
        'pending'         => RequestStatus::Quoting,
        'quoting'         => RequestStatus::AwaitingPayment,
        'awaitingPayment' => RequestStatus::AwaitingPickup,
        'awaitingPickup'  => RequestStatus::Review,
        'review'          => RequestStatus::Completed,
    ];

    public function canAdvance(SupplyRequest $sr): bool
    {
        return isset(self::TRANSITIONS[$sr->status->value]);
    }

    public function advance(SupplyRequest $sr, User $actor): void
    {
        $next = self::TRANSITIONS[$sr->status->value] ?? null;

        if (!$next) {
            throw new \LogicException("Não há transição disponível a partir de '{$sr->status->value}'.");
        }

        $this->transition($sr, $next, $actor);
    }

    public function submit(SupplyRequest $sr, User $actor): void
    {
        $this->transition($sr, RequestStatus::Pending, $actor);
    }

    public function requestCancellation(SupplyRequest $sr, User $actor, string $reason): void
    {
        $sr->update(['cancellation_reason' => $reason]);
        $this->transition($sr, RequestStatus::CancelRequested, $actor);
    }

    public function approveCancellation(SupplyRequest $sr, User $actor): void
    {
        $this->transition($sr, RequestStatus::Cancelled, $actor);
    }

    public function refuseCancellation(SupplyRequest $sr, User $actor): void
    {
        $restoreTo = $sr->previous_status
            ? RequestStatus::from($sr->previous_status)
            : RequestStatus::Pending;

        $sr->update(['cancellation_reason' => null]);
        $this->transition($sr, $restoreTo, $actor);
    }

    private function transition(SupplyRequest $sr, RequestStatus $to, User $actor): void
    {
        $from = $sr->status;

        $sr->update([
            'previous_status' => $from->value,
            'status'          => $to,
        ]);

        RequestStatusHistory::create([
            'supply_request_id' => $sr->id,
            'from_status'       => $from->value,
            'to_status'         => $to->value,
            'changed_by'        => $actor->id,
        ]);
    }
}
