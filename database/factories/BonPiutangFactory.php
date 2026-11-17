<?php

namespace Database\Factories;

use App\Models\BonPiutang;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BonPiutang>
 */
class BonPiutangFactory extends Factory
{
    protected $model = BonPiutang::class;

    public function definition(): array
    {
        return [
            'kode_bon' => 'BON-'.str_pad((string) fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'penjualan_id' => Penjualan::factory(),
            'pelanggan_id' => Pelanggan::factory(),
            'alamat_pengantaran' => fake()->address(),
            'tanggal_bon' => now()->toDateString(),
            'jatuh_tempo' => now()->addDays(30)->toDateString(),
            'total_tagihan' => fake()->numberBetween(100000, 5000000),
            'total_terbayar' => 0,
            'status_bayar' => 'belum_lunas',
            'dibuat_oleh' => 1,
        ];
    }
}
