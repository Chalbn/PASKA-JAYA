<?php

namespace App\Enums;

enum StatusBayar: string
{
    case BelumLunas = 'belum_lunas';
    case Lunas = 'lunas';

    public function label(): string
    {
        return match ($this) {
            self::BelumLunas => 'Belum Lunas',
            self::Lunas => 'Lunas',
        };
    }
}
