<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Concerns\HandlesDatabaseExceptions;
use Illuminate\Support\Facades\DB;

class PenjualanService
{
    use HandlesDatabaseExceptions;

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
