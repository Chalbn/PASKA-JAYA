<?php

use Carbon\Carbon;

if (! function_exists('rupiah')) {
    /**
     * Format angka ke format Rupiah Indonesia.
     * Contoh: rupiah(1820000) → "Rp 1.820.000"
     */
    function rupiah(int|float|null $angka = 0): string
    {
        return 'Rp '.number_format((float) ($angka ?? 0), 0, ',', '.');
    }
}

if (! function_exists('tanggal_id')) {
    /**
     * Format tanggal ke format Indonesia (Asia/Jakarta).
     * Contoh: tanggal_id('2026-11-14') → "14 November 2026"
     *         tanggal_id('2026-11-14', true) → "Sabtu, 14 November 2026"
     */
    function tanggal_id(mixed $tanggal = null, bool $denganHari = false, bool $denganJam = false): string
    {
        $hari = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $date = $tanggal ? Carbon::parse($tanggal)->timezone('Asia/Jakarta') : Carbon::now('Asia/Jakarta');

        $format = $date->day.' '.$bulan[$date->month].' '.$date->year;

        if ($denganHari) {
            $format = ($hari[$date->format('l')] ?? '').', '.$format;
        }

        if ($denganJam) {
            $format .= ' '.$date->format('H:i').' WIB';
        }

        return $format;
    }
}

if (! function_exists('inisial')) {
    /**
     * Ambil inisial dari nama (maks 2 huruf).
     * Contoh: inisial('Vascha Lumbantoruan') → "VL"
     */
    function inisial(?string $nama = null): string
    {
        if (empty($nama)) {
            return 'PJ';
        }

        $kata = preg_split('/\s+/', trim($nama));

        if (count($kata) === 1) {
            return mb_strtoupper(mb_substr($kata[0], 0, 1));
        }

        return mb_strtoupper(mb_substr($kata[0], 0, 1).mb_substr(end($kata), 0, 1));
    }
}
