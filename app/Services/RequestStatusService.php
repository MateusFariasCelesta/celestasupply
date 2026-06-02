<?php

namespace App\Services;

use App\Enums\ItemStatus;
use App\Enums\RequestStatus;
use App\Models\RequestStatusHistory;
use App\Models\SupplyRequest;
use App\Models\User;

class RequestStatusService
{
    public const TRANSITIONS = [
        'pending'    => RequestStatus::InProgress,
        'inProgress' => RequestStatus::Completed,
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

        if ($next === RequestStatus::Completed) {
            $blocked = $sr->items()
                ->whereNotIn('status', [ItemStatus::Received->value, ItemStatus::Cancelled->value])
                ->count();

            if ($blocked > 0) {
                throw new \LogicException(
                    "Há {$blocked} item(ns) que ainda não foram recebidos ou cancelados. Conclua ou cancele todos os itens antes de fechar a solicitação."
                );
            }
        }

        $this->transition($sr, $next, $actor);
    }

    public function jumpToStatus(SupplyRequest $sr, RequestStatus $to, User $actor): void
    {
        $this->transition($sr, $to, $actor);
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

        $this->transition($sr, $restoreTo, $actor);
    }

    private function transition(SupplyRequest $sr, RequestStatus $to, User $actor): void
    {
        $from = $sr->status;

        $this->cascadeItemStatuses($sr, $to);

        $keepReason = in_array($to, [RequestStatus::Cancelled, RequestStatus::CancelRequested]);

        $sr->update([
            'previous_status'     => $from->value,
            'status'              => $to,
            'cancellation_reason' => $keepReason ? $sr->cancellation_reason : null,
        ]);

        RequestStatusHistory::create([
            'supply_request_id' => $sr->id,
            'from_status'       => $from->value,
            'to_status'         => $to->value,
            'changed_by'        => $actor->id,
        ]);
    }

    private function cascadeItemStatuses(SupplyRequest $sr, RequestStatus $to): void
    {
        $cascade = match($to) {
            RequestStatus::Completed  => [
                ['statuses' => ['pending', 'quoting', 'awaitingPayment', 'awaitingDelivery'], 'target' => ItemStatus::Received],
            ],
            RequestStatus::Cancelled  => [
                ['statuses' => ['pending', 'quoting', 'awaitingPayment'], 'target' => ItemStatus::Cancelled],
            ],
            default => [],
        };

        foreach ($cascade as $rule) {
            $sr->items()
                ->whereIn('status', $rule['statuses'])
                ->update(['status' => $rule['target']->value]);
        }
    }
}
