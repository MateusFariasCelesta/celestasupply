<?php

namespace App\Mail;

use App\Models\SupplyRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class AwaitingDeliveryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SupplyRequest $supplyRequest,
        public Collection $awaitingItems,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[{$this->supplyRequest->code}] Itens aguardando entrega",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.awaiting-delivery');
    }
}
