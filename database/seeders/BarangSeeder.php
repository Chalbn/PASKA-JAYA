<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\KategoriBarang;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $katIds = KategoriBarang::pluck('id', 'nama');

        $barangData = [
            // Dari Master Item: Bahan bahan toko.jpeg
            ['kat' => 'Besi & Baja', 'nama' => 'Besi 10 mili', 'satuan' => 'Batang', 'beli' => 60000, 'jual' => 80000, 'stok' => 79],
            ['kat' => 'Besi & Baja', 'nama' => 'Besi 8 mili', 'satuan' => 'Batang', 'beli' => 52000, 'jual' => 60000, 'stok' => 58],
            ['kat' => 'Besi & Baja', 'nama' => 'Besi 6 mili', 'satuan' => 'Batang', 'beli' => 22000, 'jual' => 40000, 'stok' => 11],
            ['kat' => 'Besi & Baja', 'nama' => 'Besi 5 mili', 'satuan' => 'Batang', 'beli' => 15000, 'jual' => 28000, 'stok' => 82],
            ['kat' => 'Semen & Mortar', 'nama' => 'Semen Padang 50kg', 'satuan' => 'Sak', 'beli' => 79000, 'jual' => 90000, 'stok' => 50],
            ['kat' => 'Semen & Mortar', 'nama' => 'Semen Garuda 40kg', 'satuan' => 'Sak', 'beli' => 74000, 'jual' => 85000, 'stok' => 136],
            ['kat' => 'Material Alam', 'nama' => 'Batu Bata', 'satuan' => 'Pcs', 'beli' => 700, 'jual' => 900, 'stok' => 7000],
            ['kat' => 'Material Alam', 'nama' => 'Pasir', 'satuan' => 'M3', 'beli' => 80000, 'jual' => 170000, 'stok' => 147],
            ['kat' => 'Material Alam', 'nama' => 'Batu Padas', 'satuan' => 'M3', 'beli' => 150000, 'jual' => 240000, 'stok' => 100],
            ['kat' => 'Material Alam', 'nama' => 'Batu Cor 2/3', 'satuan' => 'M3', 'beli' => 250000, 'jual' => 350000, 'stok' => 180],
            ['kat' => 'Atap & Seng', 'nama' => 'Seng 6/11', 'satuan' => 'Lembar', 'beli' => 50000, 'jual' => 60000, 'stok' => 124],
            ['kat' => 'Atap & Seng', 'nama' => 'Seng 7/11', 'satuan' => 'Lembar', 'beli' => 60000, 'jual' => 70000, 'stok' => 24],
            ['kat' => 'Atap & Seng', 'nama' => 'Seng 8/11', 'satuan' => 'Lembar', 'beli' => 70000, 'jual' => 80000, 'stok' => 70],
            ['kat' => 'Atap & Seng', 'nama' => 'Rabung 6k', 'satuan' => 'Lembar', 'beli' => 20000, 'jual' => 35000, 'stok' => 30],
            ['kat' => 'Atap & Seng', 'nama' => 'Rabung 7k', 'satuan' => 'Lembar', 'beli' => 30000, 'jual' => 45000, 'stok' => 66],
            ['kat' => 'Atap & Seng', 'nama' => 'Rabung 8k', 'satuan' => 'Lembar', 'beli' => 40000, 'jual' => 55000, 'stok' => 85],
            ['kat' => 'Pipa & Sanitasi', 'nama' => 'Pipa 4"', 'satuan' => 'Batang', 'beli' => 70000, 'jual' => 100000, 'stok' => 58],
            ['kat' => 'Pipa & Sanitasi', 'nama' => 'Pipa 3"', 'satuan' => 'Batang', 'beli' => 60000, 'jual' => 80000, 'stok' => 59],
            ['kat' => 'Pipa & Sanitasi', 'nama' => 'Pipa 2 1/2"', 'satuan' => 'Batang', 'beli' => 50000, 'jual' => 70000, 'stok' => 70],
            ['kat' => 'Besi & Baja', 'nama' => 'Canal', 'satuan' => 'Batang', 'beli' => 100000, 'jual' => 125000, 'stok' => 25],
            ['kat' => 'Besi & Baja', 'nama' => 'Reng Baja', 'satuan' => 'Batang', 'beli' => 64000, 'jual' => 80000, 'stok' => 5],
            ['kat' => 'Atap & Seng', 'nama' => 'Seng Deck 3.5m', 'satuan' => 'Lembar', 'beli' => 260000, 'jual' => 323000, 'stok' => 60],
            ['kat' => 'Atap & Seng', 'nama' => 'Seng Deck 4.6m', 'satuan' => 'Lembar', 'beli' => 310000, 'jual' => 391000, 'stok' => 37],
            ['kat' => 'Atap & Seng', 'nama' => 'Seng Deck 3m', 'satuan' => 'Lembar', 'beli' => 205000, 'jual' => 255000, 'stok' => 47],
            ['kat' => 'Atap & Seng', 'nama' => 'Seng Deck 6m', 'satuan' => 'Lembar', 'beli' => 320000, 'jual' => 402857, 'stok' => 96],
            ['kat' => 'Cat & Finishing', 'nama' => 'Cat Avitex', 'satuan' => 'Pail', 'beli' => 1200000, 'jual' => 1500000, 'stok' => 30],
            ['kat' => 'Cat & Finishing', 'nama' => 'Cat Aries', 'satuan' => 'Pail', 'beli' => 480000, 'jual' => 600000, 'stok' => 8],
            ['kat' => 'Cat & Finishing', 'nama' => 'Keramik 50x50', 'satuan' => 'Dus', 'beli' => 80000, 'jual' => 100000, 'stok' => 203],
            ['kat' => 'Cat & Finishing', 'nama' => 'Kaca Block', 'satuan' => 'Dus', 'beli' => 190000, 'jual' => 240000, 'stok' => 50],
            ['kat' => 'Paku & Baut', 'nama' => 'Paku 2"', 'satuan' => 'Kg', 'beli' => 20000, 'jual' => 25000, 'stok' => 50],
            ['kat' => 'Paku & Baut', 'nama' => 'Paku 3"', 'satuan' => 'Kg', 'beli' => 20000, 'jual' => 25000, 'stok' => 91],
            ['kat' => 'Paku & Baut', 'nama' => 'Paku 4"', 'satuan' => 'Kg', 'beli' => 20000, 'jual' => 25000, 'stok' => 57],
            ['kat' => 'Paku & Baut', 'nama' => 'Paku Seng', 'satuan' => 'Kg', 'beli' => 35000, 'jual' => 45000, 'stok' => 61],
            ['kat' => 'Paku & Baut', 'nama' => 'Kawat Ikat', 'satuan' => 'Kg', 'beli' => 24000, 'jual' => 30000, 'stok' => 98],
            ['kat' => 'Listrik & Perkakas', 'nama' => 'Bohlam 5W', 'satuan' => 'Pcs', 'beli' => 24000, 'jual' => 30000, 'stok' => 80],
            ['kat' => 'Listrik & Perkakas', 'nama' => 'Box MCB', 'satuan' => 'Pcs', 'beli' => 24000, 'jual' => 30000, 'stok' => 52],
        ];

        foreach ($barangData as $b) {
            Barang::firstOrCreate(
                ['nama_barang' => $b['nama']],
                [
                    'kategori_id' => $katIds[$b['kat']],
                    'satuan' => $b['satuan'],
                    'harga_beli' => $b['beli'],
                    'harga_jual' => $b['jual'],
                    'stok' => $b['stok'],
                    'stok_minimum' => 15,
                    'tanggal_masuk_terakhir' => '2026-11-09',
                ]
            );
        }
    }
}