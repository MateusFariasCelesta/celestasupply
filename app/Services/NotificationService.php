<?php

namespace App\Services;

use App\Mail\AwaitingDeliveryMail;
use App\Mail\CancellationRequestedMail;
use App\Mail\RequestCancelledMail;
use App\Mail\RequestCompletedMail;
use App\Mail\RequestSubmittedBuyerMail;
use App\Mail\RequestSubmittedRequesterMail;
use App\Models\SupplyRequest;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    private function buyersAddress(): string
    {
        return env('MAIL_BUYERS_ADDRESS', config('mail.from.address'));
    }

    public function notifySubmitted(SupplyRequest $sr): void
    {
        $sr->loadMissing(['user', 'costCenter', 'items.item']);

        Mail::to($sr->user->email)->send(new RequestSubmittedRequesterMail($sr));
        Mail::to($this->buyersAddress())->send(new RequestSubmittedBuyerMail($sr));
    }

    public function notifyCompleted(SupplyRequest $sr): void
    {
        $sr->loadMissing(['user', 'costCenter', 'items']);

        Mail::to($sr->user->email)->send(new RequestCompletedMail($sr));
    }

    public function notifyCancelled(SupplyRequest $sr): void
    {
        $sr->loadMissing(['user', 'costCenter']);

        Mail::to($sr->user->email)->send(new RequestCancelledMail($sr));
    }

    public function notifyCancellationRequested(SupplyRequest $sr): void
    {
        $sr->loadMissing(['user', 'costCenter']);

        Mail::to($this->buyersAddress())->send(new CancellationRequestedMail($sr));
    }

    public function notifyAwaitingDelivery(SupplyRequest $sr): void
    {
        $sr->loadMissing(['user', 'items.item']);

        $awaitingItems = $sr->items->filter(
            fn($i) => $i->status->value === 'awaitingDelivery'
        );

        if ($awaitingItems->isEmpty()) return;

        Mail::to($sr->user->email)->send(new AwaitingDeliveryMail($sr, $awaitingItems));
    }
}
