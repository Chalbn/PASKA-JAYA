<?php

namespace Database\Factories;

use App\Enums\RolePengguna;
use App\Enums\StatusPengguna;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Factory khusus untuk pengujian (tests/).
 *
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'role_id' => fn () => $this->idRole(RolePengguna::Karyawan),
            'nama' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'no_hp' => '08'.fake()->unique()->numerify('1#########'),
            'password' => static::$password ??= Hash::make('password123'),
            'status' => StatusPengguna::Aktif,
            'alamat' => fake()->address(),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (): array => [
            'role_id' => $this->idRole(RolePengguna::Admin),
        ]);
    }

    public function owner(): static
    {
        return $this->state(fn (): array => [
            'role_id' => $this->idRole(RolePengguna::Owner),
        ]);
    }

    public function kasir(): static
    {
        return $this->state(fn (): array => [
            'role_id' => $this->idRole(RolePengguna::Kasir),
        ]);
    }

    public function karyawan(): static
    {
        return $this->state(fn (): array => [
            'role_id' => $this->idRole(RolePengguna::Karyawan),
        ]);
    }

    public function nonaktif(): static
    {
        return $this->state(fn (): array => [
            'status' => StatusPengguna::Nonaktif,
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (): array => [
            'email_verified_at' => null,
        ]);
    }

    private function idRole(RolePengguna $role): int
    {
        return Role::query()->firstOrCreate(
            ['nama' => $role->value],
            ['label' => $role->label()]
        )->id;
    }
}
