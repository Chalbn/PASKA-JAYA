<?php

namespace Database\Factories;

use App\Models\KategoriBarang;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class KategoriBarangFactory extends Factory
{
    protected $model = KategoriBarang::class;

    public function definition(): array
    {
        $nama = fake()->unique()->words(2, true);

        return [
            'nama' => ucfirst($nama),
            'slug' => Str::slug($nama),
        ];
    }
}
