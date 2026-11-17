<?php

use Carbon\Carbon;

if (! function_exists('rupiah')) {
    /**
     * Format angka ke format Rupiah Indonesia.
     * Contoh: rupiah(1820000) → "Rp 1.820.000"
     */
    function rupiah(int|float $angka): string
    {
        return 'Rp '.number_format((float) $angka, 0, ',', '.');
    }
}

if (! function_exists('tanggal_id')) {
    /**
     * Format tanggal ke format Indonesia.
     * Contoh: tanggal_id('2026-11-14') → "14 November 2026"
     */
    function tanggal_id(mixed $tanggal): string
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $date = Carbon::parse($tanggal);

        return $date->day.' '.$bulan[$date->month].' '.$date->year;
    }
}

if (! function_exists('inisial')) {
    /**
     * Ambil inisial dari nama (maks 2 huruf).
     * Contoh: inisial('Vascha Lumbantoruan') → "VL"
     */
    function inisial(string $nama): string
    {
        $kata = preg_split('/\s+/', trim($nama));

        if (count($kata) === 1) {
            return mb_strtoupper(mb_substr($kata[0], 0, 2));
        }

        return mb_strtoupper(mb_substr($kata[0], 0, 1).mb_substr(end($kata), 0, 1));
    }
}
