<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use Illuminate\Database\Seeder;

class PelangganSeeder extends Seeder
{
    public function run(): void
    {
        $pelangganData = [
            ['nama' => 'HKBP Hutanamora (Lestari Nababan)', 'no_hp' => '0838193089', 'alamat' => 'Hutanamora'],
            ['nama' => 'Eda Yasmin', 'no_hp' => null, 'alamat' => 'Sipultak Dolok'],
            ['nama' => 'DD Lubis (Duaman Lubis)', 'no_hp' => null, 'alamat' => 'Lumban Julu'],
            ['nama' => 'A. Adi', 'no_hp' => null, 'alamat' => 'Lumban Ina-ina'],
            ['nama' => 'Op. Ayu', 'no_hp' => null, 'alamat' => 'Lumban Julu'],
            ['nama' => 'Ka Lilis', 'no_hp' => null, 'alamat' => 'Lumban Julu'],
        ];

        foreach ($pelangganData as $p) {
            Pelanggan::firstOrCreate(
                ['nama' => $p['nama']],
                ['no_hp' => $p['no_hp'], 'alamat' => $p['alamat']]
            );
        }
    }
}