<?php

namespace App\Mail;

use App\Models\SupplyRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CancellationRequestedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SupplyRequest $supplyRequest) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[{$this->supplyRequest->code}] Cancelamento solicitado",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.cancellation-requested');
    }
}
