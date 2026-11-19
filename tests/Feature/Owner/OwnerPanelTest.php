<?php

use App\Models\Barang;
use App\Models\BonPiutang;
use App\Models\DetailPenjualan;
use App\Models\KategoriBarang;
use App\Models\LogStatusPengiriman;
use App\Models\Pelanggan;
use App\Models\PengirimanBarang;
use App\Models\Penjualan;
use App\Models\User;
use App\Services\LaporanService;
use App\Services\PenjualanService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

test('otorisasi rute owner dashboard, laporan, dan daftar bon: hanya owner yang diizinkan', function () {
    $owner = User::factory()->owner()->create();
    $admin = User::factory()->admin()->create();
    $kasir = User::factory()->kasir()->create();
    $karyawan = User::factory()->karyawan()->create();

    $routes = [
        '/owner',
        '/owner/laporan-barang-keluar',
        '/owner/daftar-bon',
    ];

    foreach ($routes as $url) {
        $this->actingAs($owner)->get($url)->assertOk();
        $this->actingAs($admin)->get($url)->assertForbidden();
        $this->actingAs($kasir)->get($url)->assertForbidden();
        $this->actingAs($karyawan)->get($url)->assertForbidden();

        auth()->logout();
        $this->get($url)->assertRedirect('/login');
    }
});

test('policy bon piutang menolak seluruh aksi tulis oleh owner', function () {
    $owner = User::factory()->owner()->create();
    $kasir = User::factory()->kasir()->create();
    $bon = BonPiutang::factory()->create(['dibuat_oleh' => $kasir->id]);

    expect($owner->can('viewAny', BonPiutang::class))->toBeTrue()
        ->and($owner->can('view', $bon))->toBeTrue()
        ->and($owner->can('create', BonPiutang::class))->toBeFalse()
        ->and($owner->can('update', $bon))->toBeFalse()
        ->and($owner->can('delete', $bon))->toBeFalse()
        ->and($owner->can('kirim', $bon))->toBeFalse()
        ->and($owner->can('bayar', $bon))->toBeFalse();
});

test('indeks waktu_transaksi pada tabel penjualan tersedia di database', function () {
    $indexes = DB::select("SHOW INDEX FROM penjualan WHERE Column_name = 'waktu_transaksi'");

    expect($indexes)->not->toBeEmpty()
        ->and($indexes[0]->Key_name)->toBe('idx_penjualan_waktu_transaksi');
});

test('halaman owner menampilkan nilai 0 dan empty state dengan rapi saat database kosong', function () {
    $owner = User::factory()->owner()->create();

    $this->actingAs($owner)
        ->get('/owner')
        ->assertOk()
        ->assertSee('Ringkasan Toko')
        ->assertSee('Penjualan Hari Ini')
        ->assertSee('Rp 0')
        ->assertSee('0 transaksi')
        ->assertSee('0 barang')
        ->assertSee('Stok Aman')
        ->assertSee('Tidak Ada Piutang');

    $this->actingAs($owner)
        ->get('/owner/laporan-barang-keluar')
        ->assertOk()
        ->assertSee('Laporan Barang Keluar')
        ->assertSee('Belum ada transaksi');

    $this->actingAs($owner)
        ->get('/owner/daftar-bon')
        ->assertOk()
        ->assertSee('Daftar Bon')
        ->assertSee('Hanya Lihat')
        ->assertSee('Belum ada data bon');
});

test('laporan service dan dashboard owner menampilkan angka nyata konsisten dari database dan view', function () {
    $owner = User::factory()->owner()->create();
    $kasir = User::factory()->kasir()->create(['nama' => 'Nadia Putri']);
    $kategori = KategoriBarang::factory()->create(['nama' => 'Cat & Keramik']);

    $barangStokMenipis = Barang::factory()->create([
        'kategori_id' => $kategori->id,
        'nama_barang' => 'Keramik Lantai 40x40',
        'satuan' => 'Dus',
        'harga_jual' => 75000,
        'stok' => 6,
        'stok_minimum' => 10,
    ]);

    $barangNormal = Barang::factory()->create([
        'kategori_id' => $kategori->id,
        'nama_barang' => 'Cat Tembok White 5kg',
        'satuan' => 'Kaleng',
        'harga_jual' => 135000,
        'stok' => 30,
        'stok_minimum' => 5,
    ]);

    $penjualanService = app(PenjualanService::class);

    // Transaksi Cash hari ini: 4 * 135.000 = 540.000
    $penjualanService->simpanLangsung([
        'items' => [
            ['barang_id' => $barangNormal->id, 'jumlah' => 4],
        ],
        'metode_bayar' => 'cash',
        'nama_pelanggan' => 'Pak Joko',
    ], $kasir);

    // Transaksi Transfer hari ini: 2 * 75.000 = 150.000 (sisa stok 6 -> 4)
    $penjualanService->simpanLangsung([
        'items' => [
            ['barang_id' => $barangStokMenipis->id, 'jumlah' => 2],
        ],
        'metode_bayar' => 'transfer',
        'nama_pelanggan' => 'Bu Lestari',
    ], $kasir);

    // Bon belum lunas
    $pelanggan = Pelanggan::factory()->create(['nama' => 'Pak Hendra (Kontraktor)']);
    $penjualanBon = Penjualan::factory()->create([
        'tipe' => 'bon',
        'pelanggan_id' => $pelanggan->id,
        'nama_pelanggan' => $pelanggan->nama,
        'total' => 1200000,
        'user_id' => $kasir->id,
    ]);
    BonPiutang::factory()->create([
        'penjualan_id' => $penjualanBon->id,
        'pelanggan_id' => $pelanggan->id,
        'jatuh_tempo' => Carbon::now('Asia/Jakarta')->addDays(7)->toDateString(),
        'total_tagihan' => 1200000,
        'total_terbayar' => 200000,
        'status_bayar' => 'belum_lunas',
        'dibuat_oleh' => $kasir->id,
    ]);

    $laporanService = app(LaporanService::class);
    $ringkasan = $laporanService->ringkasanToko();
    $hariIni = Carbon::now('Asia/Jakarta')->toDateString();
    $laporanHariIni = $laporanService->ambilLaporanPenjualan($hariIni, $hariIni, 'semua');

    expect($ringkasan['penjualan_hari_ini'])->toBe(690000)
        ->and($ringkasan['penjualan_hari_ini'])->toBe($laporanHariIni['ringkasan']['total_penjualan'])
        ->and($ringkasan['transaksi_hari_ini'])->toBe(2)
        ->and($ringkasan['transaksi_hari_ini'])->toBe($laporanHariIni['ringkasan']['jumlah_transaksi'])
        ->and($ringkasan['saldo_cash'])->toBe(540000)
        ->and($ringkasan['saldo_transfer'])->toBe(150000)
        ->and($ringkasan['stok_menipis_count'])->toBe(1)
        ->and($ringkasan['bon_belum_lunas_total'])->toBe(1000000)
        ->and($ringkasan['bon_belum_lunas_pelanggan_count'])->toBe(1)
        ->and($ringkasan['grafik_7_hari'])->toHaveCount(7)
        ->and(end($ringkasan['grafik_7_hari'])['total'])->toBe(690000);

    $this->actingAs($owner)
        ->get('/owner')
        ->assertOk()
        ->assertSee('Rp 690.000')
        ->assertSee('2 transaksi')
        ->assertSee('1 barang')
        ->assertSee('Rp 1.000.000')
        ->assertSee('Keramik Lantai 40x40')
        ->assertSee('4 Dus')
        ->assertSee('Pak Hendra (Kontraktor)');
});

test('laporan barang keluar mendukung filter tanggal, filter metode, dan layanan CSV', function () {
    $owner = User::factory()->owner()->create();
    $kasir = User::factory()->kasir()->create(['nama' => 'Nadia Putri']);
    $barang = Barang::factory()->create([
        'nama_barang' => 'Semen Gresik 50kg',
        'satuan' => 'Sak',
        'harga_jual' => 65000,
        'stok' => 200,
    ]);

    $sekarang = Carbon::now('Asia/Jakarta');
    $penjualanService = app(PenjualanService::class);

    for ($i = 1; $i <= 11; $i++) {
        $penjualanService->simpanLangsung([
            'items' => [['barang_id' => $barang->id, 'jumlah' => 1]],
            'metode_bayar' => 'cash',
            'nama_pelanggan' => "Pelanggan Cash {$i}",
            'waktu_transaksi' => $sekarang->copy()->subMinutes($i),
        ], $kasir);
    }

    $penjualanService->simpanLangsung([
        'items' => [['barang_id' => $barang->id, 'jumlah' => 2]],
        'metode_bayar' => 'transfer',
        'nama_pelanggan' => 'Bu Dewi Transfer',
        'waktu_transaksi' => $sekarang->copy()->subDays(2),
    ], $kasir);

    $penjualanService->simpanLangsung([
        'items' => [['barang_id' => $barang->id, 'jumlah' => 5]],
        'metode_bayar' => 'cash',
        'nama_pelanggan' => 'Pak Lama 15 Hari',
        'waktu_transaksi' => $sekarang->copy()->subDays(15),
    ], $kasir);

    // Default 7 hari terakhir
    $this->actingAs($owner)
        ->get('/owner/laporan-barang-keluar')
        ->assertOk()
        ->assertSee('Pelanggan Cash 1')
        ->assertSee('Bu Dewi Transfer')
        ->assertDontSee('Pak Lama 15 Hari');

    // Filter metode = transfer
    $this->actingAs($owner)
        ->get('/owner/laporan-barang-keluar?metode=transfer')
        ->assertOk()
        ->assertSee('Bu Dewi Transfer')
        ->assertDontSee('Pelanggan Cash 1');

    // Verifikasi CSV via LaporanService sesuai filter aktif (hanya transfer)
    $csvContent = app(LaporanService::class)->buatCsv(
        $sekarang->copy()->subDays(6)->toDateString(),
        $sekarang->toDateString(),
        'transfer'
    );
    expect($csvContent)->toContain('Bu Dewi Transfer')
        ->and($csvContent)->toContain('Semen Gresik 50kg')
        ->and($csvContent)->not->toContain('Pelanggan Cash 1')
        ->and($csvContent)->not->toContain('Pak Lama 15 Hari');

    // Rentang tanggal diperluas ke 20 hari lalu -> Pak Lama 15 Hari ikut muncul
    $this->actingAs($owner)
        ->get('/owner/laporan-barang-keluar?metode=semua&dari='.$sekarang->copy()->subDays(20)->toDateString())
        ->assertOk()
        ->assertSee('Pak Lama 15 Hari');
});

test('daftar bon owner read-only menampilkan filter status dan endpoint detail bon JSON', function () {
    $owner = User::factory()->owner()->create();
    $kasir = User::factory()->kasir()->create();
    $karyawan = User::factory()->karyawan()->create(['nama' => 'Budi Santoso']);
    $barang = Barang::factory()->create(['nama_barang' => 'Semen Gresik 50kg', 'satuan' => 'Sak', 'harga_jual' => 65000]);

    $pelanggan1 = Pelanggan::factory()->create(['nama' => 'Pak Hendra (Kontraktor)']);
    $penjualan1 = Penjualan::factory()->create([
        'tipe' => 'bon',
        'pelanggan_id' => $pelanggan1->id,
        'nama_pelanggan' => $pelanggan1->nama,
        'total' => 1300000,
        'user_id' => $kasir->id,
    ]);
    DetailPenjualan::create([
        'penjualan_id' => $penjualan1->id,
        'barang_id' => $barang->id,
        'nama_barang' => $barang->nama_barang,
        'satuan' => $barang->satuan,
        'jumlah' => 20,
        'harga_satuan' => 65000,
        'subtotal' => 1300000,
    ]);
    BonPiutang::factory()->create([
        'penjualan_id' => $penjualan1->id,
        'pelanggan_id' => $pelanggan1->id,
        'alamat_pengantaran' => 'Jl. Pembangunan No. 10',
        'jatuh_tempo' => Carbon::now('Asia/Jakarta')->subDays(2)->toDateString(),
        'total_tagihan' => 1300000,
        'total_terbayar' => 300000,
        'status_bayar' => 'belum_lunas',
        'dibuat_oleh' => $kasir->id,
    ]);

    $pelanggan2 = Pelanggan::factory()->create(['nama' => 'Bu Sari']);
    $penjualan2 = Penjualan::factory()->create([
        'tipe' => 'bon',
        'pelanggan_id' => $pelanggan2->id,
        'nama_pelanggan' => $pelanggan2->nama,
        'total' => 650000,
        'user_id' => $kasir->id,
    ]);
    DetailPenjualan::create([
        'penjualan_id' => $penjualan2->id,
        'barang_id' => $barang->id,
        'nama_barang' => $barang->nama_barang,
        'satuan' => $barang->satuan,
        'jumlah' => 10,
        'harga_satuan' => 65000,
        'subtotal' => 650000,
    ]);
    $bonLunas = BonPiutang::factory()->create([
        'penjualan_id' => $penjualan2->id,
        'pelanggan_id' => $pelanggan2->id,
        'alamat_pengantaran' => 'Jl. Melati No. 5',
        'jatuh_tempo' => Carbon::now('Asia/Jakarta')->addDays(5)->toDateString(),
        'total_tagihan' => 650000,
        'total_terbayar' => 650000,
        'status_bayar' => 'lunas',
        'dibuat_oleh' => $kasir->id,
    ]);
    $pengiriman = PengirimanBarang::factory()->selesai()->create([
        'bon_id' => $bonLunas->id,
        'karyawan_id' => $karyawan->id,
        'foto_bukti' => 'bukti-pengiriman/test-owner.jpg',
        'dibuat_oleh' => $kasir->id,
    ]);
    LogStatusPengiriman::create([
        'bon_id' => $bonLunas->id,
        'pengiriman_id' => $pengiriman->id,
        'status' => 'selesai',
        'user_id' => $karyawan->id,
        'catatan' => 'Sudah diterima',
    ]);

    $this->actingAs($owner)
        ->get('/owner/daftar-bon')
        ->assertOk()
        ->assertSee('Pak Hendra (Kontraktor)')
        ->assertSee('Rp 1.000.000')
        ->assertSee('Bu Sari');

    // Filter Belum lunas
    $this->actingAs($owner)
        ->get('/owner/daftar-bon?status_bayar=belum_lunas')
        ->assertOk()
        ->assertSee('Pak Hendra (Kontraktor)')
        ->assertDontSee('Bu Sari');

    // Filter Lunas
    $this->actingAs($owner)
        ->get('/owner/daftar-bon?status_bayar=lunas')
        ->assertOk()
        ->assertSee('Bu Sari')
        ->assertDontSee('Pak Hendra (Kontraktor)');

    // Detail bon JSON
    $this->actingAs($owner)
        ->getJson("/owner/daftar-bon/{$bonLunas->id}")
        ->assertOk()
        ->assertJsonPath('bon.bon_id', $bonLunas->id)
        ->assertJsonPath('bon.alamat_pengantaran', 'Jl. Melati No. 5')
        ->assertJsonCount(1, 'items')
        ->assertJsonCount(2, 'log_pengiriman');
});