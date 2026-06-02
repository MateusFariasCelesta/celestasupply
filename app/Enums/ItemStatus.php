<?php

namespace App\Enums;

enum ItemStatus: string
{
    case Pending            = 'pending';
    case Quoting            = 'quoting';
    case Quoted             = 'quoted';
    case Purchased          = 'purchased';
    case PartiallyDelivered = 'partiallyDelivered';
    case Delivered          = 'delivered';
    case Cancelled          = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Pending            => 'Pendente',
            self::Quoting            => 'Cotando',
            self::Quoted             => 'Cotado',
            self::Purchased          => 'Comprado',
            self::PartiallyDelivered => 'Entregue Parcialmente',
            self::Delivered          => 'Entregue',
            self::Cancelled          => 'Cancelado',
        };
    }
}
