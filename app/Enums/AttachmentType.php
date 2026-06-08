<?php

namespace App\Enums;

enum AttachmentType: string
{
    case Quote   = 'quote';
    case Invoice = 'invoice';
    case Receipt = 'receipt';
    case Other   = 'other';

    public function label(): string
    {
        return match($this) {
            self::Quote   => 'Orçamento',
            self::Invoice => 'Nota Fiscal',
            self::Receipt => 'Comprovante',
            self::Other   => 'Outro',
        };
    }
}
