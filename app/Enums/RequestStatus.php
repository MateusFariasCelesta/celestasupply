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

    public function chartColor(): string
    {
        return match($this) {
            self::Draft           => '#94A3B8',
            self::Pending         => '#3B82F6',
            self::InProgress      => '#F59E0B',
            self::Completed       => '#22C55E',
            self::CancelRequested => '#F43F5E',
            self::Cancelled       => '#EF4444',
        };
    }
}
