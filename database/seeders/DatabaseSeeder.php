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
            ['email' => 'admin@paskajaya.test'],
            [
                'role_id' => Role::where('nama', 'admin')->first()->id,
                'nama' => 'Admin Paska Jaya',
                'no_hp' => '081200000001',
                'password' => 'Password123!',
                'status' => StatusPengguna::Aktif,
                'email_verified_at' => now(),
            ]
        );

        // 2. Akun Owner (1 akun)
        User::updateOrCreate(
            ['email' => 'owner@paskajaya.test'],
            [
                'role_id' => Role::where('nama', 'owner')->first()->id,
                'nama' => 'Owner Paska Jaya',
                'no_hp' => '081200000002',
                'password' => 'Password123!',
                'status' => StatusPengguna::Aktif,
                'email_verified_at' => now(),
            ]
        );

        // 3. Akun Kasir (1 akun)
        User::updateOrCreate(
            ['email' => 'kasir@paskajaya.test'],
            [
                'role_id' => Role::where('nama', 'kasir')->first()->id,
                'nama' => 'Kasir Paska Jaya',
                'no_hp' => '081200000003',
                'password' => 'Password123!',
                'status' => StatusPengguna::Aktif,
                'email_verified_at' => now(),
            ]
        );
    }
}