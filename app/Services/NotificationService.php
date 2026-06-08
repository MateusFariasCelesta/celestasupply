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
    public function __construct(private WhatsAppService $whatsapp) {}

    private function buyersAddress(): string
    {
        return env('MAIL_BUYERS_ADDRESS', config('mail.from.address'));
    }

    private function buyersPhone(): ?string
    {
        return env('WHATSAPP_BUYERS_NUMBER') ?: null;
    }

    private function wa(?string $phone, string $message): void
    {
        if ($phone) {
            $this->whatsapp->send($phone, $message);
        }
    }

    private function waBuyers(string $message): void
    {
        $this->wa($this->buyersPhone(), $message);
    }

    public function notifySubmitted(SupplyRequest $sr): void
    {
        $sr->loadMissing(['user', 'costCenter', 'items.item']);

        Mail::to($sr->user->email)->send(new RequestSubmittedRequesterMail($sr));
        Mail::to($this->buyersAddress())->send(new RequestSubmittedBuyerMail($sr));

        $this->wa(
            $sr->user->whatsapp_phone,
            "✅ *CelestaSupply* — Sua solicitação *{$sr->title}* foi enviada e está aguardando análise."
        );

        $this->waBuyers(
            "📋 *CelestaSupply* — Nova solicitação *{$sr->title}* de *{$sr->user->name}* aguardando análise."
        );
    }

    public function notifyCompleted(SupplyRequest $sr): void
    {
        $sr->loadMissing(['user', 'costCenter', 'items']);

        Mail::to($sr->user->email)->send(new RequestCompletedMail($sr));

        $this->wa(
            $sr->user->whatsapp_phone,
            "🎉 *CelestaSupply* — Sua solicitação *{$sr->title}* foi concluída!"
        );
    }

    public function notifyCancelled(SupplyRequest $sr): void
    {
        $sr->loadMissing(['user', 'costCenter']);

        Mail::to($sr->user->email)->send(new RequestCancelledMail($sr));

        $this->wa(
            $sr->user->whatsapp_phone,
            "❌ *CelestaSupply* — Sua solicitação *{$sr->title}* foi cancelada."
        );
    }

    public function notifyCancellationRequested(SupplyRequest $sr): void
    {
        $sr->loadMissing(['user', 'costCenter']);

        Mail::to($this->buyersAddress())->send(new CancellationRequestedMail($sr));

        $this->waBuyers(
            "⚠️ *CelestaSupply* — *{$sr->user->name}* solicitou o cancelamento de *{$sr->title}*."
        );
    }

    public function notifyAwaitingDelivery(SupplyRequest $sr): void
    {
        $sr->loadMissing(['user', 'items.item']);

        $awaitingItems = $sr->items->filter(
            fn($i) => $i->status->value === 'awaitingDelivery'
        );

        if ($awaitingItems->isEmpty()) return;

        Mail::to($sr->user->email)->send(new AwaitingDeliveryMail($sr, $awaitingItems));

        if ($sr->user->whatsapp_phone) {
            $count    = $awaitingItems->count();
            $label    = $count === 1 ? '1 item aguardando entrega' : "{$count} itens aguardando entrega";
            $itemList = $awaitingItems->map(fn($i) => "• {$i->item->name}")->join("\n");

            $this->wa(
                $sr->user->whatsapp_phone,
                "📦 *CelestaSupply* — Solicitação *{$sr->title}*\n{$label}:\n\n{$itemList}"
            );
        }
    }
}
