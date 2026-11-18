<?php

use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\BuktiPengirimanController;
use App\Http\Middleware\NoStoreCache;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\Pengguna\Index as AdminPenggunaIndex;
use App\Livewire\Admin\Tim\Index as AdminTimIndex;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\LupaPassword;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\VerifikasiOtp;
use App\Livewire\Karyawan\Pengantaran\Detail as PengantaranDetail;
use App\Livewire\Karyawan\Pengantaran\Index as PengantaranIndex;

use App\Livewire\Kasir\BahanHarian as KasirBahanHarian;

use App\Livewire\Kasir\BarangKeluar;
use App\Livewire\Kasir\Beranda as KasirBeranda;

use App\Livewire\Kasir\CatatBon;
use App\Livewire\Kasir\DaftarBon as KasirDaftarBon;
use App\Livewire\Kasir\GrafikMingguan as KasirGrafikMingguan;
use App\Livewire\Kasir\LihatSaldo as KasirLihatSaldo;
use App\Livewire\Kasir\RiwayatSaldoLaci as KasirRiwayatSaldoLaci;
use App\Livewire\Shared\MasterItem\Index as MasterItemIndex;
use App\Livewire\Shared\Profil;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

$halamanSementara = static fn (string $roleKey, string $title, ?string $subtitle = null) => static fn () => view('pages.belum-tersedia', [
    'roleKey' => $roleKey,
    'title' => $title,
    'subtitle' => $subtitle,
]);

// Halaman utama: arahkan ke beranda role jika sudah login, atau ke halaman login jika tamu
Route::get('/', function () {
    $user = Auth::user();

    return $user instanceof User
        ? redirect($user->urlBeranda())
        : redirect()->route('login');
})->name('home');

// Halaman Autentikasi (tanpa cache browser agar tombol Back selalu memakai token CSRF terbaru)
Route::middleware([NoStoreCache::class])->group(function () {
    Route::get('/login', Login::class)->name('login');
});

Route::middleware(['guest', NoStoreCache::class])->group(function () {
    Route::get('/register', Register::class)->name('register');
    Route::get('/verifikasi-otp', VerifikasiOtp::class)->name('otp.verify');
    Route::get('/lupa-password', LupaPassword::class)->name('password.request');
    Route::get('/reset-password/{token}', LupaPassword::class)->name('password.reset');
});

// Route Logout (POST dengan CSRF)
Route::post('/logout', LogoutController::class)
    ->middleware('auth')
    ->name('logout');

// ── Admin (/admin) ──
Route::middleware(['auth', 'aktif', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', AdminDashboard::class)->name('dashboard');
        Route::get('/pengguna', AdminPenggunaIndex::class)->name('pengguna');
        Route::get('/tim', AdminTimIndex::class)->name('tim');
        Route::redirect('/profil', '/admin/tim')->name('profil');
    });

// ── Owner (/owner) ──
Route::middleware(['auth', 'aktif', 'role:owner'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () use ($halamanSementara) {
        Route::get('/', $halamanSementara('owner', 'Ringkasan Toko', 'Pantau omzet, stok kritis, dan piutang PASKA JAYA.'))
            ->name('dashboard');
        Route::get('/laporan-barang-keluar', $halamanSementara('owner', 'Laporan Barang Keluar'))
            ->name('laporan');
        Route::get('/daftar-bon', $halamanSementara('owner', 'Daftar Bon'))
            ->name('bon');
        Route::get('/master-item', MasterItemIndex::class)
            ->name('master-item');
        Route::get('/kehadiran', $halamanSementara('owner', 'Kehadiran'))
            ->name('kehadiran');
        Route::get('/profil', Profil::class)->name('profil');
    });

// ── Kasir (/kasir) ──
Route::middleware(['auth', 'aktif', 'role:kasir'])
    ->prefix('kasir')
    ->name('kasir.')
    ->group(function () use ($halamanSementara) {
        Route::get('/', KasirBeranda::class)
            ->name('beranda');
        Route::get('/barang-keluar', BarangKeluar::class)
            ->name('barang-keluar');
        Route::get('/saldo', KasirLihatSaldo::class)
            ->name('saldo');
        Route::get('/grafik-mingguan', KasirGrafikMingguan::class)
            ->name('grafik');
        Route::get('/riwayat-saldo', KasirRiwayatSaldoLaci::class)
            ->name('riwayat-laci');
        Route::get('/catat-bon', CatatBon::class)
            ->name('catat-bon');
        Route::get('/daftar-bon', KasirDaftarBon::class)
            ->name('daftar-bon');
        Route::get('/bahan-harian', KasirBahanHarian::class)
            ->name('bahan-harian');
        Route::get('/kehadiran', $halamanSementara('kasir', 'Kehadiran'))
            ->name('kehadiran');
        Route::get('/master-item', MasterItemIndex::class)
            ->name('master-item');
        Route::get('/profil', Profil::class)->name('profil');
    });

// ── Karyawan (/karyawan) ──
Route::middleware(['auth', 'aktif', 'role:karyawan'])
    ->prefix('karyawan')
    ->name('karyawan.')
    ->group(function () {
        Route::get('/pengantaran', PengantaranIndex::class)
            ->name('pengantaran.index');
        Route::get('/pengantaran-menu', fn () => redirect()->route('karyawan.pengantaran.index'))
            ->name('pengantaran');
        Route::get('/pengantaran/{id}', PengantaranDetail::class)
            ->name('pengantaran.detail');
        Route::get('/profil', Profil::class)
            ->name('profil');
    });

// Berkas privat (ADR-011): owner, kasir, atau karyawan pemilik (dicek Policy di controller)
Route::middleware(['auth', 'aktif'])->group(function () {
    Route::get('/berkas/bukti-pengiriman/{id}', BuktiPengirimanController::class)
        ->name('berkas.bukti-pengiriman');
});

// Halaman Galeri Komponen UI/UX & Layout (Khusus Lingkungan Lokal & Pengujian)
if (app()->environment(['local', 'testing'])) {
    Route::prefix('dev')->group(function () {
        Route::get('/ui', function () {
            return view('dev.ui-gallery');
        })->name('dev.ui');

        Route::get('/auth', function () {
            return view('dev.auth-preview');
        })->name('dev.auth');

        Route::get('/register', function () {
            return view('dev.register-preview');
        })->name('dev.register');

        Route::get('/owner-bon', function () {
            return view('dev.owner-bon-preview');
        })->name('dev.owner-bon');
    });
}
