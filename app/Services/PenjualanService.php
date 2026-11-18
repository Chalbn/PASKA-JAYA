<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\MetodeBayar;
use App\Enums\TipePenjualan;
use App\Models\Barang;
use App\Models\DetailPenjualan;
use App\Models\Penjualan;
use App\Models\User;
use App\Services\Concerns\HandlesDatabaseExceptions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenjualanService
{
    use HandlesDatabaseExceptions;

    /**
     * Menyimpan transaksi penjualan langsung (tipe='langsung').
     *
     * @param  array{
     *     items: array<int, array{barang_id?: int, master_item_id?: int, jumlah: int}>,
     *     metode_bayar: string|MetodeBayar,
     *     nama_pelanggan?: string|null,
     *     pelanggan_id?: int|null,
     *     kode_transaksi?: string|null,
     *     waktu_transaksi?: \DateTimeInterface|string|null,
     * }  $data
     * @param  User|null  $user  Kasir yang mencatat transaksi
     *
     * @throws \InvalidArgumentException|\RuntimeException
     */
    public function simpanLangsung(array $data, ?User $user = null): Penjualan
    {
        return DB::transaction(function () use ($data, $user): Penjualan {
            $rawItems = $data['items'] ?? [];
            if (empty($rawItems)) {
                throw new \InvalidArgumentException('Keranjang transaksi tidak boleh kosong.');
            }

            // Agregasi jumlah jika barang yang sama ditambahkan berulang
            $aggregated = [];
            foreach ($rawItems as $item) {
                $barangId = (int) ($item['barang_id'] ?? $item['master_item_id'] ?? 0);
                $jumlah = (int) ($item['jumlah'] ?? 0);

                if ($barangId <= 0) {
                    throw new \InvalidArgumentException('Item barang tidak valid.');
                }
                if ($jumlah <= 0) {
                    throw new \InvalidArgumentException('Jumlah barang harus minimal 1.');
                }

                $aggregated[$barangId] = ($aggregated[$barangId] ?? 0) + $jumlah;
            }

            // Kunci baris barang secara berurutan untuk mencegah race condition & deadlock
            $barangIds = array_keys($aggregated);
            sort($barangIds);

            $barangList = Barang::whereIn('id', $barangIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $total = 0;
            $detailRecords = [];

            foreach ($aggregated as $barangId => $jumlah) {
                $barang = $barangList->get($barangId);
                if (! $barang) {
                    throw new \InvalidArgumentException("Barang dengan ID {$barangId} tidak ditemukan.");
                }

                // Validasi stok dengan pesan persis: "Stok {nama} tidak cukup (tersisa N)"
                if ($barang->stok < $jumlah) {
                    throw new \RuntimeException("Stok {$barang->nama_barang} tidak cukup (tersisa {$barang->stok})");
                }

                $hargaSatuan = (int) $barang->harga_jual;
                $subtotal = $jumlah * $hargaSatuan;
                $total += $subtotal;

                $detailRecords[] = [
                    'barang_id' => $barang->id,
                    'nama_barang' => $barang->nama_barang,
                    'satuan' => $barang->satuan,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $subtotal,
                ];
            }

            // Penomoran kode transaksi berurutan TRX-#### secara aman dari race condition
            if (! empty($data['kode_transaksi'])) {
                $kodeTransaksi = (string) $data['kode_transaksi'];
            } else {
                $maxNum = DB::table('penjualan')
                    ->where('kode_transaksi', 'REGEXP', '^TRX-[0-9]+$')
                    ->lockForUpdate()
                    ->selectRaw('MAX(CAST(SUBSTRING(kode_transaksi, 5) AS UNSIGNED)) as max_num')
                    ->value('max_num');

                $nextNum = ($maxNum === null) ? 1001 : ((int) $maxNum) + 1;
                $kodeTransaksi = 'TRX-'.str_pad((string) $nextNum, 4, '0', STR_PAD_LEFT);
            }

            // Normalisasi metode bayar
            $metode = $data['metode_bayar'] ?? 'cash';
            $metodeBayar = $metode instanceof MetodeBayar ? $metode->value : strtolower(trim((string) $metode));
            if (! in_array($metodeBayar, ['cash', 'transfer'], true)) {
                $metodeBayar = 'cash';
            }

            $namaPelanggan = ! empty($data['nama_pelanggan']) ? trim((string) $data['nama_pelanggan']) : 'Umum';
            $currentUser = $user ?? Auth::user();
            $userId = (int) ($currentUser?->id ?? 1);
            $waktuTransaksi = $data['waktu_transaksi'] ?? now();

            // 1. Insert header penjualan (tipe='langsung')
            // Trigger T3 (trg_t3_penjualan_ai_kas_laci) otomatis mengisi kas_laci
            $penjualan = Penjualan::create([
                'kode_transaksi' => $kodeTransaksi,
                'tipe' => TipePenjualan::Langsung->value,
                'pelanggan_id' => $data['pelanggan_id'] ?? null,
                'nama_pelanggan' => $namaPelanggan,
                'metode_bayar' => $metodeBayar,
                'total' => $total,
                'user_id' => $userId,
                'waktu_transaksi' => $waktuTransaksi,
            ]);

            // 2. Insert detail_penjualan (snapshot)
            // Trigger T2 (trg_t2_detail_penjualan_ai_potong_stok) otomatis memotong stok & catat mutasi_stok
            foreach ($detailRecords as $record) {
                $record['penjualan_id'] = $penjualan->id;
                DetailPenjualan::create($record);
            }

            // 3. Catat audit trail ke activity_logs
            app(ActivityLogService::class)->catat(
                aksi: 'penjualan_langsung',
                modul: 'penjualan',
                deskripsi: "Penjualan langsung {$penjualan->kode_transaksi} sebesar ".rupiah($penjualan->total)." ({$penjualan->nama_pelanggan})",
                user: $currentUser,
                targetId: $penjualan->id,
                dataBaru: [
                    'kode_transaksi' => $penjualan->kode_transaksi,
                    'total' => $penjualan->total,
                    'metode_bayar' => $metodeBayar,
                    'nama_pelanggan' => $namaPelanggan,
                    'jumlah_item' => count($detailRecords),
                ],
            );

            return $penjualan->load('detailPenjualan');
        });
    }

    public function tambahBarang(
        string $kodeBarang,
        string $kategori,
        string $namaBarang,
        string $satuan,
        int $hargaBeli,
        int $hargaJual,
        int $stokAwal,
        int $stokMinimum,
        ?string $foto = null,
        ?int $userId = null,
    ): object {
        return $this->executeWithExceptionHandling(function () use (
            $kodeBarang,
            $kategori,
            $namaBarang,
            $satuan,
            $hargaBeli,
            $hargaJual,
            $stokAwal,
            $stokMinimum,
            $foto,
            $userId,
        ): object {
            $hasil = DB::select(
                'CALL sp_tambah_barang(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $kodeBarang,
                    $kategori,
                    $namaBarang,
                    $satuan,
                    $hargaBeli,
                    $hargaJual,
                    $stokAwal,
                    $stokMinimum,
                    $foto,
                    $userId,
                ]
            );

            return $hasil[0] ?? (object) [];
        });
    }

    public function restockBarang(
        int $barangId,
        int $jumlahTambah,
        int $hargaBeli,
        int $hargaJual,
        int $stokMinimum,
        ?int $userId = null,
    ): object {
        return $this->executeWithExceptionHandling(function () use (
            $barangId,
            $jumlahTambah,
            $hargaBeli,
            $hargaJual,
            $stokMinimum,
            $userId,
        ): object {
            $hasil = DB::select(
                'CALL sp_restock_barang(?, ?, ?, ?, ?, ?)',
                [
                    $barangId,
                    $jumlahTambah,
                    $hargaBeli,
                    $hargaJual,
                    $stokMinimum,
                    $userId,
                ]
            );

            return $hasil[0] ?? (object) [];
        });
    }
}
