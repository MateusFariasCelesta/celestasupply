<?php

namespace App\Enums;

enum RequestStatus: string
{
    case Draft           = 'draft';
    case Pending         = 'pending';
    case InProgress      = 'inProgress';
    case Completed       = 'completed';
    case Cancelled       = 'cancelled';
    case CancelRequested = 'cancelRequested';

    public function label(): string
    {
        return match($this) {
            self::Draft           => 'Rascunho',
            self::Pending         => 'Pendente',
            self::InProgress      => 'Em Andamento',
            self::Completed       => 'Concluído',
            self::Cancelled       => 'Cancelado',
            self::CancelRequested => 'Cancelamento Solicitado',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Draft           => 'cs-badge-draft',
            self::Pending         => 'cs-badge-pending',
            self::InProgress      => 'cs-badge-inProgress',
            self::Completed       => 'cs-badge-completed',
            self::Cancelled       => 'cs-badge-cancelled',
            self::CancelRequested => 'cs-badge-cancelRequested',
        };
    }
}
