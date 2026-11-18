<?php

use App\Livewire\Kasir\BarangKeluar;
use App\Livewire\Kasir\Beranda;
use App\Models\Barang;
use App\Models\KasLaci;
use App\Models\MutasiStok;
use App\Models\Penjualan;
use App\Models\User;
use App\Services\PenjualanService;
use Livewire\Livewire;

test('otorisasi rute kasir beranda dan barang keluar: hanya kasir diizinkan', function () {
    $kasir = User::factory()->kasir()->create();
    $owner = User::factory()->owner()->create();
    $karyawan = User::factory()->karyawan()->create();
    $admin = User::factory()->admin()->create();

    // Kasir diizinkan (200)
    $this->actingAs($kasir)->get('/kasir')->assertOk();
    $this->actingAs($kasir)->get('/kasir/barang-keluar')->assertOk();

    // Owner, Karyawan, Admin ditolak (403)
    $this->actingAs($owner)->get('/kasir')->assertForbidden();
    $this->actingAs($owner)->get('/kasir/barang-keluar')->assertForbidden();

    $this->actingAs($karyawan)->get('/kasir')->assertForbidden();
    $this->actingAs($karyawan)->get('/kasir/barang-keluar')->assertForbidden();

    $this->actingAs($admin)->get('/kasir')->assertForbidden();
    $this->actingAs($admin)->get('/kasir/barang-keluar')->assertForbidden();

    // Tamu tanpa login diredirect ke login (302)
    auth()->logout();
    $this->get('/kasir')->assertRedirect('/login');
    $this->get('/kasir/barang-keluar')->assertRedirect('/login');
});

test('beranda kasir pada kondisi awal menampilkan saldo 0 dan empty state tanpa error', function () {
    $kasir = User::factory()->kasir()->create();

    Livewire::actingAs($kasir, 'web')
        ->test(Beranda::class)
        ->assertSee('Beranda Kasir')
        ->assertSee('SALDO LACI HARI INI')
        ->assertSee('Rp 0')
        ->assertSee('Belum ada transaksi hari ini')
        ->assertSee('Transaksi Hari Ini')
        ->assertSee('Stok Menipis')
        ->assertSee('Bon Belum Lunas');
});

test('barang keluar pada kondisi awal menampilkan form kosong dan keranjang kosong', function () {
    $kasir = User::factory()->kasir()->create();

    Livewire::actingAs($kasir, 'web')
        ->test(BarangKeluar::class)
        ->assertSee('Barang Keluar')
        ->assertSee('1. Pilih barang')
        ->assertSee('2. Keranjang')
        ->assertSee('Belum ada barang. Pilih barang lalu klik Tambah.')
        ->assertSee('3. Pembayaran')
        ->assertSee('Total bayar')
        ->assertSee('Riwayat Barang Keluar')
        ->assertSee('Belum ada transaksi');
});

test('simpanLangsung berhasil memotong stok, membuat mutasi stok, mengisi kas laci, dan menghasilkan TRX berurutan', function () {
    $kasir = User::factory()->kasir()->create();

    $barang1 = Barang::factory()->create([
        'nama_barang' => 'Semen Padang 50kg',
        'harga_jual' => 65000,
        'stok' => 50,
    ]);

    $barang2 = Barang::factory()->create([
        'nama_barang' => 'Paku Beton 2 Inch',
        'harga_jual' => 20000,
        'stok' => 30,
    ]);

    $service = app(PenjualanService::class);

    $penjualan = $service->simpanLangsung([
        'items' => [
            ['barang_id' => $barang1->id, 'jumlah' => 2],
            ['barang_id' => $barang2->id, 'jumlah' => 3],
        ],
        'metode_bayar' => 'cash',
        'nama_pelanggan' => 'Pak Joko',
    ], $kasir);

    // Total = (2 * 65.000) + (3 * 20.000) = 130.000 + 60.000 = 190.000
    expect($penjualan)->toBeInstanceOf(Penjualan::class)
        ->and($penjualan->total)->toBe(190000)
        ->and($penjualan->kode_transaksi)->toMatch('/^TRX-\d{4}$/')
        ->and($penjualan->nama_pelanggan)->toBe('Pak Joko');

    // Trigger T2: Memotong stok
    expect($barang1->fresh()->stok)->toBe(48)
        ->and($barang2->fresh()->stok)->toBe(27);

    // Mutasi stok tercatat
    $mutasi1 = MutasiStok::where('barang_id', $barang1->id)->where('jenis', 'penjualan')->first();
    expect($mutasi1)->not->toBeNull()
        ->and($mutasi1->jumlah)->toBe(-2);

    // Trigger T3: Masuk ke kas laci
    $kas = KasLaci::where('sumber_tipe', 'penjualan')
        ->where('sumber_id', $penjualan->id)
        ->first();

    expect($kas)->not->toBeNull()
        ->and($kas->metode->value ?? $kas->metode)->toBe('cash')
        ->and((int) $kas->jumlah)->toBe(190000)
        ->and($kas->penutupan_id)->toBeNull();
});

test('simpanLangsung menolak transaksi jika stok barang tidak mencukupi dengan format pesan yang tepat', function () {
    $kasir = User::factory()->kasir()->create();

    $barang = Barang::factory()->create([
        'nama_barang' => 'Bata Ringan Hebel',
        'harga_jual' => 10000,
        'stok' => 3,
    ]);

    $service = app(PenjualanService::class);

    expect(fn () => $service->simpanLangsung([
        'items' => [
            ['barang_id' => $barang->id, 'jumlah' => 10],
        ],
        'metode_bayar' => 'cash',
    ], $kasir))->toThrow(
        RuntimeException::class,
        'Stok Bata Ringan Hebel tidak cukup (tersisa 3)'
    );

    // Pastikan stok tidak berkurang dan tidak ada record penjualan tersimpan
    expect($barang->fresh()->stok)->toBe(3);
    expect(Penjualan::where('nama_pelanggan', 'Umum')->where('total', 100000)->count())->toBe(0);
});

test('livewire barang keluar dapat menambah barang ke keranjang, menggabungkan item sama, dan menyelesaikan transaksi', function () {
    $kasir = User::factory()->kasir()->create();

    $barang = Barang::factory()->create([
        'nama_barang' => 'Cat Tembok Putih 5kg',
        'harga_jual' => 120000,
        'stok' => 15,
        'satuan' => 'Pail',
    ]);

    $component = Livewire::actingAs($kasir, 'web')
        ->test(BarangKeluar::class)
        ->set('selectedBarangId', $barang->id)
        ->set('jumlah', 2)
        ->call('tambahItem')
        ->assertHasNoErrors();

    // Verifikasi item di keranjang
    expect($component->get('keranjang'))->toHaveCount(1)
        ->and($component->get('keranjang')[0]['jumlah'])->toBe(2)
        ->and($component->get('keranjang')[0]['subtotal'])->toBe(240000);

    // Tambah barang yang sama lagi -> harus digabung (2 + 3 = 5)
    $component->set('selectedBarangId', $barang->id)
        ->set('jumlah', 3)
        ->call('tambahItem')
        ->assertHasNoErrors();

    expect($component->get('keranjang'))->toHaveCount(1)
        ->and($component->get('keranjang')[0]['jumlah'])->toBe(5)
        ->and($component->get('keranjang')[0]['subtotal'])->toBe(600000);

    // Kurangi 1 dari keranjang
    $component->call('ubahJumlah', $barang->id, -1);
    expect($component->get('keranjang')[0]['jumlah'])->toBe(4)
        ->and($component->get('keranjang')[0]['subtotal'])->toBe(480000);

    // Simpan transaksi
    $component->set('namaPelanggan', 'Bu Dewi')
        ->set('metodeBayar', 'transfer')
        ->call('simpanTransaksi')
        ->assertDispatched('toast');

    // Keranjang harus kembali kosong
    expect($component->get('keranjang'))->toBeEmpty();

    // Stok berkurang sebanyak 4
    expect($barang->fresh()->stok)->toBe(11);

    // Penjualan tersimpan
    $penjualan = Penjualan::where('nama_pelanggan', 'Bu Dewi')->latest('id')->first();
    expect($penjualan)->not->toBeNull()
        ->and($penjualan->total)->toBe(480000)
        ->and($penjualan->metode_bayar->value ?? $penjualan->metode_bayar)->toBe('transfer');
});

test('beranda kasir dapat melakukan reset manual laci', function () {
    $kasir = User::factory()->kasir()->create();

    // Buat kas laci aktif
    KasLaci::create([
        'tanggal' => now()->toDateString(),
        'waktu' => now(),
        'keterangan' => 'Penjualan TRX-TEST',
        'metode' => 'cash',
        'jumlah' => 500000,
        'sumber_tipe' => 'penjualan',
        'sumber_id' => 99999,
        'penutupan_id' => null,
    ]);

    $component = Livewire::actingAs($kasir, 'web')
        ->test(Beranda::class);

    expect($component->get('saldoTotal'))->toBe(500000);

    // Jalankan reset manual
    $component->call('resetManual')
        ->assertDispatched('toast');

    // Saldo aktif harus kembali menjadi 0
    expect($component->get('saldoTotal'))->toBe(0);
});
