<?php

namespace App\Enums;

enum RequestStatus: string
{
    case Draft           = 'draft';
    case Pending         = 'pending';
    case Quoting         = 'quoting';
    case AwaitingPayment = 'awaitingPayment';
    case AwaitingPickup  = 'awaitingPickup';
    case Review          = 'review';
    case CancelRequested = 'cancelRequested';
    case Completed       = 'completed';
    case Cancelled       = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Draft           => 'Rascunho',
            self::Pending         => 'Pendente',
            self::Quoting         => 'Cotando',
            self::AwaitingPayment => 'Aguardando Pagamento',
            self::AwaitingPickup  => 'Aguardando Retirada',
            self::Review          => 'Em Revisão',
            self::CancelRequested => 'Cancelamento Solicitado',
            self::Completed       => 'Concluído',
            self::Cancelled       => 'Cancelado',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Draft           => 'cs-badge-draft',
            self::Pending         => 'cs-badge-pending',
            self::Quoting         => 'cs-badge-quoting',
            self::AwaitingPayment => 'cs-badge-awaitingPayment',
            self::AwaitingPickup  => 'cs-badge-awaitingPickup',
            self::Review          => 'cs-badge-review',
            self::CancelRequested => 'cs-badge-cancelRequested',
            self::Completed       => 'cs-badge-completed',
            self::Cancelled       => 'cs-badge-cancelled',
        };
    }
}
