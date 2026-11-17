<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'role_id' => Role::where('nama', 'karyawan')->value('id') ?? 4,
            'nama' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'no_hp' => fake()->unique()->optional()->e164PhoneNumber(),
            'password' => static::$password ??= Hash::make('password'),
            'status' => 'aktif',
            'alamat' => fake()->optional()->address(),
            'foto_profil' => null,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role_id' => Role::where('nama', 'admin')->value('id') ?? 1,
        ]);
    }

    public function owner(): static
    {
        return $this->state(fn () => [
            'role_id' => Role::where('nama', 'owner')->value('id') ?? 2,
        ]);
    }

    public function kasir(): static
    {
        return $this->state(fn () => [
            'role_id' => Role::where('nama', 'kasir')->value('id') ?? 3,
        ]);
    }

    public function karyawan(): static
    {
        return $this->state(fn () => [
            'role_id' => Role::where('nama', 'karyawan')->value('id') ?? 4,
        ]);
    }
}
