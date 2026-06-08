<?php

namespace App\Enums;

enum ItemStatus: string
{
    case Pending          = 'pending';
    case Quoting          = 'quoting';
    case AwaitingPayment  = 'awaitingPayment';
    case AwaitingDelivery = 'awaitingDelivery';
    case Received         = 'received';
    case Cancelled        = 'cancelled';
    case CancelRequested  = 'cancelRequested';

    public function label(): string
    {
        return match($this) {
            self::Pending          => 'Pendente',
            self::Quoting          => 'Em Cotação',
            self::AwaitingPayment  => 'Aguardando Pagamento',
            self::AwaitingDelivery => 'Aguardando Entrega',
            self::Received         => 'Recebido',
            self::Cancelled        => 'Cancelado',
            self::CancelRequested  => 'Cancelamento Solicitado',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Pending          => 'cs-badge-pending',
            self::Quoting          => 'cs-badge-quoting',
            self::AwaitingPayment  => 'cs-badge-awaitingPayment',
            self::AwaitingDelivery => 'cs-badge-awaitingDelivery',
            self::Received         => 'cs-badge-completed',
            self::Cancelled        => 'cs-badge-cancelled',
            self::CancelRequested  => 'cs-badge-cancelRequested',
        };
    }

    public function nextStatus(): ?self
    {
        return match($this) {
            self::Pending          => self::Quoting,
            self::Quoting          => self::AwaitingPayment,
            self::AwaitingPayment  => self::AwaitingDelivery,
            self::AwaitingDelivery => self::Received,
            default                => null,
        };
    }
}
