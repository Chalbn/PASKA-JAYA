<?php

namespace App\Enums;

enum TipePenjualan: string
{
    case Langsung = 'langsung';
    case Bon = 'bon';

    public function label(): string
    {
        return match ($this) {
            self::Langsung => 'Langsung',
            self::Bon => 'Bon',
        };
    }
}
