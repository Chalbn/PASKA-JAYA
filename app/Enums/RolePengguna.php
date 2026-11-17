<?php

namespace App\Enums;

enum RolePengguna: string
{
    case Admin = 'admin';
    case Owner = 'owner';
    case Kasir = 'kasir';
    case Karyawan = 'karyawan';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Owner => 'Owner',
            self::Kasir => 'Kasir',
            self::Karyawan => 'Karyawan',
        };
    }
}
