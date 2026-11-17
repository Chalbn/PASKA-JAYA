<?php

namespace App\Support;

/**
 * Normalisasi nomor HP Indonesia ke format lokal `08xxxxxxxxxx`
 * agar `users.no_hp` (UNIQUE) konsisten dan login dengan nomor HP selalu cocok,
 * apa pun cara pengguna mengetiknya (0812 3456 7890, +62 812-3456-7890, 6281234567890).
 */
class NomorHp
{
    public static function normalisasi(string $nomor): string
    {
        $bersih = preg_replace('/[\s\-().]/', '', trim($nomor)) ?? '';

        if (str_starts_with($bersih, '+62')) {
            return '0'.substr($bersih, 3);
        }

        if (str_starts_with($bersih, '62')) {
            return '0'.substr($bersih, 2);
        }

        return $bersih;
    }

    /**
     * Format seluler Indonesia: 08 + digit 1-9 + 7 sampai 11 digit.
     */
    public static function valid(string $nomor): bool
    {
        return (bool) preg_match('/^08[1-9][0-9]{7,11}$/', self::normalisasi($nomor));
    }
}
