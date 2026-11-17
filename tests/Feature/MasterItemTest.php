<?php

use App\Livewire\Shared\MasterItem\Index as MasterItemIndex;
use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\MutasiStok;
use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    // Pastikan role dasar ada di database
    Role::firstOrCreate(['nama' => 'admin'], ['label' => 'Admin']);
    Role::firstOrCreate(['nama' => 'owner'], ['label' => 'Owner']);
    Role::firstOrCreate(['nama' => 'kasir'], ['label' => 'Kasir']);
    Role::firstOrCreate(['nama' => 'karyawan'], ['label' => 'Karyawan']);
});

test('otorisasi rute master item: owner dan kasir diizinkan, karyawan ditolak', function () {
    $owner = User::factory()->owner()->create();
    $kasir = User::factory()->kasir()->create();
    $karyawan = User::factory()->karyawan()->create();

    // Owner akses /owner/master-item
    $this->actingAs($owner)->get('/owner/master-item')->assertOk();

    // Kasir akses /kasir/master-item
    $this->actingAs($kasir)->get('/kasir/master-item')->assertOk();

    // Karyawan akses ditolak (403)
    $this->actingAs($karyawan)->get('/owner/master-item')->assertForbidden();
    $this->actingAs($karyawan)->get('/kasir/master-item')->assertForbidden();

    // Tamu tanpa login diredirect (302)
    auth()->logout();
    $this->get('/owner/master-item')->assertRedirect('/login');
});

test('tampilan awal pada database kosong menampilkan empty state dan statistik 0', function () {
    $owner = User::factory()->owner()->create();

    Livewire::actingAs($owner, 'web')
        ->test(MasterItemIndex::class)
        ->assertSee('Master Item')
        ->assertSee('Belum ada barang')
        ->assertSee('Katalog master item masih kosong')
        ->assertSeeHtml('0 kategori terdaftar');
});

test('tambah barang baru berhasil dan kategori baru otomatis terdaftar di filter', function () {
    $owner = User::factory()->owner()->create();

    Livewire::actingAs($owner, 'web')
        ->test(MasterItemIndex::class)
        ->call('openTambahModal')
        ->set('kode_barang', 'BRG-999')
        ->set('nama_barang', 'Semen Gresik Super 50kg')
        ->set('kategori_nama', 'Semen & Pasir Unik')
        ->set('satuan', 'Sak')
        ->set('harga_beli', 60000)
        ->set('harga_jual', 72000)
        ->set('stok_awal', 25)
        ->set('stok_minimum', 10)
        ->call('simpanTambah')
        ->assertHasNoErrors()
        ->assertDispatched('toast');

    // Pastikan barang tersimpan di database
    $barang = Barang::where('kode_barang', 'BRG-999')->first();
    expect($barang)->not->toBeNull()
        ->and($barang->nama_barang)->toBe('Semen Gresik Super 50kg')
        ->and($barang->stok)->toBe(25);

    // Pastikan kategori baru dibuat otomatis (ADR-010)
    $kategori = KategoriBarang::where('nama', 'Semen & Pasir Unik')->first();
    expect($kategori)->not->toBeNull()
        ->and($barang->kategori_id)->toBe($kategori->id);

    // Pastikan stok awal masuk ke mutasi_stok
    $mutasi = MutasiStok::where('barang_id', $barang->id)
        ->where('jenis', 'stok_awal')
        ->first();
    expect($mutasi)->not->toBeNull()
        ->and($mutasi->jumlah)->toBe(25);

    // Kategori baru harus muncul di filter dropdown
    Livewire::actingAs($owner, 'web')
        ->test(MasterItemIndex::class)
        ->assertSee('Semen & Pasir Unik');
});

test('restock barang memanggil sp_restock_barang dan menambah stok serta memperbarui tanggal masuk', function () {
    $kasir = User::factory()->kasir()->create();
    $kategori = KategoriBarang::create(['nama' => 'Besi', 'slug' => 'besi']);

    $barang = Barang::create([
        'kode_barang' => 'BRG-777',
        'kategori_id' => $kategori->id,
        'nama_barang' => 'Besi Beton 8mm',
        'satuan' => 'Batang',
        'harga_beli' => 45000,
        'harga_jual' => 55000,
        'stok' => 10,
        'stok_minimum' => 5,
        'tanggal_masuk_terakhir' => now()->subDays(10)->toDateString(),
    ]);

    Livewire::actingAs($kasir, 'web')
        ->test(MasterItemIndex::class)
        ->call('openEditModal', $barang->id)
        ->assertSet('editBarangId', $barang->id)
        ->assertSet('edit_stok_saat_ini', 10)
        ->set('tambah_stok', 15) // Tambah 15 batang
        ->call('simpanEdit')
        ->assertHasNoErrors()
        ->assertDispatched('toast');

    // Periksa stok bertambah menjadi 10 + 15 = 25
    $barang->refresh();
    expect($barang->stok)->toBe(25)
        ->and($barang->tanggal_masuk_terakhir->toDateString())->toBe(now()->toDateString());

    // Periksa mutasi restock tercatat
    $mutasi = MutasiStok::where('barang_id', $barang->id)
        ->where('jenis', 'restock')
        ->latest('id')
        ->first();
    expect($mutasi)->not->toBeNull()
        ->and($mutasi->jumlah)->toBe(15);
});

test('edit atribut barang tanpa restock berhasil memperbarui data', function () {
    $owner = User::factory()->owner()->create();
    $kategori = KategoriBarang::create(['nama' => 'Cat', 'slug' => 'cat']);

    $barang = Barang::create([
        'kode_barang' => 'BRG-CAT1',
        'kategori_id' => $kategori->id,
        'nama_barang' => 'Cat Tembok Putih 5Kg',
        'satuan' => 'Pail',
        'harga_beli' => 75000,
        'harga_jual' => 90000,
        'stok' => 8,
        'stok_minimum' => 5,
    ]);

    Livewire::actingAs($owner, 'web')
        ->test(MasterItemIndex::class)
        ->call('openEditModal', $barang->id)
        ->set('edit_nama_barang', 'Cat Tembok Putih Super 5Kg')
        ->set('edit_harga_jual', 95000)
        ->set('tambah_stok', 0) // Tidak ada penambahan stok
        ->call('simpanEdit')
        ->assertHasNoErrors()
        ->assertDispatched('toast');

    $barang->refresh();
    expect($barang->nama_barang)->toBe('Cat Tembok Putih Super 5Kg')
        ->and($barang->harga_jual)->toBe(95000)
        ->and($barang->stok)->toBe(8); // Stok tidak berubah
});

test('hapus barang melakukan soft delete', function () {
    $owner = User::factory()->owner()->create();
    $kategori = KategoriBarang::create(['nama' => 'Kayu', 'slug' => 'kayu']);

    $barang = Barang::create([
        'kode_barang' => 'BRG-KAYU',
        'kategori_id' => $kategori->id,
        'nama_barang' => 'Kayu Kaso 4x6',
        'satuan' => 'Batang',
        'harga_beli' => 20000,
        'harga_jual' => 25000,
        'stok' => 50,
        'stok_minimum' => 10,
    ]);

    Livewire::actingAs($owner, 'web')
        ->test(MasterItemIndex::class)
        ->call('confirmDelete', $barang->id)
        ->assertSet('deleteBarangId', $barang->id)
        ->call('deleteBarang')
        ->assertDispatched('toast');

    // Barang ter-soft delete
    expect(Barang::find($barang->id))->toBeNull()
        ->and(Barang::withTrashed()->find($barang->id))->not->toBeNull();
});

test('pencarian dan filter kategori menyaring data barang', function () {
    $owner = User::factory()->owner()->create();
    $katSemen = KategoriBarang::create(['nama' => 'Semen', 'slug' => 'semen']);
    $katCat = KategoriBarang::create(['nama' => 'Cat', 'slug' => 'cat']);

    $semen = Barang::create([
        'kode_barang' => 'BRG-SMN1',
        'kategori_id' => $katSemen->id,
        'nama_barang' => 'Semen Tiga Roda',
        'satuan' => 'Sak',
        'harga_beli' => 60000,
        'harga_jual' => 70000,
        'stok' => 100,
        'stok_minimum' => 10,
    ]);

    $cat = Barang::create([
        'kode_barang' => 'BRG-CAT2',
        'kategori_id' => $katCat->id,
        'nama_barang' => 'Cat Dulux WeatherShield',
        'satuan' => 'Kaleng',
        'harga_beli' => 150000,
        'harga_jual' => 180000,
        'stok' => 15,
        'stok_minimum' => 5,
    ]);

    // Cari "Dulux"
    Livewire::actingAs($owner, 'web')
        ->test(MasterItemIndex::class)
        ->set('search', 'Dulux')
        ->assertSee('Cat Dulux WeatherShield')
        ->assertDontSee('Semen Tiga Roda');

    // Filter kategori "semen"
    Livewire::actingAs($owner, 'web')
        ->test(MasterItemIndex::class)
        ->set('kategori', 'semen')
        ->assertSee('Semen Tiga Roda')
        ->assertDontSee('Cat Dulux WeatherShield');
});

test('toggle view cards dan table berfungsi', function () {
    $owner = User::factory()->owner()->create();
    $kat = KategoriBarang::create(['nama' => 'Paku', 'slug' => 'paku']);

    Barang::create([
        'kode_barang' => 'BRG-PKU1',
        'kategori_id' => $kat->id,
        'nama_barang' => 'Paku Kayu 5cm',
        'satuan' => 'Kg',
        'harga_beli' => 12000,
        'harga_jual' => 16000,
        'stok' => 30,
        'stok_minimum' => 5,
    ]);

    // Mode default cards
    Livewire::actingAs($owner, 'web')
        ->test(MasterItemIndex::class)
        ->assertSet('view', 'cards')
        ->assertSeeHtml('BRG-PKU1')
        ->call('setView', 'table')
        ->assertSet('view', 'table')
        ->assertSee('Nama Barang')
        ->assertSee('Kode');
});
