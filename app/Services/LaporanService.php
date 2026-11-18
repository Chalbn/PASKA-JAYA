<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DetailPenjualan;
use App\Services\Concerns\HandlesDatabaseExceptions;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PDO;

class LaporanService
{
    use HandlesDatabaseExceptions;

    private const ZONA_WAKTU = 'Asia/Jakarta';

    /**
     * Ambil seluruh data untuk Ringkasan Toko (F05) dari view database nyata.
     *
     * @return array{
     *     penjualan_hari_ini: int,
     *     transaksi_hari_ini: int,
     *     saldo_cash: int,
     *     saldo_transfer: int,
     *     stok_menipis_count: int,
     *     stok_menipis_daftar: Collection<int, object>,
     *     bon_belum_lunas_total: int,
     *     bon_belum_lunas_pelanggan_count: int,
     *     bon_belum_lunas_daftar: Collection<int, object>,
     *     transaksi_terbaru: Collection<int, object>,
     *     grafik_7_hari: array<int, array{tanggal: string, hari: string, total: int, label_singkat: string}>
     * }
     */
    public function ringkasanToko(): array
    {
        $sekarang = Carbon::now(self::ZONA_WAKTU);
        $hariIni = $sekarang->toDateString();

        // Penjualan langsung hari ini dari v_penjualan_harian
        $penjualanHariIniRow = DB::table('v_penjualan_harian')
            ->where('tanggal', $hariIni)
            ->first();

        // Saldo kas laci aktif dari v_saldo_laci_aktif
        $saldoLaciRow = DB::table('v_saldo_laci_aktif')->first();

        // Stok menipis dari v_stok_menipis
        $stokMenipisCount = (int) DB::table('v_stok_menipis')->count();
        $stokMenipisDaftar = DB::table('v_stok_menipis')
            ->orderBy('stok')
            ->orderBy('nama_barang')
            ->limit(5)
            ->get();

        // Bon belum lunas dari v_piutang_aktif
        $bonBelumLunasTotal = (int) (DB::table('v_piutang_aktif')->sum('sisa') ?? 0);
        $bonPelangganCount = (int) DB::table('v_piutang_aktif')
            ->distinct()
            ->count('pelanggan_id');
        $bonBelumLunasDaftar = DB::table('v_piutang_aktif')
            ->orderBy('jatuh_tempo')
            ->orderByDesc('bon_id')
            ->limit(5)
            ->get();

        // 5 transaksi terbaru dari v_laporan_barang_keluar
        $transaksiTerbaru = DB::table('v_laporan_barang_keluar')
            ->orderByDesc('waktu_transaksi')
            ->orderByDesc('penjualan_id')
            ->limit(5)
            ->get();

        // Grafik 7 hari terakhir (rolling 7 hari s.d. hari ini) dari v_penjualan_harian
        $mulai = $sekarang->copy()->subDays(6)->toDateString();
        $penjualan7Hari = DB::table('v_penjualan_harian')
            ->whereBetween('tanggal', [$mulai, $hariIni])
            ->get()
            ->keyBy('tanggal');

        $hariMap = [
            'Mon' => 'Sen',
            'Tue' => 'Sel',
            'Wed' => 'Rab',
            'Thu' => 'Kam',
            'Fri' => 'Jum',
            'Sat' => 'Sab',
            'Sun' => 'Min',
        ];

        $grafik7Hari = [];
        for ($i = 6; $i >= 0; $i--) {
            $dt = $sekarang->copy()->subDays($i);
            $tgl = $dt->toDateString();
            $row = $penjualan7Hari->get($tgl);
            $total = (int) ($row->total_penjualan ?? 0);

            $grafik7Hari[] = [
                'tanggal' => $tgl,
                'hari' => $hariMap[$dt->format('D')] ?? $dt->format('D'),
                'total' => $total,
                'label_singkat' => $this->formatSingkat($total),
            ];
        }

        return [
            'penjualan_hari_ini' => (int) ($penjualanHariIniRow->total_penjualan ?? 0),
            'transaksi_hari_ini' => (int) ($penjualanHariIniRow->jumlah_transaksi ?? 0),
            'saldo_cash' => (int) ($saldoLaciRow->saldo_cash ?? 0),
            'saldo_transfer' => (int) ($saldoLaciRow->saldo_transfer ?? 0),
            'stok_menipis_count' => $stokMenipisCount,
            'stok_menipis_daftar' => $stokMenipisDaftar,
            'bon_belum_lunas_total' => $bonBelumLunasTotal,
            'bon_belum_lunas_pelanggan_count' => $bonPelangganCount,
            'bon_belum_lunas_daftar' => $bonBelumLunasDaftar,
            'transaksi_terbaru' => $transaksiTerbaru,
            'grafik_7_hari' => $grafik7Hari,
        ];
    }

    /**
     * Ambil laporan barang keluar melalui Stored Procedure sp_laporan_penjualan.
     *
     * @return array{
     *     ringkasan: array{total_penjualan: int, total_cash: int, total_transfer: int, jumlah_transaksi: int},
     *     rows: Collection<int, object>
     * }
     */
    public function ambilLaporanPenjualan(
        ?string $dari = null,
        ?string $sampai = null,
        string $metode = 'semua',
    ): array {
        $sekarang = Carbon::now(self::ZONA_WAKTU);
        $dariDate = $this->normalisasiTanggal($dari, $sekarang->copy()->subDays(6)->toDateString());
        $sampaiDate = $this->normalisasiTanggal($sampai, $sekarang->toDateString());
        $metodeNorm = $this->normalisasiMetode($metode);

        return $this->executeWithExceptionHandling(function () use ($dariDate, $sampaiDate, $metodeNorm): array {
            $pdo = DB::connection()->getPdo();
            $stmt = $pdo->prepare('CALL sp_laporan_penjualan(?, ?, ?)');
            $stmt->execute([$dariDate, $sampaiDate, $metodeNorm]);

            $summaryRow = $stmt->fetch(PDO::FETCH_OBJ);
            $rows = [];

            if ($stmt->nextRowset()) {
                $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
            }

            while ($stmt->nextRowset()) {
                // Kosongkan sisa rowset agar koneksi PDO tetap bersih
            }
            $stmt->closeCursor();

            return [
                'ringkasan' => [
                    'total_penjualan' => (int) ($summaryRow->total_penjualan ?? 0),
                    'total_cash' => (int) ($summaryRow->total_cash ?? 0),
                    'total_transfer' => (int) ($summaryRow->total_transfer ?? 0),
                    'jumlah_transaksi' => (int) ($summaryRow->jumlah_transaksi ?? 0),
                ],
                'rows' => collect($rows),
            ];
        });
    }

    /**
     * Ambil laporan barang keluar dengan paginasi serta detail item tiap transaksi pada halaman aktif.
     *
     * @return array{
     *     ringkasan: array{total_penjualan: int, total_cash: int, total_transfer: int, jumlah_transaksi: int},
     *     transaksi: LengthAwarePaginator<int, object>,
     *     detailsByPenjualanId: Collection<int, Collection<int, DetailPenjualan>>
     * }
     */
    public function laporanPenjualanPaginated(
        ?string $dari = null,
        ?string $sampai = null,
        string $metode = 'semua',
        int $perPage = 10,
        int $page = 1,
        ?string $path = null,
    ): array {
        $hasil = $this->ambilLaporanPenjualan($dari, $sampai, $metode);
        $allRows = $hasil['rows'];

        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $items = $allRows->forPage($page, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $items,
            $allRows->count(),
            $perPage,
            $page,
            [
                'path' => $path ?: Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]
        );

        $penjualanIds = $items->pluck('penjualan_id')->filter()->all();
        $detailsByPenjualanId = empty($penjualanIds)
            ? collect()
            : DetailPenjualan::query()
                ->whereIn('penjualan_id', $penjualanIds)
                ->orderBy('id')
                ->get()
                ->groupBy('penjualan_id');

        return [
            'ringkasan' => $hasil['ringkasan'],
            'transaksi' => $paginator,
            'detailsByPenjualanId' => $detailsByPenjualanId,
        ];
    }

    /**
     * Bangun isi berkas CSV laporan barang keluar sesuai filter aktif.
     */
    public function buatCsv(
        ?string $dari = null,
        ?string $sampai = null,
        string $metode = 'semua',
    ): string {
        $hasil = $this->ambilLaporanPenjualan($dari, $sampai, $metode);
        $rows = $hasil['rows'];

        $penjualanIds = $rows->pluck('penjualan_id')->filter()->all();
        $detailsByPenjualanId = empty($penjualanIds)
            ? collect()
            : DetailPenjualan::query()
                ->whereIn('penjualan_id', $penjualanIds)
                ->orderBy('id')
                ->get()
                ->groupBy('penjualan_id');

        $handle = fopen('php://temp', 'r+b');
        // Tambahkan UTF-8 BOM agar karakter terbaca rapi saat dibuka di Microsoft Excel
        fwrite($handle, "\xEF\xBB\xBF");

        fputcsv($handle, [
            'ID Transaksi',
            'Waktu',
            'Pelanggan',
            'Metode',
            'Kasir',
            'Rincian Barang',
            'Total',
        ]);

        foreach ($rows as $row) {
            $waktuDt = $row->waktu_transaksi
                ? Carbon::parse($row->waktu_transaksi)->timezone(self::ZONA_WAKTU)
                : null;
            $waktuLabel = $waktuDt
                ? tanggal_id($waktuDt).', '.$waktuDt->format('H.i')
                : '-';

            $details = $detailsByPenjualanId->get($row->penjualan_id, collect());
            $rincianStr = $details->isNotEmpty()
                ? $details->map(fn ($d) => sprintf(
                    '%s (%d %s x %s)',
                    $d->nama_barang,
                    $d->jumlah,
                    $d->satuan,
                    rupiah($d->harga_satuan)
                ))->implode('; ')
                : ($row->ringkasan_barang ?? '-');

            fputcsv($handle, [
                (string) ($row->kode_transaksi ?? '-'),
                $waktuLabel,
                (string) ($row->nama_pelanggan ?: 'Umum'),
                strtoupper((string) ($row->metode_bayar ?? '-')),
                (string) ($row->kasir ?? '-'),
                $rincianStr,
                (int) ($row->total ?? 0),
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle) ?: '';
        fclose($handle);

        return $csv;
    }

    /**
     * Ambil daftar bon untuk Owner (read-only) dari view v_daftar_bon beserta rincian item tiap bon.
     *
     * @return array{
     *     daftarBon: LengthAwarePaginator<int, object>,
     *     itemsByPenjualanId: Collection<int, Collection<int, DetailPenjualan>>
     * }
     */
    public function daftarBonOwner(string $filterStatus = 'semua', int $perPage = 10): array
    {
        $query = DB::table('v_daftar_bon');

        if ($filterStatus === 'belum_lunas') {
            $query->where('status_bayar', 'belum_lunas');
        } elseif ($filterStatus === 'lunas') {
            $query->where('status_bayar', 'lunas');
        }

        $daftarBon = $query
            ->orderByDesc('bon_id')
            ->paginate($perPage);

        $penjualanIds = collect($daftarBon->items())->pluck('penjualan_id')->filter()->all();
        $itemsByPenjualanId = empty($penjualanIds)
            ? collect()
            : DetailPenjualan::query()
                ->whereIn('penjualan_id', $penjualanIds)
                ->orderBy('id')
                ->get()
                ->groupBy('penjualan_id');

        return [
            'daftarBon' => $daftarBon,
            'itemsByPenjualanId' => $itemsByPenjualanId,
        ];
    }

    /**
     * Ambil detail lengkap bon via Stored Procedure sp_detail_bon (read-only).
     *
     * @return array{
     *     bon: object,
     *     items: array<int, object>,
     *     logs: array<int, object>,
     *     pembayaran: array<int, object>,
     *     timeline: array<int, array{key: string, title: string, date: ?string, status: string, note: ?string}>
     * }|null
     */
    public function detailBonOwner(int $bonId): ?array
    {
        return $this->executeWithExceptionHandling(function () use ($bonId): ?array {
            $pdo = DB::connection()->getPdo();
            $stmt = $pdo->prepare('CALL sp_detail_bon(?)');
            $stmt->execute([$bonId]);

            $bon = $stmt->fetch(PDO::FETCH_OBJ);
            $items = [];
            $logs = [];
            $pembayaran = [];

            if ($stmt->nextRowset()) {
                $items = $stmt->fetchAll(PDO::FETCH_OBJ);
            }
            if ($stmt->nextRowset()) {
                $logs = $stmt->fetchAll(PDO::FETCH_OBJ);
            }
            if ($stmt->nextRowset()) {
                $pembayaran = $stmt->fetchAll(PDO::FETCH_OBJ);
            }
            while ($stmt->nextRowset()) {
                // Kosongkan sisa rowset
            }
            $stmt->closeCursor();

            if (! $bon) {
                return null;
            }

            $timeline = $this->bangunTimelineStatus($bon, $logs);

            return [
                'bon' => $bon,
                'items' => $items,
                'logs' => $logs,
                'pembayaran' => $pembayaran,
                'timeline' => $timeline,
            ];
        });
    }

    /**
     * Bangun 5 langkah timeline alur status pengiriman bon.
     *
     * @param  array<int, object>  $logs
     * @return array<int, array{key: string, title: string, date: ?string, status: string, note: ?string}>
     */
    private function bangunTimelineStatus(object $bon, array $logs): array
    {
        $langkah = [
            'belum_dikirim' => 'Belum Dikirim',
            'dikirim_ke_karyawan' => 'Dikirim ke Karyawan',
            'sedang_disiapkan' => 'Sedang Disiapkan',
            'siap_berangkat' => 'Siap Berangkat/Sedang Diantar',
            'selesai' => 'Selesai',
        ];

        $urutan = array_keys($langkah);
        $statusSekarang = (string) ($bon->status_pengiriman ?? 'belum_dikirim');
        $indeksSekarang = array_search($statusSekarang, $urutan, true);
        if ($indeksSekarang === false) {
            $indeksSekarang = 0;
        }

        // Ambil timestamp terakhir untuk masing-masing status dari log
        $logPerStatus = [];
        foreach ($logs as $log) {
            $st = (string) ($log->status ?? '');
            $logPerStatus[$st] = $log;
        }

        $timeline = [];
        foreach ($urutan as $idx => $key) {
            $log = $logPerStatus[$key] ?? null;
            $waktuRaw = $log?->waktu ?? ($key === 'belum_dikirim' ? ($bon->created_at ?? null) : null);
            $waktuLabel = null;

            if ($waktuRaw) {
                $dt = Carbon::parse($waktuRaw)->timezone(self::ZONA_WAKTU);
                $waktuLabel = tanggal_id($dt).', '.$dt->format('H.i');
            }

            if ($idx < $indeksSekarang) {
                $stepStatus = 'completed';
            } elseif ($idx === $indeksSekarang) {
                $stepStatus = 'completed';
            } else {
                $stepStatus = 'pending';
            }

            $timeline[] = [
                'key' => $key,
                'title' => $langkah[$key],
                'date' => $waktuLabel,
                'status' => $stepStatus,
                'note' => $log->catatan ?? null,
            ];
        }

        return $timeline;
    }

    /**
     * Format nominal singkat di bawah bar chart (mis. 0, 50 rb, 0,7 jt, 1,8 jt).
     */
    private function formatSingkat(int $nominal): string
    {
        if ($nominal <= 0) {
            return '0';
        }

        if ($nominal >= 100000) {
            $juta = $nominal / 1000000;
            $formatted = rtrim(rtrim(number_format($juta, 1, ',', '.'), '0'), ',');

            return $formatted.' jt';
        }

        if ($nominal >= 1000) {
            return number_format($nominal / 1000, 0, ',', '.').' rb';
        }

        return (string) $nominal;
    }

    private function normalisasiTanggal(?string $tanggal, string $default): string
    {
        if (empty($tanggal)) {
            return $default;
        }

        try {
            return Carbon::parse($tanggal, self::ZONA_WAKTU)->toDateString();
        } catch (\Throwable) {
            return $default;
        }
    }

    private function normalisasiMetode(?string $metode): string
    {
        $val = strtolower(trim((string) $metode));

        return in_array($val, ['cash', 'transfer', 'semua'], true) ? $val : 'semua';
    }
}
