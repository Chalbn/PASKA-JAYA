<?php

use App\Livewire\Kasir\GrafikMingguan;
use App\Livewire\Kasir\LihatSaldo;
use App\Livewire\Kasir\RiwayatSaldoLaci;
use App\Models\KasLaci;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\PenutupanLaci;
use App\Models\Role;
use App\Models\User;
use App\Services\LaciService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

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

    // Kasir diizinkan (200)
    $this->actingAs($kasir)->get('/kasir/saldo')->assertOk();
    $this->actingAs($kasir)->get('/kasir/grafik-mingguan')->assertOk();
    $this->actingAs($kasir)->get('/kasir/riwayat-saldo')->assertOk();

    // Owner ditolak (403)
    $this->actingAs($owner)->get('/kasir/saldo')->assertForbidden();
    $this->actingAs($owner)->get('/kasir/grafik-mingguan')->assertForbidden();
    $this->actingAs($owner)->get('/kasir/riwayat-saldo')->assertForbidden();

    // Karyawan ditolak (403)
    $this->actingAs($karyawan)->get('/kasir/saldo')->assertForbidden();
    $this->actingAs($karyawan)->get('/kasir/grafik-mingguan')->assertForbidden();
    $this->actingAs($karyawan)->get('/kasir/riwayat-saldo')->assertForbidden();

    // Admin ditolak (403)
    $this->actingAs($admin)->get('/kasir/saldo')->assertForbidden();
    $this->actingAs($admin)->get('/kasir/grafik-mingguan')->assertForbidden();
    $this->actingAs($admin)->get('/kasir/riwayat-saldo')->assertForbidden();

    // Tamu tanpa login diredirect ke login (302)
    auth()->logout();
    $this->get('/kasir/saldo')->assertRedirect('/login');
    $this->get('/kasir/grafik-mingguan')->assertRedirect('/login');
    $this->get('/kasir/riwayat-saldo')->assertRedirect('/login');
});

test('tampilan awal pada database kosong menampilkan saldo 0, grafik 0, dan empty state', function () {
    $kasir = User::factory()->kasir()->create();

    // Lihat Saldo
    Livewire::actingAs($kasir, 'web')
        ->test(LihatSaldo::class)
        ->assertSee('Lihat Saldo')
        ->assertSee('Saldo Aktif Saat Ini')
        ->assertSee('Rp 0')
        ->assertSee('0 transaksi tercatat pada saldo aktif saat ini')
        ->assertSee('Belum ada transaksi aktif')
        ->assertSee('Belum ada riwayat');

    // Grafik Mingguan
    Livewire::actingAs($kasir, 'web')
        ->test(GrafikMingguan::class)
        ->assertSee('Grafik Penjualan Mingguan')
        ->assertSee('Total Penjualan')
        ->assertSee('Rp 0')
        ->assertSee('Hari Tertinggi')
        ->assertSee('Belum ada transaksi');

    // Riwayat Saldo Laci
    Livewire::actingAs($kasir, 'web')
        ->test(RiwayatSaldoLaci::class)
        ->assertSee('Riwayat Saldo Laci')
        ->assertSee('Belum ada riwayat');
});

test('reset manual laci memanggil sp_reset_laci dan membukukan transaksi aktif', function () {
    $kasir = User::factory()->kasir()->create();

    // Masukkan transaksi kas_laci aktif
    KasLaci::create([
        'tanggal' => now()->toDateString(),
        'waktu' => now(),
        'keterangan' => 'Penjualan TRX-TEST-001',
        'metode' => 'cash',
        'jumlah' => 150000,
        'sumber_tipe' => 'penjualan',
        'sumber_id' => 1,
    ]);

    KasLaci::create([
        'tanggal' => now()->toDateString(),
        'waktu' => now(),
        'keterangan' => 'Penjualan TRX-TEST-002',
        'metode' => 'transfer',
        'jumlah' => 250000,
        'sumber_tipe' => 'penjualan',
        'sumber_id' => 2,
    ]);

    Livewire::actingAs($kasir, 'web')
        ->test(LihatSaldo::class)
        ->assertSee('Rp 400.000')
        ->assertSee('Rp 150.000')
        ->assertSee('Rp 250.000')
        ->call('bukaModalReset')
        ->assertSet('modalResetOpen', true)
        ->call('resetManual')
        ->assertSet('modalResetOpen', false)
        ->assertDispatched('toast');

    // Periksa bahwa penutupan laci telah tercatat di database
    $penutupan = PenutupanLaci::latest('id')->first();
    expect($penutupan)->not->toBeNull()
        ->and($penutupan->total_cash)->toBe(150000)
        ->and($penutupan->total_transfer)->toBe(250000)
        ->and($penutupan->total)->toBe(400000)
        ->and($penutupan->direset_oleh)->toBe($kasir->id)
        ->and($penutupan->tipe_reset)->toBe('manual');

    // Periksa bahwa kas_laci aktif kini tertaut ke penutupan_id
    $kasAktifCount = KasLaci::aktif()->count();
    expect($kasAktifCount)->toBe(0);

    // Di komponen, saldo aktif kembali 0
    Livewire::actingAs($kasir, 'web')
        ->test(LihatSaldo::class)
        ->assertSee('Rp 0')
        ->assertSee($kasir->nama); // Nama kasir muncul di tabel riwayat 7 hari
});

test('command laci:reset menjalankan reset otomatis dengan direset_oleh NULL (Sistem)', function () {
    // Masukkan transaksi kas_laci aktif
    KasLaci::create([
        'tanggal' => now()->toDateString(),
        'waktu' => now(),
        'keterangan' => 'Penjualan Otomatis Test',
        'metode' => 'cash',
        'jumlah' => 300000,
        'sumber_tipe' => 'penjualan',
        'sumber_id' => 99,
    ]);

    $exitCode = Artisan::call('laci:reset');
    expect($exitCode)->toBe(0);

    $penutupan = PenutupanLaci::latest('id')->first();
    expect($penutupan)->not->toBeNull()
        ->and($penutupan->total)->toBe(300000)
        ->and($penutupan->direset_oleh)->toBeNull()
        ->and($penutupan->tipe_reset)->toBe('otomatis');

    // Periksa view v_riwayat_saldo_laci menampilkan 'Sistem'
    $row = DB::table('v_riwayat_saldo_laci')->where('penutupan_id', $penutupan->id)->first();
    expect($row->direset_oleh)->toBe('Sistem');
});

test('grafik mingguan merespons 3 rentang: minggu ini, minggu lalu, dan pilih tanggal', function () {
    $kasir = User::factory()->kasir()->create();
    $pelanggan = Pelanggan::create([
        'nama' => 'Pelanggan Uji',
        'no_hp' => '081234567890',
    ]);

    // Buat data penjualan langsung pada hari Rabu minggu ini
    $rabu = now()->startOfWeek(Carbon::MONDAY)->addDays(2);
    Penjualan::create([
        'kode_penjualan' => 'PJ-GR-001',
        'pelanggan_id' => $pelanggan->id,
        'user_id' => $kasir->id,
        'tipe' => 'langsung',
        'metode_bayar' => 'cash',
        'total' => 500000,
        'waktu_transaksi' => $rabu,
    ]);

    // Mode default: minggu ini
    $test = Livewire::actingAs($kasir, 'web')
        ->test(GrafikMingguan::class)
        ->assertSet('mode', 'minggu_ini')
        ->assertSee('Rp 500.000')
        ->assertSee('Rabu');

    // Ganti mode: minggu lalu (harus 0 karena transaksi di minggu ini)
    $test->set('mode', 'minggu_lalu')
        ->assertSee('Rp 0');

    // Ganti mode: pilih tanggal (pada tanggal rabu)
    $test->set('mode', 'pilih_tanggal')
        ->set('customTanggal', $rabu->toDateString())
        ->assertSee('Rp 500.000')
        ->assertSee('Rabu');
});

test('riwayat saldo laci pagination dan modal detail menampilkan rincian transaksi', function () {
    $kasir = User::factory()->kasir()->create();

    // Buat data penutupan laci beserta rincian kas_laci
    $penutupan = PenutupanLaci::create([
        'tanggal' => now()->toDateString(),
        'total_cash' => 200000,
        'total_transfer' => 100000,
        'total' => 300000,
        'waktu_reset' => now(),
        'direset_oleh' => $kasir->id,
        'tipe_reset' => 'manual',
    ]);

    KasLaci::create([
        'tanggal' => now()->toDateString(),
        'waktu' => now(),
        'keterangan' => 'Penjualan TRX-DETAIL-01',
        'metode' => 'cash',
        'jumlah' => 200000,
        'sumber_tipe' => 'penjualan',
        'sumber_id' => 1,
        'penutupan_id' => $penutupan->id,
    ]);

    KasLaci::create([
        'tanggal' => now()->toDateString(),
        'waktu' => now(),
        'keterangan' => 'Penjualan TRX-DETAIL-02',
        'metode' => 'transfer',
        'jumlah' => 100000,
        'sumber_tipe' => 'penjualan',
        'sumber_id' => 2,
        'penutupan_id' => $penutupan->id,
    ]);

    Livewire::actingAs($kasir, 'web')
        ->test(RiwayatSaldoLaci::class)
        ->assertSee('Rp 300.000')
        ->assertSee('Rp 200.000')
        ->assertSee('Rp 100.000')
        ->call('bukaModalDetail', $penutupan->id)
        ->assertSet('modalDetailOpen', true)
        ->assertSee('Penjualan TRX-DETAIL-01')
        ->assertSee('Penjualan TRX-DETAIL-02')
        ->call('tutupModalDetail')
        ->assertSet('modalDetailOpen', false);
});

test('ekspor excel dan pdf menghasilkan response download yang valid', function () {
    $kasir = User::factory()->kasir()->create();

    PenutupanLaci::create([
        'tanggal' => now()->toDateString(),
        'total_cash' => 100000,
        'total_transfer' => 50000,
        'total' => 150000,
        'waktu_reset' => now(),
        'direset_oleh' => $kasir->id,
        'tipe_reset' => 'manual',
    ]);

    // Ekspor Excel
    Livewire::actingAs($kasir, 'web')
        ->test(RiwayatSaldoLaci::class)
        ->call('exportExcel')
        ->assertFileDownloaded();

    // Ekspor PDF
    Livewire::actingAs($kasir, 'web')
        ->test(RiwayatSaldoLaci::class)
        ->call('exportPdf')
        ->assertFileDownloaded();
});

test('LaciService aman dan menghasilkan nilai 0 atau array kosong saat database kosong', function () {
    /** @var LaciService $service */
    $service = app(LaciService::class);

    $saldo = $service->getSaldoAktif();
    expect($saldo['saldo_cash'])->toBe(0)
        ->and($saldo['saldo_transfer'])->toBe(0)
        ->and($saldo['saldo_total'])->toBe(0)
        ->and($saldo['jumlah_transaksi'])->toBe(0);

    $transaksi = $service->getTransaksiTerbaru();
    expect($transaksi)->toBeEmpty();

    $riwayat7 = $service->getRiwayat7Hari();
    expect($riwayat7)->toBeEmpty();

    $grafik = $service->getGrafikMingguan();
    expect($grafik['total_penjualan'])->toBe(0)
        ->and($grafik['hari_tertinggi'])->toBeNull()
        ->and($grafik['dibanding_minggu_lalu']['persen'])->toBe(0.0)
        ->and(count($grafik['hari']))->toBe(7);

    $riwayatPaginator = $service->getRiwayatSaldoLaci();
    expect($riwayatPaginator->total())->toBe(0);

    $rincian = $service->getRincianPenutupan(99999);
    expect($rincian)->toBeNull();
});
