<?php

if (! function_exists('rupiah')) {
    /**
     * Format integer Rupiah (ADR-003, UI_UX_GUIDELINES.md)
     * Contoh: rupiah(1820000) => "Rp 1.820.000"
     */
    function rupiah(?int $nominal): string
    {
        return 'Rp ' . number_format($nominal ?? 0, 0, ',', '.');
    }
}

if (! function_exists('tanggal_id')) {
    /**
     * Format tanggal Bahasa Indonesia (Asia/Jakarta)
     * Contoh: "14 November 2026" atau "Sabtu, 14 November 2026"
     */
    function tanggal_id($date = null, bool $denganHari = false, bool $denganJam = false): string
    {
        $dt = $date ? \Illuminate\Support\Carbon::parse($date)->timezone('Asia/Jakarta') : now('Asia/Jakarta');

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
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $namaHari = $hari[$dt->format('l')] ?? '';
        $namaBulan = $bulan[(int) $dt->format('n')] ?? '';

        $format = $dt->format('j') . ' ' . $namaBulan . ' ' . $dt->format('Y');

        if ($denganHari) {
            $format = $namaHari . ', ' . $format;
        }

        if ($denganJam) {
            $format .= ' ' . $dt->format('H:i') . ' WIB';
        }

        return $format;
    }
}

if (! function_exists('inisial')) {
    /**
     * Ambil 1-2 huruf inisial dari nama (uppercase)
     * Contoh: "Paska Jaya" => "PJ", "Admin" => "A"
     */
    function inisial(?string $nama): string
    {
        if (empty($nama)) {
            return 'PJ';
        }

        $words = preg_split('/\s+/', trim($nama));
        $initials = '';
        foreach ($words as $w) {
            if (! empty($w)) {
                $initials .= mb_substr($w, 0, 1);
            }
            if (mb_strlen($initials) >= 2) {
                break;
            }
        }

        return mb_strtoupper($initials);
    }
}
