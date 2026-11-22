<?php

namespace Database\Seeders;

use App\Models\PenggunaanBahan;
use App\Models\User;
use Illuminate\Database\Seeder;

class PenggunaanBahanSeeder extends Seeder
{
    public function run(): void
    {
        if (PenggunaanBahan::count() > 0) {
            return;
        }

        $kasir = User::whereHas('role', fn ($q) => $q->where('nama', 'kasir'))->firstOrFail();

        $bahanHarian = [
            ['tanggal' => '2026-11-09', 'nama_bahan' => 'Batu Padas', 'jumlah' => 1, 'satuan' => 'truk', 'harga' => 1100000, 'keterangan' => 'Lapangan'],
            ['tanggal' => '2026-11-09', 'nama_bahan' => 'Solar', 'jumlah' => 2, 'satuan' => 'mobil', 'harga' => 800000, 'keterangan' => 'Untuk 2 mobil (@400.000)'],
            ['tanggal' => '2026-11-09', 'nama_bahan' => 'Pasir', 'jumlah' => 2, 'satuan' => 'truk', 'harga' => 800000, 'keterangan' => 'Tumpukan (@400.000)'],
            ['tanggal' => '2026-11-09', 'nama_bahan' => 'Honor Supir', 'jumlah' => 2, 'satuan' => 'orang', 'harga' => 200000, 'keterangan' => 'Honor 2 supir (@100.000)'],
            ['tanggal' => '2026-11-09', 'nama_bahan' => 'Uang Makan Supir', 'jumlah' => 2, 'satuan' => 'orang', 'harga' => 150000, 'keterangan' => 'Makan 2 supir (@75.000)'],
            ['tanggal' => '2026-11-09', 'nama_bahan' => 'Bensin Mobil TS', 'jumlah' => 1, 'satuan' => 'mobil', 'harga' => 400000, 'keterangan' => 'Mobil TS'],
            ['tanggal' => '2026-11-10', 'nama_bahan' => 'Pasir', 'jumlah' => 1, 'satuan' => 'truk', 'harga' => 400000, 'keterangan' => 'Lapangan'],
            ['tanggal' => '2026-11-10', 'nama_bahan' => 'Semen', 'jumlah' => 100, 'satuan' => 'zak', 'harga' => 7400000, 'keterangan' => '100 zak (@74.000)'],
            ['tanggal' => '2026-11-10', 'nama_bahan' => 'Honor Supir', 'jumlah' => 2, 'satuan' => 'orang', 'harga' => 200000, 'keterangan' => 'Honor 2 supir (@100.000)'],
            ['tanggal' => '2026-11-10', 'nama_bahan' => 'Uang Makan Supir', 'jumlah' => 2, 'satuan' => 'orang', 'harga' => 150000, 'keterangan' => 'Makan 2 supir (@75.000)'],
            ['tanggal' => '2026-11-10', 'nama_bahan' => 'Batu Padas', 'jumlah' => 2, 'satuan' => 'truk', 'harga' => 2200000, 'keterangan' => '1 Lapangan + 1 Tumpukan (@1.100.000)'],
        ];

        foreach ($bahanHarian as $row) {
            PenggunaanBahan::create(array_merge($row, ['user_id' => $kasir->id]));
        }
    }
}