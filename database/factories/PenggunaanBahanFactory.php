<?php

namespace Database\Factories;

use App\Models\PenggunaanBahan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PenggunaanBahan>
 */
class PenggunaanBahanFactory extends Factory
{
    protected $model = PenggunaanBahan::class;

    public function definition(): array
    {
        return [
            'tanggal' => now()->toDateString(),
            'nama_bahan' => fake()->randomElement(['Bensin Pick-up', 'Tali Rafia', 'Sarung Tangan Kerja', 'Kawat Las', 'Plastik Cor']),
            'jumlah' => fake()->randomFloat(2, 1, 10),
            'satuan' => fake()->randomElement(['liter', 'roll', 'pasang', 'dus', 'kotak']),
            'harga' => fake()->numberBetween(15000, 200000),
            'keterangan' => fake()->optional()->sentence(),
            'user_id' => User::factory()->kasir(),
        ];
    }
}
