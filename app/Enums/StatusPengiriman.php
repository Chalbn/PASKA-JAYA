<?php

namespace App\Enums;

enum StatusPengiriman: string
{
    case DikirimKeKaryawan = 'dikirim_ke_karyawan';
    case SedangDisiapkan = 'sedang_disiapkan';
    case SiapBerangkat = 'siap_berangkat';
    case Selesai = 'selesai';
    case Dibatalkan = 'dibatalkan';

    public function label(): string
    {
        return match ($this) {
            self::DikirimKeKaryawan => 'Dikirim ke Karyawan',
            self::SedangDisiapkan => 'Sedang Disiapkan',
            self::SiapBerangkat => 'Siap Berangkat',
            self::Selesai => 'Selesai',
            self::Dibatalkan => 'Dibatalkan',
        };
    }

    /**
     * Status yang menandakan pengiriman masih aktif (belum selesai/batal).
     */
    public function aktif(): bool
    {
        return ! in_array($this, [self::Selesai, self::Dibatalkan]);
    }
}
