<?php

namespace Database\Seeders;

use App\Enums\StatusPengguna;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan tabel roles sudah terisi
        $this->call([
            RoleSeeder::class,
        ]);

        // 1. Akun Admin (1 akun)
        User::updateOrCreate(
            ['email' => 'adminpaskajaya@gmail.com'],
            [
                'role_id' => Role::where('nama', 'admin')->first()->id,
                'nama' => 'Admin Paska Jaya',
                'no_hp' => '087856251735',
                'password' => 'Admin123!',
                'status' => StatusPengguna::Aktif,
                'email_verified_at' => now(),
            ]
        );

        // 2. Akun Owner (1 akun)
        User::updateOrCreate(
            ['email' => 'paskajayaowner@gmail.com'],
            [
                'role_id' => Role::where('nama', 'owner')->first()->id,
                'nama' => 'Owner Paska Jaya',
                'no_hp' => '081377489793',
                'password' => 'Owner123!',
                'status' => StatusPengguna::Aktif,
                'email_verified_at' => null,
            ]
        );

        // 3. Akun Kasir (1 akun)
        User::updateOrCreate(
            ['email' => 'kasirpaskajaya@gmail.com'],
            [
                'role_id' => Role::where('nama', 'kasir')->first()->id,
                'nama' => 'Kasir Paska Jaya',
                'no_hp' => '085211336469',
                'password' => 'Kasir123!',
                'status' => StatusPengguna::Aktif,
                'email_verified_at' => null,
            ]
        );
    }
}