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

        Mail::mailer('mailjet-api')->to($sr->user->email)->queue(new RequestSubmittedRequesterMail($sr));
        Mail::mailer('mailjet-api')->to($this->buyersAddress())->queue(new RequestSubmittedBuyerMail($sr));
    }

    public function notifyCompleted(SupplyRequest $sr): void
    {
        $sr->loadMissing(['user', 'costCenter', 'items']);

        Mail::mailer('mailjet-api')->to($sr->user->email)->queue(new RequestCompletedMail($sr));
    }

    public function notifyCancelled(SupplyRequest $sr): void
    {
        $sr->loadMissing(['user', 'costCenter']);

        Mail::mailer('mailjet-api')->to($sr->user->email)->queue(new RequestCancelledMail($sr));
    }

    public function notifyCancellationRequested(SupplyRequest $sr): void
    {
        $sr->loadMissing(['user', 'costCenter']);

        Mail::mailer('mailjet-api')->to($this->buyersAddress())->queue(new CancellationRequestedMail($sr));
    }

    public function notifyAwaitingDelivery(SupplyRequest $sr): void
    {
        $sr->loadMissing(['user', 'items.item']);

        $awaitingItems = $sr->items->filter(
            fn($i) => $i->status->value === 'awaitingDelivery'
        );

        if ($awaitingItems->isEmpty()) return;

        Mail::mailer('mailjet-api')->to($sr->user->email)->queue(new AwaitingDeliveryMail($sr, $awaitingItems));
    }
}
