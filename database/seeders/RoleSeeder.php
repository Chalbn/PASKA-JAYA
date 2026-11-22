<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->upsert([
            ['nama' => 'admin', 'label' => 'Admin', 'deskripsi' => 'Mengelola pengguna dan sistem'],
            ['nama' => 'owner', 'label' => 'Owner', 'deskripsi' => 'Pemilik toko, memantau laporan'],
            ['nama' => 'kasir', 'label' => 'Kasir', 'deskripsi' => 'Mencatat penjualan, bon, kehadiran'],
            ['nama' => 'karyawan', 'label' => 'Karyawan', 'deskripsi' => 'Mengantar barang'],
        ], ['nama'], ['label', 'deskripsi']);
    }
}
