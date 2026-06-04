<?php

namespace App\Jobs;

use App\Mail\AwaitingDeliveryMail;
use App\Models\SupplyRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class SendAwaitingDeliveryNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private int $supplyRequestId,
        private string $cacheKey,
        private \Carbon\Carbon $dispatchedAt,
    ) {}

    public function handle(): void
    {
        $lastTrigger = Cache::get($this->cacheKey);

        // Se houve um trigger mais recente após este job ser despachado, abortar
        if ($lastTrigger && $lastTrigger->gt($this->dispatchedAt)) {
            return;
        }

        $sr = SupplyRequest::with(['user', 'items.item'])->find($this->supplyRequestId);

        if (!$sr || !$sr->user) {
            return;
        }

        $awaitingItems = $sr->items->filter(
            fn($i) => $i->status->value === 'awaitingDelivery'
        );

        if ($awaitingItems->isEmpty()) {
            return;
        }

        Mail::to($sr->user->email)->send(new AwaitingDeliveryMail($sr, $awaitingItems));

        Cache::forget($this->cacheKey);
    }
}
