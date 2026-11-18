<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\KasLaci;
use App\Services\Concerns\HandlesDatabaseExceptions;
use Carbon\Carbon;
use DomainException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LaciService
{
    use HandlesDatabaseExceptions;

    /**
     * Ambil ringkasan saldo laci aktif (belum ditutup/reset).
     *
     * @return array{saldo_cash: int, saldo_transfer: int, saldo_total: int, jumlah_transaksi: int}
     */
    public function getSaldoAktif(): array
    {
        $row = DB::table('v_saldo_laci_aktif')->first();

        return [
            'saldo_cash' => (int) ($row->saldo_cash ?? 0),
            'saldo_transfer' => (int) ($row->saldo_transfer ?? 0),
            'saldo_total' => (int) ($row->saldo_total ?? 0),
            'jumlah_transaksi' => (int) ($row->jumlah_transaksi ?? 0),
        ];
    }

    /**
     * Ambil daftar transaksi aktif terbaru di laci.
     */
    public function getTransaksiTerbaru(int $limit = 10): Collection
    {
        return KasLaci::query()
            ->aktif()
            ->latest('id')
            ->take($limit)
            ->get();
    }

    /**
     * Ambil riwayat penutupan laci 7 hari / 7 entri terakhir.
     */
    public function getRiwayat7Hari(int $limit = 7): Collection
    {
        return DB::table('v_riwayat_saldo_laci')
            ->orderByDesc('penutupan_id')
            ->take($limit)
            ->get();
    }

    /**
     * Eksekusi reset laci manual oleh user (kasir/owner).
     *
     * @return array{penutupan_id: int, tanggal: string, total_cash: int, total_transfer: int, total: int}
     *
     * @throws DomainException
     */
    public function resetManual(int $userId): array
    {
        return $this->executeWithExceptionHandling(function () use ($userId) {
            $results = DB::select('CALL sp_reset_laci(?, ?)', ['manual', $userId]);
            $first = $results[0] ?? null;

            return [
                'penutupan_id' => (int) ($first->penutupan_id ?? 0),
                'tanggal' => (string) ($first->tanggal ?? now()->toDateString()),
                'total_cash' => (int) ($first->total_cash ?? 0),
                'total_transfer' => (int) ($first->total_transfer ?? 0),
                'total' => (int) ($first->total ?? 0),
            ];
        });
    }

    /**
     * Eksekusi reset laci otomatis pukul 00:00 (user_id = NULL).
     *
     * @return array{penutupan_id: int, tanggal: string, total_cash: int, total_transfer: int, total: int}
     *
     * @throws DomainException
     */
    public function resetOtomatis(): array
    {
        return $this->executeWithExceptionHandling(function () {
            $results = DB::select('CALL sp_reset_laci(?, ?)', ['otomatis', null]);
            $first = $results[0] ?? null;

            return [
                'penutupan_id' => (int) ($first->penutupan_id ?? 0),
                'tanggal' => (string) ($first->tanggal ?? now()->toDateString()),
                'total_cash' => (int) ($first->total_cash ?? 0),
                'total_transfer' => (int) ($first->total_transfer ?? 0),
                'total' => (int) ($first->total ?? 0),
            ];
        });
    }

    /**
     * Ambil data penjualan mingguan untuk stacked bar chart (Senin s.d. Minggu).
     *
     * @return array{
     *     rentang_label: string,
     *     tanggal_mulai: string,
     *     tanggal_selesai: string,
     *     total_penjualan: int,
     *     total_cash: int,
     *     total_transfer: int,
     *     hari_tertinggi: array{hari: string, tanggal: string, total: int}|null,
     *     dibanding_minggu_lalu: array{persen: float, tren: string, selisih: int, total_lalu: int},
     *     hari: array<int, array{nama_hari: string, label: string, tanggal: string, total_cash: int, total_transfer: int, total: int}>
     * }
     */
    public function getGrafikMingguan(string $mode = 'minggu_ini', ?string $customTanggal = null): array
    {
        $timezone = 'Asia/Jakarta';
        $now = Carbon::now($timezone);

        if ($mode === 'minggu_lalu') {
            $mulai = $now->copy()->subWeek()->startOfWeek(Carbon::MONDAY);
        } elseif ($mode === 'pilih_tanggal' && $customTanggal) {
            $parsed = Carbon::parse($customTanggal, $timezone);
            $mulai = $parsed->copy()->startOfWeek(Carbon::MONDAY);
        } else {
            $mulai = $now->copy()->startOfWeek(Carbon::MONDAY);
        }

        $selesai = $mulai->copy()->endOfWeek(Carbon::SUNDAY);

        // Ambil data penjualan per hari dari view v_penjualan_harian
        $penjualanPerHari = DB::table('v_penjualan_harian')
            ->whereBetween('tanggal', [$mulai->toDateString(), $selesai->toDateString()])
            ->get()
            ->keyBy('tanggal');

        $namaHariMap = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ];

        $hariData = [];
        $totalPenjualan = 0;
        $totalCash = 0;
        $totalTransfer = 0;
        $maxHari = null;
        $maxNominal = -1;

        for ($i = 0; $i < 7; $i++) {
            $curr = $mulai->copy()->addDays($i);
            $tglStr = $curr->toDateString();
            $row = $penjualanPerHari->get($tglStr);

            $cash = (int) ($row->total_cash ?? 0);
            $transfer = (int) ($row->total_transfer ?? 0);
            $total = (int) ($row->total_penjualan ?? 0);

            $namaHari = $namaHariMap[$curr->format('l')] ?? $curr->format('l');

            $hariData[] = [
                'nama_hari' => $namaHari,
                'label' => $namaHari.' ('.$curr->format('d/m').')',
                'tanggal' => $tglStr,
                'total_cash' => $cash,
                'total_transfer' => $transfer,
                'total' => $total,
            ];

            $totalPenjualan += $total;
            $totalCash += $cash;
            $totalTransfer += $transfer;

            if ($total > $maxNominal) {
                $maxNominal = $total;
                $maxHari = [
                    'hari' => $namaHari,
                    'tanggal' => $curr->format('d M Y'),
                    'total' => $total,
                ];
            }
        }

        // Hitung pembanding 7 hari sebelumnya
        $mulaiLalu = $mulai->copy()->subDays(7);
        $selesaiLalu = $selesai->copy()->subDays(7);
        $totalLalu = (int) DB::table('v_penjualan_harian')
            ->whereBetween('tanggal', [$mulaiLalu->toDateString(), $selesaiLalu->toDateString()])
            ->sum('total_penjualan');

        $selisih = $totalPenjualan - $totalLalu;
        if ($totalLalu === 0) {
            $persen = $totalPenjualan > 0 ? 100.0 : 0.0;
            $tren = $totalPenjualan > 0 ? 'meningkat' : 'stabil';
        } else {
            $persen = round((($totalPenjualan - $totalLalu) / $totalLalu) * 100, 1);
            $tren = $persen > 0 ? 'meningkat' : ($persen < 0 ? 'menurun' : 'stabil');
        }

        $rentangLabel = $mulai->format('d M').' – '.$selesai->format('d M Y');

        return [
            'rentang_label' => $rentangLabel,
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $selesai->toDateString(),
            'total_penjualan' => $totalPenjualan,
            'total_cash' => $totalCash,
            'total_transfer' => $totalTransfer,
            'hari_tertinggi' => $maxNominal > 0 ? $maxHari : null,
            'dibanding_minggu_lalu' => [
                'persen' => abs($persen),
                'tren' => $tren,
                'selisih' => $selisih,
                'total_lalu' => $totalLalu,
            ],
            'hari' => $hariData,
        ];
    }

    /**
     * Ambil riwayat saldo laci (penutupan_laci) dengan filter rentang tanggal dan pagination.
     */
    public function getRiwayatSaldoLaci(?string $tanggalAwal = null, ?string $tanggalAkhir = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = DB::table('v_riwayat_saldo_laci');

        if (! empty($tanggalAwal)) {
            $query->whereDate('tanggal', '>=', $tanggalAwal);
        }

        if (! empty($tanggalAkhir)) {
            $query->whereDate('tanggal', '<=', $tanggalAkhir);
        }

        return $query->orderByDesc('penutupan_id')->paginate($perPage);
    }

    /**
     * Ambil rincian lengkap satu sesi penutupan laci beserta seluruh transaksi kas_laci di dalamnya.
     */
    public function getRincianPenutupan(int $penutupanId): ?array
    {
        $penutupan = DB::table('v_riwayat_saldo_laci')
            ->where('penutupan_id', $penutupanId)
            ->first();

        if (! $penutupan) {
            return null;
        }

        $transaksi = KasLaci::query()
            ->where('penutupan_id', $penutupanId)
            ->orderBy('waktu')
            ->get();

        return [
            'penutupan' => $penutupan,
            'transaksi' => $transaksi,
            'jumlah_transaksi' => $transaksi->count(),
        ];
    }
}
