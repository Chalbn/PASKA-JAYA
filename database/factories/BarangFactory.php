<?php

namespace Database\Factories;

use App\Models\Barang;
use App\Models\KategoriBarang;
use Illuminate\Database\Eloquent\Factories\Factory;

class BarangFactory extends Factory
{
    protected $model = Barang::class;

    public function definition(): array
    {
        $hargaBeli = fake()->numberBetween(10000, 100000);
        $hargaJual = (int) ($hargaBeli * 1.2);

        return [
            'kode_barang' => null, // diisi trigger fn_kode_barang_berikutnya jika null
            'kategori_id' => KategoriBarang::factory(),
            'nama_barang' => fake()->words(3, true),
            'satuan' => fake()->randomElement(['Sak', 'Batang', 'Dus', 'M3', 'Kg', 'Pcs']),
            'harga_beli' => $hargaBeli,
            'harga_jual' => $hargaJual,
            'stok' => fake()->numberBetween(20, 100),
            'stok_minimum' => 10,
            'foto' => null,
            'tanggal_masuk_terakhir' => now()->toDateString(),
        ];
    }

    /**
     * Kondisi stok menipis (<= stok_minimum).
     */
    public function stokMenipis(): static
    {
        return $this->state(fn () => [
            'stok' => 5,
            'stok_minimum' => 10,
        ]);
    }
}
