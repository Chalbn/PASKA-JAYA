<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        $nama = fake()->unique()->word();

        return [
            'nama' => $nama,
            'label' => ucfirst($nama),
            'deskripsi' => fake()->sentence(),
        ];
    }
}
