<?php

namespace Database\Seeders;

use App\Models\KategoriBarang;
use Illuminate\Database\Seeder;

class KategoriBarangSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            'Besi & Baja',
            'Semen & Mortar',
            'Material Alam',
            'Atap & Seng',
            'Pipa & Sanitasi',
            'Cat & Finishing',
            'Paku & Baut',
            'Listrik & Perkakas',
        ];

        foreach ($kategori as $nama) {
            KategoriBarang::firstOrCreate(['nama' => $nama]);
        }
    }
}
