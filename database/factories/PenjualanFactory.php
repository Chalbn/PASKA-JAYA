<?php

namespace Database\Factories;

use App\Models\Penjualan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Penjualan>
 */
class PenjualanFactory extends Factory
{
    protected $model = Penjualan::class;

    public function definition(): array
    {
        return [
            'kode_transaksi' => 'TRX-'.fake()->unique()->numberBetween(1000, 9999),
            'tipe' => 'bon',
            'pelanggan_id' => null,
            'nama_pelanggan' => fake()->name(),
            'metode_bayar' => null,
            'total' => fake()->numberBetween(100000, 5000000),
            'user_id' => User::factory(),
            'waktu_transaksi' => now(),
        ];
    }
}
