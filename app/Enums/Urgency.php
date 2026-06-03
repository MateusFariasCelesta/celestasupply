<?php

namespace App\Enums;

enum Urgency: string
{
    case Low    = 'low';
    case Medium = 'medium';
    case High   = 'high';

    public function label(): string
    {
        return match($this) {
            self::Low    => 'Baixa',
            self::Medium => 'Média',
            self::High   => 'Alta',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Low    => 'cs-badge-low',
            self::Medium => 'cs-badge-medium',
            self::High   => 'cs-badge-high',
        };
    }

    public function sortOrder(): int
    {
        return match($this) {
            self::High   => 3,
            self::Medium => 2,
            self::Low    => 1,
        };
    }
}
