<?php

use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    Role::firstOrCreate(['nama' => 'admin'], ['label' => 'Admin']);
    Role::firstOrCreate(['nama' => 'owner'], ['label' => 'Owner']);
    Role::firstOrCreate(['nama' => 'kasir'], ['label' => 'Kasir']);
    Role::firstOrCreate(['nama' => 'karyawan'], ['label' => 'Karyawan']);
});

test('otorisasi rute saldo laci: hanya kasir diizinkan, role lain ditolak', function () {
    $kasir = User::factory()->kasir()->create();
    $owner = User::factory()->owner()->create();
    $karyawan = User::factory()->karyawan()->create();
    $admin = User::factory()->admin()->create();

    $this->actingAs($owner)->get('/kasir/saldo')->assertForbidden();
    $this->actingAs($karyawan)->get('/kasir/saldo')->assertForbidden();
    $this->actingAs($admin)->get('/kasir/saldo')->assertForbidden();

    auth()->logout();
    $this->get('/kasir/saldo')->assertRedirect('/login');
});
