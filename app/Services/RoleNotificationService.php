<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\MetodeBayar;
use App\Enums\StatusBayar;
use App\Enums\StatusPengiriman;
use App\Models\ActivityLog;
use App\Models\Barang;
use App\Models\BonPiutang;
use App\Models\OwnerNotification;
use App\Models\PengirimanBarang;
use App\Models\Penjualan;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Throwable;

class RoleNotificationService
{
    /**
     * Sinkronisasi notifikasi untuk role tertentu secara idempoten.
     */
    public function syncForRole(string $role, ?int $userId = null): void
    {
        try {
            if (! Schema::hasTable('owner_notifications')) {
                return;
            }
        } catch (Throwable) {
            return;
        }

        $today = now('Asia/Jakarta')->toDateString();

        match (strtolower($role)) {
            'owner' => $this->syncOwner($today),
            'kasir' => $this->syncKasir($today),
            'karyawan' => $this->syncKaryawan($userId),
            'admin' => $this->syncAdmin($today),
            default => null,
        };
    }

    /**
     * Sinkronisasi semua role (dipanggil saat event lintas role terjadi).
     */
    public function syncAll(): void
    {
        $today = now('Asia/Jakarta')->toDateString();
        $this->syncOwner($today);
        $this->syncKasir($today);
        $this->syncKaryawan(null);
        $this->syncAdmin($today);
    }

    private function syncOwner(string $today): void
    {
        try {
            $sales = Penjualan::query()
                ->langsung()
                ->whereDate('waktu_transaksi', $today)
                ->latest('waktu_transaksi')
                ->take(30)
                ->get();

            $urlLaporan = Route::has('owner.laporan') ? route('owner.laporan') : '/owner/laporan-barang-keluar';

            foreach ($sales as $sale) {
                $metode = $sale->metode_bayar instanceof MetodeBayar
                    ? $sale->metode_bayar->label()
                    : ucfirst((string) ($sale->metode_bayar ?? 'Cash'));

                $pelanggan = trim((string) ($sale->nama_pelanggan ?? '')) ?: 'Umum';
                $kode = $sale->kode_transaksi ?: ('TRX-' . $sale->id);

                OwnerNotification::firstOrCreate(
                    ['event_key' => "sale_{$sale->id}"],
                    [
                        'target_role' => 'owner',
                        'type' => 'transaksi',
                        'title' => "Transaksi Baru #{$kode}",
                        'message' => "Penjualan kepada {$pelanggan} sebesar " . rupiah((int) $sale->total) . " ({$metode}).",
                        'url' => $urlLaporan,
                        'occurred_at' => $sale->waktu_transaksi ?? $sale->created_at ?? now('Asia/Jakarta'),
                    ]
                );
            }
        } catch (Throwable) {
        }

        $this->syncStokUntukRole('owner', $today, 'stock_', 'owner.master-item', '/owner/master-item');

        try {
            $urlBonIndex = Route::has('owner.bon') ? route('owner.bon') : '/owner/daftar-bon';

            $bonBaru = BonPiutang::query()
                ->with(['pelanggan', 'penjualan'])
                ->where(function ($q) use ($today): void {
                    $q->whereDate('tanggal_bon', $today)
                        ->orWhereDate('created_at', $today);
                })
                ->latest('id')
                ->take(30)
                ->get();

            foreach ($bonBaru as $bon) {
                $namaPelanggan = $bon->pelanggan?->nama ?? $bon->penjualan?->nama_pelanggan ?? 'Pelanggan';
                $kode = $bon->kode_bon ?: ('BON-' . $bon->id);
                $url = Route::has('owner.bon.detail') ? route('owner.bon.detail', $bon->id) : $urlBonIndex;

                OwnerNotification::firstOrCreate(
                    ['event_key' => "bon_new_{$bon->id}"],
                    [
                        'target_role' => 'owner',
                        'type' => 'bon',
                        'title' => 'Catatan Bon Baru',
                        'message' => "Bon #{$kode} atas nama {$namaPelanggan} senilai " . rupiah((int) $bon->total_tagihan) . '.',
                        'url' => $url,
                        'occurred_at' => $bon->created_at ?? $bon->tanggal_bon ?? now('Asia/Jakarta'),
                    ]
                );
            }

            $bonLunas = BonPiutang::query()
                ->with(['pelanggan', 'penjualan', 'pembayaran'])
                ->where('status_bayar', StatusBayar::Lunas->value)
                ->whereHas('pembayaran', fn ($q) => $q->whereDate('waktu_bayar', $today))
                ->latest('id')
                ->take(30)
                ->get();

            foreach ($bonLunas as $bon) {
                $namaPelanggan = $bon->pelanggan?->nama ?? $bon->penjualan?->nama_pelanggan ?? 'Pelanggan';
                $kode = $bon->kode_bon ?: ('BON-' . $bon->id);
                $url = Route::has('owner.bon.detail') ? route('owner.bon.detail', $bon->id) : $urlBonIndex;
                $waktuBayar = $bon->pembayaran->max('waktu_bayar') ?? now('Asia/Jakarta');

                OwnerNotification::firstOrCreate(
                    ['event_key' => "bon_paid_{$bon->id}"],
                    [
                        'target_role' => 'owner',
                        'type' => 'bon',
                        'title' => 'Pelunasan Bon',
                        'message' => "Bon #{$kode} atas nama {$namaPelanggan} sebesar " . rupiah((int) $bon->total_tagihan) . ' telah lunas.',
                        'url' => $url,
                        'occurred_at' => $waktuBayar,
                    ]
                );
            }
        } catch (Throwable) {
        }
    }

    private function syncKasir(string $today): void
    {
        // 1. Bon jatuh tempo hari ini / lewat jatuh tempo
        try {
            $baseBonUrl = Route::has('kasir.daftar-bon') ? route('kasir.daftar-bon') : '/kasir/daftar-bon';

            $bonJatuhTempo = BonPiutang::query()
                ->with(['pelanggan', 'penjualan'])
                ->belumLunas()
                ->whereDate('jatuh_tempo', '<=', $today)
                ->orderBy('jatuh_tempo')
                ->take(30)
                ->get();

            foreach ($bonJatuhTempo as $bon) {
                $kode = $bon->kode_bon ?: ('BON-' . $bon->id);
                $namaPelanggan = $bon->pelanggan?->nama ?? $bon->penjualan?->nama_pelanggan ?? 'Pelanggan';
                $sisa = max(0, (int) $bon->total_tagihan - (int) $bon->total_terbayar);
                $tglJatuhTempo = $bon->jatuh_tempo?->format('d M Y') ?? $today;
                $lewat = $bon->jatuh_tempo && $bon->jatuh_tempo->toDateString() < $today;

                OwnerNotification::firstOrCreate(
                    ['event_key' => "kasir_bon_due_{$bon->id}_{$today}"],
                    [
                        'target_role' => 'kasir',
                        'type' => 'bon',
                        'title' => $lewat ? 'Bon Lewat Jatuh Tempo' : 'Bon Jatuh Tempo Hari Ini',
                        'message' => "Bon #{$kode} ({$namaPelanggan}) sisa tagihan " . rupiah($sisa) . " (jatuh tempo {$tglJatuhTempo}).",
                        'url' => $baseBonUrl . '?search=' . urlencode($kode),
                        'occurred_at' => now('Asia/Jakarta'),
                    ]
                );
            }
        } catch (Throwable) {
        }

        // 2. Stok kritis setelah transaksi / stok habis / stok menipis
        $this->syncStokUntukRole('kasir', $today, 'kasir_stock_', 'kasir.master-item', '/kasir/master-item');

        // 3. Konfirmasi pengantaran selesai hari ini
        try {
            $baseBonUrl = Route::has('kasir.daftar-bon') ? route('kasir.daftar-bon') : '/kasir/daftar-bon';

            $pengantaranSelesai = PengirimanBarang::query()
                ->with(['bonPiutang.pelanggan', 'bonPiutang.penjualan', 'karyawan'])
                ->where('status', StatusPengiriman::Selesai->value)
                ->whereDate('waktu_selesai', $today)
                ->latest('waktu_selesai')
                ->take(30)
                ->get();

            foreach ($pengantaranSelesai as $pengiriman) {
                $kodeBon = $pengiriman->bonPiutang?->kode_bon ?? ('BON-' . $pengiriman->bon_id);
                $penerima = $pengiriman->bonPiutang?->pelanggan?->nama
                    ?? $pengiriman->bonPiutang?->penjualan?->nama_pelanggan
                    ?? 'Pelanggan';
                $kurir = $pengiriman->karyawan?->nama ?? 'Karyawan';

                OwnerNotification::firstOrCreate(
                    ['event_key' => "kasir_delivery_done_{$pengiriman->id}"],
                    [
                        'target_role' => 'kasir',
                        'type' => 'pengantaran',
                        'title' => 'Pengantaran Selesai',
                        'message' => "Pengantaran #{$kodeBon} untuk {$penerima} telah diselesaikan oleh {$kurir} dengan foto bukti.",
                        'url' => $baseBonUrl . '?search=' . urlencode($kodeBon),
                        'occurred_at' => $pengiriman->waktu_selesai ?? now('Asia/Jakarta'),
                    ]
                );
            }
        } catch (Throwable) {
        }
    }

    private function syncKaryawan(?int $userId): void
    {
        try {
            $query = PengirimanBarang::query()
                ->with(['bonPiutang.pelanggan', 'bonPiutang.penjualan'])
                ->whereIn('status', [
                    StatusPengiriman::DikirimKeKaryawan->value,
                    StatusPengiriman::SedangDisiapkan->value,
                    StatusPengiriman::SiapBerangkat->value,
                ])
                ->latest('id')
                ->take(30);

            if ($userId !== null) {
                $query->where('karyawan_id', $userId);
            } else {
                $query->whereNotNull('karyawan_id');
            }

            $tugasList = $query->get();

            foreach ($tugasList as $pengiriman) {
                $kodeBon = $pengiriman->bonPiutang?->kode_bon ?? ('BON-' . $pengiriman->bon_id);
                $namaPelanggan = $pengiriman->bonPiutang?->pelanggan?->nama
                    ?? $pengiriman->bonPiutang?->penjualan?->nama_pelanggan
                    ?? 'Pelanggan';
                $alamat = trim((string) ($pengiriman->bonPiutang?->alamat_pengantaran ?? ''));
                $catatan = trim((string) ($pengiriman->catatan_kasir ?? ''));

                $detailBagian = [];
                if ($alamat !== '') {
                    $detailBagian[] = $alamat;
                }
                if ($catatan !== '') {
                    $detailBagian[] = "Catatan: {$catatan}";
                }
                $ringkasan = ! empty($detailBagian)
                    ? implode(' • ', $detailBagian)
                    : 'Segera siapkan barang dan perbarui status pengantaran.';

                $targetUrl = Route::has('karyawan.pengantaran.detail')
                    ? route('karyawan.pengantaran.detail', $pengiriman->id)
                    : '/karyawan/pengantaran/' . $pengiriman->id;

                OwnerNotification::firstOrCreate(
                    ['event_key' => "karyawan_delivery_{$pengiriman->id}_{$pengiriman->karyawan_id}"],
                    [
                        'target_role' => 'karyawan',
                        'target_user_id' => $pengiriman->karyawan_id,
                        'type' => 'pengantaran',
                        'title' => 'Tugas Pengantaran Baru',
                        'message' => "Pesanan #{$kodeBon} a.n. {$namaPelanggan} — {$ringkasan}",
                        'url' => $targetUrl,
                        'occurred_at' => $pengiriman->created_at ?? now('Asia/Jakarta'),
                    ]
                );
            }
        } catch (Throwable) {
        }
    }

    private function syncAdmin(string $today): void
    {
        try {
            $basePenggunaUrl = Route::has('admin.pengguna') ? route('admin.pengguna') : '/admin/pengguna';

            // 1. Pendaftaran / penambahan pengguna baru hari ini
            $usersBaru = User::query()
                ->with('role')
                ->whereDate('created_at', $today)
                ->latest('id')
                ->take(30)
                ->get();

            foreach ($usersBaru as $u) {
                $roleLabel = ucfirst((string) ($u->role?->nama ?? 'pengguna'));

                OwnerNotification::firstOrCreate(
                    ['event_key' => "admin_user_reg_{$u->id}"],
                    [
                        'target_role' => 'admin',
                        'type' => 'sistem',
                        'title' => 'Pendaftaran Akun Baru',
                        'message' => "{$u->nama} ({$u->email}) terdaftar dengan role {$roleLabel}.",
                        'url' => $basePenggunaUrl . '?cari=' . urlencode($u->email),
                        'occurred_at' => $u->created_at ?? now('Asia/Jakarta'),
                    ]
                );
            }

            // 2. Perubahan status/role pengguna dari activity_logs hari ini
            $logPengguna = ActivityLog::query()
                ->where('modul', 'pengguna')
                ->whereIn('aksi', ['status', 'ubah', 'tambah', 'hapus'])
                ->whereDate('created_at', $today)
                ->latest('id')
                ->take(30)
                ->get();

            foreach ($logPengguna as $log) {
                OwnerNotification::firstOrCreate(
                    ['event_key' => "admin_user_log_{$log->id}"],
                    [
                        'target_role' => 'admin',
                        'type' => 'sistem',
                        'title' => 'Perubahan Akun Pengguna',
                        'message' => $log->deskripsi,
                        'url' => $basePenggunaUrl,
                        'occurred_at' => $log->created_at ?? now('Asia/Jakarta'),
                    ]
                );
            }
        } catch (Throwable) {
        }
    }

    private function syncStokUntukRole(
        string $role,
        string $today,
        string $keyPrefix,
        string $routeName,
        string $fallbackPath,
    ): void {
        try {
            $baseMasterUrl = Route::has($routeName) ? route($routeName) : $fallbackPath;

            $stokHabisList = Barang::query()
                ->where('stok', '<=', 0)
                ->orderBy('nama_barang')
                ->get();

            foreach ($stokHabisList as $barang) {
                $kode = $barang->kode_barang ? " ({$barang->kode_barang})" : '';
                $targetUrl = $barang->kode_barang
                    ? $baseMasterUrl . '?search=' . urlencode($barang->kode_barang)
                    : $baseMasterUrl;

                OwnerNotification::firstOrCreate(
                    ['event_key' => "{$keyPrefix}empty_{$barang->id}_{$today}"],
                    [
                        'target_role' => $role,
                        'type' => 'stok_habis',
                        'title' => 'Stok Produk Habis!',
                        'message' => "{$barang->nama_barang}{$kode} stok kosong (0 {$barang->satuan}). Segera lakukan restock.",
                        'url' => $targetUrl,
                        'occurred_at' => now('Asia/Jakarta'),
                    ]
                );
            }

            $stokMenipisList = Barang::query()
                ->where('stok', '>', 0)
                ->whereColumn('stok', '<=', 'stok_minimum')
                ->orderBy('stok')
                ->get();

            foreach ($stokMenipisList as $barang) {
                $kode = $barang->kode_barang ? " ({$barang->kode_barang})" : '';
                $targetUrl = $barang->kode_barang
                    ? $baseMasterUrl . '?search=' . urlencode($barang->kode_barang)
                    : $baseMasterUrl;

                OwnerNotification::firstOrCreate(
                    ['event_key' => "{$keyPrefix}low_{$barang->id}_{$today}"],
                    [
                        'target_role' => $role,
                        'type' => 'stok_menipis',
                        'title' => 'Stok Menipis',
                        'message' => "{$barang->nama_barang}{$kode} tersisa {$barang->stok} {$barang->satuan} (batas min: {$barang->stok_minimum} {$barang->satuan}).",
                        'url' => $targetUrl,
                        'occurred_at' => now('Asia/Jakarta'),
                    ]
                );
            }
        } catch (Throwable) {
        }
    }
}
