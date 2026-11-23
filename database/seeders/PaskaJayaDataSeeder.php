<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PaskaJayaDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DatabaseSeeder::class,
            SystemConfigSeeder::class,
            KategoriBarangSeeder::class,
            BarangSeeder::class,
            PelangganSeeder::class,
            PenggunaanBahanSeeder::class,
            PenjualanLangsungSeeder::class,
            BonTransaksiSeeder::class,
        ]);
    }
}