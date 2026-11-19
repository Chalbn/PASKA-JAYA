<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\MetodeBayar;
use App\Enums\StatusBayar;
use App\Models\Barang;
use App\Models\BonPiutang;
use App\Models\OwnerNotification;
use App\Models\Penjualan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Throwable;

class OwnerNotificationService
{
    /**
     * Sinkronisasi aktivitas nyata dari database ke tabel owner_notifications
     * secara idempoten melalui event_key unik.
     */
    public function sync(): void
    {
        try {
            if (! Schema::hasTable('owner_notifications')) {
                return;
            }
        } catch (Throwable) {
            return;
        }

        $today = now('Asia/Jakarta')->toDateString();

        $this->syncTransaksiHariIni($today);
        $this->syncStokProduk($today);
        $this->syncBonPiutang($today);
    }

    private function syncTransaksiHariIni(string $today): void
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
                        'type' => 'transaksi',
                        'title' => "Transaksi Baru #{$kode}",
                        'message' => "Penjualan kepada {$pelanggan} sebesar " . rupiah((int) $sale->total) . " ({$metode}).",
                        'url' => $urlLaporan,
                        'occurred_at' => $sale->waktu_transaksi ?? $sale->created_at ?? now('Asia/Jakarta'),
                    ]
                );
            }
        } catch (Throwable) {
            // Abaikan error query sinkronisasi agar tidak mengganggu request utama
        }
    }

    private function syncStokProduk(string $today): void
    {
        try {
            $baseMasterUrl = Route::has('owner.master-item') ? route('owner.master-item') : '/owner/master-item';

            // 1. Produk stok habis (stok <= 0)
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
                    ['event_key' => "stock_empty_{$barang->id}_{$today}"],
                    [
                        'type' => 'stok_habis',
                        'title' => 'Stok Produk Habis!',
                        'message' => "{$barang->nama_barang}{$kode} stok kosong (0 {$barang->satuan}). Segera lakukan restock.",
                        'url' => $targetUrl,
                        'occurred_at' => now('Asia/Jakarta'),
                    ]
                );
            }

            // 2. Produk stok menipis (0 < stok <= stok_minimum)
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
                    ['event_key' => "stock_low_{$barang->id}_{$today}"],
                    [
                        'type' => 'stok_menipis',
                        'title' => 'Stok Menipis',
                        'message' => "{$barang->nama_barang}{$kode} tersisa {$barang->stok} {$barang->satuan} (batas min: {$barang->stok_minimum} {$barang->satuan}).",
                        'url' => $targetUrl,
                        'occurred_at' => now('Asia/Jakarta'),
                    ]
                );
            }
        } catch (Throwable) {
            // Abaikan error query sinkronisasi agar tidak mengganggu request utama
        }
    }

    private function syncBonPiutang(string $today): void
    {
        try {
            $urlBonIndex = Route::has('owner.bon') ? route('owner.bon') : '/owner/daftar-bon';

            // 1. Catatan Bon baru hari ini
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
                        'type' => 'bon',
                        'title' => 'Catatan Bon Baru',
                        'message' => "Bon #{$kode} atas nama {$namaPelanggan} senilai " . rupiah((int) $bon->total_tagihan) . '.',
                        'url' => $url,
                        'occurred_at' => $bon->created_at ?? $bon->tanggal_bon ?? now('Asia/Jakarta'),
                    ]
                );
            }

            // 2. Pelunasan Bon hari ini
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
                        'type' => 'bon',
                        'title' => 'Pelunasan Bon',
                        'message' => "Bon #{$kode} atas nama {$namaPelanggan} sebesar " . rupiah((int) $bon->total_tagihan) . ' telah lunas.',
                        'url' => $url,
                        'occurred_at' => $waktuBayar,
                    ]
                );
            }
        } catch (Throwable) {
            // Abaikan error query sinkronisasi agar tidak mengganggu request utama
        }
    }
}
