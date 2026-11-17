<?php

namespace Database\Factories;

use App\Models\BonPiutang;
use App\Models\PengirimanBarang;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PengirimanBarang>
 */
class PengirimanBarangFactory extends Factory
{
    protected $model = PengirimanBarang::class;

    public function definition(): array
    {
        return [
            'bon_id' => BonPiutang::factory(),
            'karyawan_id' => User::factory(),
            'status' => 'dikirim_ke_karyawan',
            'jadwal_kirim' => now()->addHours(2),
            'catatan_kasir' => fake()->optional()->sentence(),
            'foto_bukti' => null,
            'dibaca_pada' => null,
            'waktu_selesai' => null,
            'dibuat_oleh' => 1,
        ];
    }

    public function sedangDisiapkan(): static
    {
        return $this->state(['status' => 'sedang_disiapkan', 'dibaca_pada' => now()]);
    }

    public function siapBerangkat(): static
    {
        return $this->state(['status' => 'siap_berangkat', 'dibaca_pada' => now()]);
    }

    public function selesai(): static
    {
        return $this->state([
            'status' => 'selesai',
            'dibaca_pada' => now(),
            'foto_bukti' => 'bukti-pengiriman/test.jpg',
            'waktu_selesai' => now(),
        ]);
    }
}
