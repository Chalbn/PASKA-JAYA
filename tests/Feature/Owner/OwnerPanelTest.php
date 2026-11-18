<?php

use App\Livewire\Owner\DaftarBon;
use App\Livewire\Owner\Dashboard;
use App\Livewire\Owner\LaporanBarangKeluar;
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
use Livewire\Livewire;

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

    Livewire::actingAs($owner, 'web')
        ->test(Dashboard::class)
        ->assertSee('Ringkasan Toko')
        ->assertSee('Penjualan hari ini')
        ->assertSee('Rp 0')
        ->assertSee('0 transaksi')
        ->assertSee('0 barang')
        ->assertSee('0 pelanggan')
        ->assertSee('Penjualan 7 Hari Terakhir')
        ->assertSee('Semua stok aman')
        ->assertSee('Belum ada bon belum lunas')
        ->assertSee('Belum ada transaksi terbaru');

    Livewire::actingAs($owner, 'web')
        ->test(LaporanBarangKeluar::class)
        ->assertSee('Laporan Barang Keluar')
        ->assertSee('Unduh CSV')
        ->assertSee('0 transaksi')
        ->assertSee('Rp 0')
        ->assertSee('Belum ada transaksi barang keluar');

    Livewire::actingAs($owner, 'web')
        ->test(DaftarBon::class)
        ->assertSee('Daftar Bon')
        ->assertSee('Hanya Lihat')
        ->assertSee('Mode baca saja: pembayaran dan pengiriman bon hanya dapat dilakukan oleh Kasir.')
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

    // Angka dashboard harus sama persis dengan angka laporan hari ini
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

    Livewire::actingAs($owner, 'web')
        ->test(Dashboard::class)
        ->assertSee('Rp 690.000')
        ->assertSee('2 transaksi')
        ->assertSee('Rp 540.000')
        ->assertSee('Transfer: Rp 150.000')
        ->assertSee('1 barang')
        ->assertSee('Rp 1.000.000')
        ->assertSee('1 pelanggan')
        ->assertSee('Keramik Lantai 40x40')
        ->assertSee('4 Dus')
        ->assertSee('Pak Hendra (Kontraktor)')
        ->assertSee('Bu Lestari')
        ->assertSee('Kelola')
        ->assertSee('Laporan');
});

test('laporan barang keluar mendukung filter tanggal, filter metode, rincian item expand/tutup, paginasi, dan unduh CSV', function () {
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

    // 11 transaksi cash hari ini (untuk uji paginasi > 10 baris) + 1 transaksi transfer 2 hari lalu + 1 transaksi cash 15 hari lalu
    for ($i = 1; $i <= 11; $i++) {
        $penjualanService->simpanLangsung([
            'items' => [['barang_id' => $barang->id, 'jumlah' => 1]],
            'metode_bayar' => 'cash',
            'nama_pelanggan' => "Pelanggan Cash {$i}",
            'waktu_transaksi' => $sekarang->copy()->subMinutes($i),
        ], $kasir);
    }

    $trxTransfer = $penjualanService->simpanLangsung([
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

    // Uji default 7 hari terakhir: 11 cash (715.000) + 1 transfer (130.000) = 12 transaksi (845.000)
    $component = Livewire::actingAs($owner, 'web')
        ->test(LaporanBarangKeluar::class)
        ->assertSee('Rp 845.000')
        ->assertSee('12 transaksi')
        ->assertSee('Rp 715.000')
        ->assertSee('Rp 130.000')
        ->assertDontSee('Pak Lama 15 Hari');

    // Uji paginasi halaman 2 (10 per halaman -> sisa 2 transaksi di halaman 2, termasuk Bu Dewi Transfer yang paling lama di rentang 7 hari)
    $component->call('setPage', 2)
        ->assertSee('Bu Dewi Transfer');

    // Uji filter metode = transfer
    $component->set('metode', 'transfer')
        ->assertSee('Rp 130.000')
        ->assertSee('1 transaksi')
        ->assertSee('Bu Dewi Transfer')
        ->assertDontSee('Pelanggan Cash 1');

    // Uji expand tombol Rincian -> berubah jadi Tutup & tampil item + kasir
    $component->call('toggleRincian', $trxTransfer->id)
        ->assertSee('Tutup')
        ->assertSee('Semen Gresik 50kg')
        ->assertSee('2 Sak × Rp 65.000')
        ->assertSee('Kasir: Nadia Putri');

    // Klik lagi -> tutup kembali
    $component->call('toggleRincian', $trxTransfer->id)
        ->assertDontSee('Kasir: Nadia Putri');

    // Uji Unduh CSV sesuai filter aktif (hanya transfer)
    $namaFile = 'laporan-barang-keluar-'.$sekarang->format('Ymd').'.csv';
    $downloadResponse = $component->call('unduhCsv');
    $downloadResponse->assertFileDownloaded($namaFile);

    $csvContent = app(LaporanService::class)->buatCsv(
        $sekarang->copy()->subDays(6)->toDateString(),
        $sekarang->toDateString(),
        'transfer'
    );
    expect($csvContent)->toContain('Bu Dewi Transfer')
        ->and($csvContent)->toContain('Semen Gresik 50kg')
        ->and($csvContent)->not->toContain('Pelanggan Cash 1')
        ->and($csvContent)->not->toContain('Pak Lama 15 Hari');

    // Uji rentang tanggal diperluas ke 20 hari lalu -> Pak Lama 15 Hari ikut muncul
    $component->set('metode', 'semua')
        ->set('dariTanggal', $sekarang->copy()->subDays(20)->toDateString())
        ->assertSee('13 transaksi');
});

test('daftar bon owner read-only menampilkan filter status, badge lewat tempo, modal detail bon, timeline, dan foto bukti', function () {
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
    $bonBelumLunas = BonPiutang::factory()->create([
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

    $component = Livewire::actingAs($owner, 'web')
        ->test(DaftarBon::class)
        ->assertSee('Pak Hendra (Kontraktor)')
        ->assertSee('Lewat tempo')
        ->assertSee('Sisa Rp 1.000.000')
        ->assertSee('Bu Sari')
        ->assertSee('Budi Santoso');

    // Filter Belum lunas
    $component->call('setFilter', 'belum_lunas')
        ->assertSee('Pak Hendra (Kontraktor)')
        ->assertDontSee('Bu Sari');

    // Filter Lunas & buka detail bon
    $component->call('setFilter', 'lunas')
        ->assertSee('Bu Sari')
        ->assertDontSee('Pak Hendra (Kontraktor)')
        ->call('bukaDetail', $bonLunas->id)
        ->assertSee('Detail Bon')
        ->assertSee('Jl. Melati No. 5')
        ->assertSee('ALUR STATUS')
        ->assertSee('Foto bukti selesai diantar')
        ->assertSee(route('berkas.bukti-pengiriman', $pengiriman->id));
});
