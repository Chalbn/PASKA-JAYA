<?php

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    Role::firstOrCreate(['nama' => 'admin'], ['label' => 'Admin']);
    Role::firstOrCreate(['nama' => 'owner'], ['label' => 'Owner']);
    Role::firstOrCreate(['nama' => 'kasir'], ['label' => 'Kasir']);
    Role::firstOrCreate(['nama' => 'karyawan'], ['label' => 'Karyawan']);
});

test('otorisasi rute master item: owner dan kasir diizinkan, karyawan ditolak', function () {
    $owner = User::factory()->owner()->create();
    $kasir = User::factory()->kasir()->create();
    $karyawan = User::factory()->karyawan()->create();

    $this->actingAs($owner)->get('/owner/master-item')->assertOk();
    $this->actingAs($kasir)->get('/kasir/master-item')->assertOk();

    $this->actingAs($karyawan)->get('/owner/master-item')->assertForbidden();
    $this->actingAs($karyawan)->get('/kasir/master-item')->assertForbidden();

    auth()->logout();
    $this->get('/owner/master-item')->assertRedirect('/login');
});

test('master item controller menampilkan daftar barang dan filter pencarian', function () {
    $owner = User::factory()->owner()->create();
    $kategori = KategoriBarang::create(['nama' => 'Semen', 'slug' => 'semen']);
    Barang::create([
        'kode_barang' => 'BRG-001',
        'kategori_id' => $kategori->id,
        'nama_barang' => 'Semen Gresik 50kg',
        'satuan' => 'Sak',
        'harga_beli' => 55000,
        'harga_jual' => 65000,
        'stok' => 25,
        'stok_minimum' => 10,
    ]);

    $this->actingAs($owner)
        ->get('/owner/master-item?search=Gresik')
        ->assertOk()
        ->assertSee('Semen Gresik 50kg');
});
