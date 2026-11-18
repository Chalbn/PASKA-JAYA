<?php

use App\Enums\RolePengguna;
use App\Enums\StatusPengguna;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\Pengguna\Index as AdminPenggunaIndex;
use App\Livewire\Admin\Tim\Index as AdminTimIndex;
use App\Livewire\Auth\Login;
use App\Models\ActivityLog;
use App\Models\AnggotaTim;
use App\Models\Role;
use App\Models\User;
use App\Services\PenggunaService;
use App\Services\StatusSistemService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('otorisasi admin panel: hanya admin yang diizinkan dan tamu diarahkan ke login', function () {
    $this->get('/admin')->assertRedirect('/login');
    $this->get('/admin/pengguna')->assertRedirect('/login');
    $this->get('/admin/tim')->assertRedirect('/login');

    foreach ([RolePengguna::Owner, RolePengguna::Kasir, RolePengguna::Karyawan] as $role) {
        $user = User::factory()->{$role->value}()->create();

        $this->actingAs($user)->get('/admin')->assertForbidden();
        $this->actingAs($user)->get('/admin/pengguna')->assertForbidden();
        $this->actingAs($user)->get('/admin/tim')->assertForbidden();

        Livewire::actingAs($user, 'web')->test(AdminDashboard::class)->assertForbidden();
        Livewire::actingAs($user, 'web')->test(AdminPenggunaIndex::class)->assertForbidden();
        Livewire::actingAs($user, 'web')->test(AdminTimIndex::class)->assertForbidden();
    }

    $admin = User::factory()->admin()->create();
    $this->actingAs($admin)->get('/admin')
        ->assertOk()
        ->assertSee(route('admin.tim'))
        ->assertDontSee('/admin/profil')
        ->assertDontSee('Buka Profil Saya');
    $this->actingAs($admin)->get('/admin/pengguna')->assertOk();
    $this->actingAs($admin)->get('/admin/tim')->assertOk();
    $this->actingAs($admin)->get('/admin/profil')->assertRedirect(route('admin.tim'));
});

test('ringkasan sistem menampilkan angka nol saat database kosong dan tautan kelola pengguna', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin, 'web')
        ->test(AdminDashboard::class)
        ->assertSee('Ringkasan Sistem')
        ->assertSee('Kelola Pengguna')
        ->assertSee(route('admin.pengguna'))
        ->assertSee('Total pengguna')
        ->assertSee('1 aktif')
        ->assertSee('Master item')
        ->assertSee('0 stok menipis')
        ->assertSee('Transaksi keluar')
        ->assertSee('0 data bon')
        ->assertSee('Pengantaran')
        ->assertSee('0 berjalan')
        ->assertSee('Pengguna per Role')
        ->assertSee('1 akun')
        ->assertSee('0 akun')
        ->assertSee('Status Sistem')
        ->assertSee('Normal')
        ->assertSee('Terhubung')
        ->assertSee('Belum pernah')
        ->assertSee('0 data');
});

test('ringkasan sistem menghitung data aktual dari database via StatusSistemService', function () {
    $admin = User::factory()->admin()->create();
    $kasir = User::factory()->kasir()->create();
    $karyawan = User::factory()->karyawan()->create();
    User::factory()->owner()->nonaktif()->create();

    $kategoriId = DB::table('kategori_barang')->insertGetId([
        'nama' => 'Semen',
        'slug' => 'semen',
    ]);

    DB::table('barang')->insert([
        [
            'kode_barang' => 'BRG-001',
            'kategori_id' => $kategoriId,
            'nama_barang' => 'Semen A',
            'satuan' => 'Sak',
            'harga_beli' => 50000,
            'harga_jual' => 65000,
            'stok' => 5,
            'stok_minimum' => 10,
        ],
        [
            'kode_barang' => 'BRG-002',
            'kategori_id' => $kategoriId,
            'nama_barang' => 'Semen B',
            'satuan' => 'Sak',
            'harga_beli' => 52000,
            'harga_jual' => 68000,
            'stok' => 25,
            'stok_minimum' => 10,
        ],
    ]);

    $pelangganId = DB::table('pelanggan')->insertGetId([
        'nama' => 'Proyek Mandiri',
        'no_hp' => '081234567899',
        'alamat' => 'Jl. Merdeka',
    ]);

    DB::table('penjualan')->insert([
        'kode_transaksi' => 'PJ-2026-0001',
        'tipe' => 'langsung',
        'nama_pelanggan' => 'Umum',
        'metode_bayar' => 'cash',
        'total' => 130000,
        'user_id' => $kasir->id,
        'waktu_transaksi' => now('Asia/Jakarta'),
    ]);

    $penjualanBonId = DB::table('penjualan')->insertGetId([
        'kode_transaksi' => 'PJ-2026-0002',
        'tipe' => 'bon',
        'pelanggan_id' => $pelangganId,
        'nama_pelanggan' => 'Proyek Mandiri',
        'metode_bayar' => null,
        'total' => 200000,
        'user_id' => $kasir->id,
        'waktu_transaksi' => now('Asia/Jakarta'),
    ]);

    $bonId = DB::table('bon_piutang')->insertGetId([
        'kode_bon' => 'BON-2026-0001',
        'penjualan_id' => $penjualanBonId,
        'pelanggan_id' => $pelangganId,
        'alamat_pengantaran' => 'Jl. Merdeka',
        'tanggal_bon' => today('Asia/Jakarta')->toDateString(),
        'jatuh_tempo' => today('Asia/Jakarta')->addDays(7)->toDateString(),
        'total_tagihan' => 200000,
        'dibuat_oleh' => $kasir->id,
    ]);

    DB::table('pengiriman_barang')->insert([
        [
            'bon_id' => $bonId,
            'karyawan_id' => $karyawan->id,
            'status' => 'sedang_disiapkan',
            'dibuat_oleh' => $kasir->id,
        ],
        [
            'bon_id' => $bonId,
            'karyawan_id' => $karyawan->id,
            'status' => 'selesai',
            'dibuat_oleh' => $kasir->id,
        ],
    ]);

    DB::table('absensi_karyawan')->insert([
        'karyawan_id' => $karyawan->id,
        'tanggal' => today('Asia/Jakarta')->toDateString(),
        'shift' => 'pagi',
        'dibuat_oleh' => $kasir->id,
    ]);

    DB::table('penggunaan_bahan')->insert([
        'tanggal' => today('Asia/Jakarta')->toDateString(),
        'nama_bahan' => 'Solar Truk',
        'jumlah' => 20,
        'satuan' => 'Liter',
        'harga' => 136000,
        'user_id' => $kasir->id,
    ]);

    $waktuBackup = now('Asia/Jakarta')->setTime(2, 0);
    DB::table('system_configs')->updateOrInsert(
        ['kunci' => 'backup_terakhir'],
        ['nilai' => $waktuBackup->format('Y-m-d H:i:s'), 'updated_at' => $waktuBackup]
    );

    Livewire::actingAs($admin, 'web')
        ->test(AdminDashboard::class)
        ->assertSee('3 aktif')
        ->assertSee('1 stok menipis')
        ->assertSee('1 data bon')
        ->assertSee('1 berjalan')
        ->assertSee('Hari ini, 02.00')
        ->assertSee('1 data');
});

test('manajemen pengguna: tampil (Anda), pencarian nama atau email, tambah, dan edit pengguna', function () {
    $admin = User::factory()->admin()->create([
        'nama' => 'Admin Utama',
        'email' => 'admin.utama@paskajaya.test',
    ]);
    $kasir = User::factory()->kasir()->create([
        'nama' => 'Siti Kasir',
        'email' => 'siti.kasir@paskajaya.test',
    ]);

    Livewire::actingAs($admin, 'web')
        ->test(AdminPenggunaIndex::class)
        ->assertSee('Admin Utama')
        ->assertSee('(Anda)')
        ->assertSee('Siti Kasir')
        ->set('search', 'siti')
        ->assertSee('Siti Kasir')
        ->assertDontSee('Admin Utama')
        ->set('search', 'admin.utama@paskajaya.test')
        ->assertSee('Admin Utama')
        ->assertDontSee('Siti Kasir')
        ->set('search', '')
        ->call('bukaModalTambah')
        ->set('nama', 'Budi Lapangan')
        ->set('email', 'budi.baru@paskajaya.test')
        ->set('no_hp', '+62 812-3456-7890')
        ->set('role', RolePengguna::Karyawan->value)
        ->set('password', 'passwordku123')
        ->call('simpan')
        ->assertHasNoErrors();

    $penggunaBaru = User::query()->where('email', 'budi.baru@paskajaya.test')->first();
    expect($penggunaBaru)->not->toBeNull()
        ->and($penggunaBaru->no_hp)->toBe('081234567890')
        ->and($penggunaBaru->hasRole(RolePengguna::Karyawan))->toBeTrue();

    // Pastikan password tidak tercatat di activity_logs
    $logTambah = ActivityLog::query()
        ->where('modul', 'pengguna')
        ->where('aksi', 'tambah')
        ->where('target_id', $penggunaBaru->id)
        ->first();
    expect($logTambah)->not->toBeNull()
        ->and(json_encode($logTambah->data_baru))->not->toContain('passwordku123');

    // Pengguna baru dapat langsung login
    auth()->logout();
    Livewire::test(Login::class)
        ->set('login', 'budi.baru@paskajaya.test')
        ->set('password', 'passwordku123')
        ->call('masuk')
        ->assertRedirect('/karyawan/pengantaran');

    // Edit pengguna lain oleh admin
    Livewire::actingAs($admin, 'web')
        ->test(AdminPenggunaIndex::class)
        ->call('bukaModalEdit', $kasir->id)
        ->set('nama', 'Siti Rahma')
        ->set('no_hp', '081311112222')
        ->call('simpan')
        ->assertHasNoErrors();

    expect($kasir->fresh()->nama)->toBe('Siti Rahma')
        ->and($kasir->fresh()->no_hp)->toBe('081311112222');
});

test('ubah role langsung pada tabel langsung berlaku pada permintaan berikutnya', function () {
    $admin = User::factory()->admin()->create();
    $target = User::factory()->owner()->create();

    $this->actingAs($target)->get('/owner')->assertOk();
    $this->actingAs($target)->get('/kasir')->assertForbidden();

    Livewire::actingAs($admin, 'web')
        ->test(AdminPenggunaIndex::class)
        ->call('ubahRole', $target->id, RolePengguna::Kasir->value)
        ->assertDispatched('toast');

    $targetSegar = $target->fresh()->load('role');
    expect($targetSegar->hasRole(RolePengguna::Kasir))->toBeTrue();

    $this->actingAs($targetSegar)->get('/kasir')->assertOk();
    $this->actingAs($targetSegar)->get('/owner')->assertForbidden();
});

test('menonaktifkan pengguna memblokir login serta menghentikan sesi aktif dan mengaktifkannya kembali membolehkan login', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->kasir()->create([
        'email' => 'kasir.aktif@paskajaya.test',
        'password' => 'rahasia123',
    ]);

    Livewire::actingAs($admin, 'web')
        ->test(AdminPenggunaIndex::class)
        ->call('toggleStatus', $user->id);

    expect($user->fresh()->status)->toBe(StatusPengguna::Nonaktif);

    // Sesi aktif akun yang baru dinonaktifkan ditendang middleware aktif
    $this->actingAs($user->fresh())
        ->get('/kasir')
        ->assertRedirect(route('login'));
    $this->assertGuest();

    // Login ulang saat nonaktif ditolak
    Livewire::test(Login::class)
        ->set('login', 'kasir.aktif@paskajaya.test')
        ->set('password', 'rahasia123')
        ->call('masuk')
        ->assertHasErrors('login');

    // Aktifkan kembali
    Livewire::actingAs($admin, 'web')
        ->test(AdminPenggunaIndex::class)
        ->call('toggleStatus', $user->id);

    expect($user->fresh()->status)->toBe(StatusPengguna::Aktif);

    Livewire::test(Login::class)
        ->set('login', 'kasir.aktif@paskajaya.test')
        ->set('password', 'rahasia123')
        ->call('masuk')
        ->assertHasNoErrors()
        ->assertRedirect('/kasir');
});

test('proteksi akun sendiri dan soft delete pengguna bekerja dengan benar', function () {
    $admin = User::factory()->admin()->create();
    $karyawan = User::factory()->karyawan()->create();

    Livewire::actingAs($admin, 'web')
        ->test(AdminPenggunaIndex::class)
        ->call('ubahRole', $admin->id, RolePengguna::Kasir->value)
        ->assertForbidden();

    Livewire::actingAs($admin, 'web')
        ->test(AdminPenggunaIndex::class)
        ->call('toggleStatus', $admin->id)
        ->assertForbidden();

    Livewire::actingAs($admin, 'web')
        ->test(AdminPenggunaIndex::class)
        ->call('konfirmasiHapus', $admin->id)
        ->assertForbidden();

    // Soft delete pengguna lain
    Livewire::actingAs($admin, 'web')
        ->test(AdminPenggunaIndex::class)
        ->call('konfirmasiHapus', $karyawan->id)
        ->call('hapusPengguna')
        ->assertDispatched('toast');

    expect(User::query()->find($karyawan->id))->toBeNull()
        ->and(User::withTrashed()->find($karyawan->id)?->deleted_at)->not->toBeNull();
});

test('proteksi admin aktif terakhir dijaga di PenggunaService dan trigger database T7', function () {
    $adminSatu = User::factory()->admin()->create();
    $adminDua = User::factory()->admin()->create();
    $service = app(PenggunaService::class);

    expect($service->adalahAdminAktifTerakhir($adminSatu))->toBeFalse();

    // Nonaktifkan admin kedua -> adminSatu kini satu-satunya admin aktif
    $service->ubahStatus($adminSatu, $adminDua, StatusPengguna::Nonaktif);
    expect($service->adalahAdminAktifTerakhir($adminSatu->fresh()))->toBeTrue();

    // Operasi pada admin aktif terakhir via service ditolak
    expect(fn () => $service->ubahRole($adminDua, $adminSatu->fresh(), RolePengguna::Owner))
        ->toThrow(DomainException::class);
    expect(fn () => $service->ubahStatus($adminDua, $adminSatu->fresh(), StatusPengguna::Nonaktif))
        ->toThrow(DomainException::class);
    expect(fn () => $service->hapus($adminDua, $adminSatu->fresh()))
        ->toThrow(DomainException::class);

    // Pengaman tingkat database (trigger T7) menolak penurunan role/status langsung di SQL
    $roleOwnerId = Role::query()->where('nama', RolePengguna::Owner->value)->value('id');
    expect(fn () => DB::table('users')->where('id', $adminSatu->id)->update(['role_id' => $roleOwnerId]))
        ->toThrow(QueryException::class);
    expect(fn () => DB::table('users')->where('id', $adminSatu->id)->update(['status' => 'nonaktif']))
        ->toThrow(QueryException::class);
});

test('halaman tim: langsung menampilkan 5 kartu anggota dan setiap kartu dapat diedit dengan inisial trigger serta foto opsional', function () {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin, 'web')
        ->test(AdminTimIndex::class)
        ->assertDontSee('+ Tambah Anggota')
        ->assertSee('Rudi Hartono')
        ->assertSee('RH')
        ->assertSee('Dewi Anggraini')
        ->assertSee('DA')
        ->assertSee('Bagas Pratama')
        ->assertSee('BP')
        ->assertSee('Sri Wahyuni')
        ->assertSee('SW')
        ->assertSee('Yusuf Maulana')
        ->assertSee('YM');

    $foto = UploadedFile::fake()->image('budi.jpg', 300, 300);

    // Edit slot ke-2 dari tampilan default -> tersimpan ke anggota_tim dengan trigger inisial T14
    Livewire::actingAs($admin, 'web')
        ->test(AdminTimIndex::class)
        ->call('bukaModalEdit', 2)
        ->set('nama', 'Andi Wijaya')
        ->set('jabatan', 'Kepala Gudang')
        ->set('foto', $foto)
        ->call('simpan')
        ->assertHasNoErrors()
        ->assertDispatched('toast');

    $anggota = AnggotaTim::query()->where('urutan', 2)->first();
    expect($anggota)->not->toBeNull()
        ->and($anggota->nama)->toBe('Andi Wijaya')
        ->and($anggota->inisial)->toBe('AW')
        ->and($anggota->jabatan)->toBe('Kepala Gudang')
        ->and($anggota->foto)->not->toBeNull();

    Storage::disk('public')->assertExists($anggota->foto);
    $pathFoto = $anggota->foto;

    // Edit ulang slot ke-2 dan hapus foto -> kembali memakai inisial dan foto lama terhapus
    Livewire::actingAs($admin, 'web')
        ->test(AdminTimIndex::class)
        ->assertSee('Andi Wijaya')
        ->call('bukaModalEdit', 2)
        ->set('nama', 'Budi Santoso')
        ->set('hapusFotoSaatIni', true)
        ->call('simpan')
        ->assertHasNoErrors();

    $anggotaSegar = $anggota->fresh();
    expect($anggotaSegar->nama)->toBe('Budi Santoso')
        ->and($anggotaSegar->inisial)->toBe('BS')
        ->and($anggotaSegar->foto)->toBeNull();

    Storage::disk('public')->assertMissing($pathFoto);
});

test('command backup:run menjalankan mysqldump dengan pj_backup, memperbarui backup_terakhir, dan terjadwal pukul 02:00 WIB', function () {
    Process::fake();
    $jalur = storage_path('app/private/backups/test_backup.sql');

    $this->artisan('backup:run', ['--path' => $jalur])
        ->assertSuccessful();

    Process::assertRan(function ($process) use ($jalur) {
        return in_array('--user=pj_backup', $process->command, true)
            && in_array('--result-file='.$jalur, $process->command, true)
            && in_array('--no-tablespaces', $process->command, true);
    });

    $nilai = DB::table('system_configs')->where('kunci', 'backup_terakhir')->value('nilai');
    expect($nilai)->not->toBeNull()->not->toBe('')
        ->and(app(StatusSistemService::class)->pencadanganTerakhir())->toStartWith('Hari ini,');

    // Jika mysqldump gagal, command mengembalikan failure dan nilai sebelumnya tidak terhapus
    Process::fake([
        '*' => Process::result(output: '', errorOutput: 'Access denied', exitCode: 1),
    ]);

    $this->artisan('backup:run', ['--path' => $jalur])
        ->assertFailed();

    expect(DB::table('system_configs')->where('kunci', 'backup_terakhir')->value('nilai'))->toBe($nilai);

    // Pastikan jadwal cron terdaftar pukul 02:00 Asia/Jakarta
    $events = collect(app(Schedule::class)->events())
        ->filter(fn ($event) => str_contains((string) $event->command, 'backup:run'));

    expect($events)->toHaveCount(1);
    $event = $events->first();
    expect($event->expression)->toBe('0 2 * * *')
        ->and($event->timezone)->toBe('Asia/Jakarta');
});

test('halaman tim: unggah, tampil transparan, ganti, dan hapus foto bersama tim sebagai latar', function () {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    $fotoPertama = UploadedFile::fake()->image('tim-bersama-1.jpg', 1200, 720);

    Livewire::actingAs($admin, 'web')
        ->test(AdminTimIndex::class)
        ->call('bukaModalFotoBersama')
        ->set('fotoBersama', $fotoPertama)
        ->call('simpanFotoBersama')
        ->assertHasNoErrors()
        ->assertDispatched('toast');

    $pathPertama = (string) DB::table('system_configs')->where('kunci', 'foto_tim_bersama')->value('nilai');
    expect($pathPertama)->not->toBe('');
    Storage::disk('public')->assertExists($pathPertama);

    Livewire::actingAs($admin, 'web')
        ->test(AdminTimIndex::class)
        ->assertSeeHtml('opacity-35')
        ->assertSeeHtml(Storage::disk('public')->url($pathPertama))
        ->assertSee('Ganti Foto Bersama');

    // Ganti dengan foto baru -> foto lama otomatis terhapus
    $fotoKedua = UploadedFile::fake()->image('tim-bersama-2.jpg', 1200, 720);

    Livewire::actingAs($admin, 'web')
        ->test(AdminTimIndex::class)
        ->call('bukaModalFotoBersama')
        ->set('fotoBersama', $fotoKedua)
        ->call('simpanFotoBersama')
        ->assertHasNoErrors();

    $pathKedua = (string) DB::table('system_configs')->where('kunci', 'foto_tim_bersama')->value('nilai');
    expect($pathKedua)->not->toBe('')->not->toBe($pathPertama);
    Storage::disk('public')->assertMissing($pathPertama);
    Storage::disk('public')->assertExists($pathKedua);

    // Hapus foto bersama
    Livewire::actingAs($admin, 'web')
        ->test(AdminTimIndex::class)
        ->call('hapusFotoBersama')
        ->assertDispatched('toast');

    Storage::disk('public')->assertMissing($pathKedua);
    expect((string) DB::table('system_configs')->where('kunci', 'foto_tim_bersama')->value('nilai'))->toBe('');
});
