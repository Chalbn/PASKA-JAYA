<?php

use App\Models\User;

test('otorisasi rute kasir beranda dan barang keluar: hanya kasir diizinkan', function () {
    $kasir = User::factory()->kasir()->create();
    $owner = User::factory()->owner()->create();
    $karyawan = User::factory()->karyawan()->create();
    $admin = User::factory()->admin()->create();

    $this->actingAs($kasir)->get('/kasir')->assertOk()->assertSee('Beranda Kasir');
    $this->actingAs($kasir)->get('/kasir/barang-keluar')->assertOk()->assertSee('Barang Keluar');

    $this->actingAs($owner)->get('/kasir')->assertForbidden();
    $this->actingAs($owner)->get('/kasir/barang-keluar')->assertForbidden();

    $this->actingAs($karyawan)->get('/kasir')->assertForbidden();
    $this->actingAs($karyawan)->get('/kasir/barang-keluar')->assertForbidden();

    $this->actingAs($admin)->get('/kasir')->assertForbidden();
    $this->actingAs($admin)->get('/kasir/barang-keluar')->assertForbidden();

    auth()->logout();
    $this->get('/kasir')->assertRedirect('/login');
});
