<?php

use App\Http\Controllers\Auth\LogoutController;
use App\Http\Middleware\NoStoreCache;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\LupaPassword;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\VerifikasiOtp;
use App\Livewire\Shared\Profil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

$halamanSementara = static fn (string $roleKey, string $title, ?string $subtitle = null) => static fn () => view('pages.belum-tersedia', [
    'roleKey' => $roleKey,
    'title' => $title,
    'subtitle' => $subtitle,
]);

// Halaman utama: arahkan ke beranda role jika sudah login, atau ke halaman login jika tamu
Route::get('/', function () {
    return Auth::check()
        ? redirect(Auth::user()->urlBeranda())
        : redirect()->route('login');
});

// Halaman Autentikasi (tanpa cache browser agar tombol Back selalu memakai token CSRF terbaru)
Route::middleware(NoStoreCache::class)->group(function () {
    Route::get('/login', Login::class)->name('login');
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
    ->group(function () use ($halamanSementara) {
        Route::get('/', $halamanSementara('admin', 'Ringkasan Sistem', 'Gambaran data dan pengguna di seluruh sistem PASKA JAYA.'))
            ->name('dashboard');
        Route::get('/pengguna', $halamanSementara('admin', 'Manajemen Pengguna'))
            ->name('pengguna');
        Route::get('/tim', $halamanSementara('admin', 'Tim'))
            ->name('tim');
        Route::get('/profil', Profil::class)->name('profil');
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
        Route::get('/master-item', $halamanSementara('owner', 'Master Item'))
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
        Route::get('/', $halamanSementara('kasir', 'Beranda Kasir', 'Kelola transaksi barang keluar, saldo laci, dan bon harian.'))
            ->name('beranda');
        Route::get('/barang-keluar', $halamanSementara('kasir', 'Barang Keluar'))
            ->name('barang-keluar');
        Route::get('/saldo', $halamanSementara('kasir', 'Lihat Saldo'))
            ->name('saldo');
        Route::get('/grafik-mingguan', $halamanSementara('kasir', 'Grafik Mingguan'))
            ->name('grafik');
        Route::get('/riwayat-saldo', $halamanSementara('kasir', 'Riwayat Saldo Laci'))
            ->name('riwayat-laci');
        Route::get('/catat-bon', $halamanSementara('kasir', 'Catat Bon'))
            ->name('catat-bon');
        Route::get('/daftar-bon', $halamanSementara('kasir', 'Daftar Bon'))
            ->name('daftar-bon');
        Route::get('/bahan-harian', $halamanSementara('kasir', 'Bahan Harian'))
            ->name('bahan-harian');
        Route::get('/kehadiran', $halamanSementara('kasir', 'Kehadiran'))
            ->name('kehadiran');
        Route::get('/master-item', $halamanSementara('kasir', 'Master Item'))
            ->name('master-item');
        Route::get('/profil', Profil::class)->name('profil');
    });

// ── Karyawan (/karyawan) ──
Route::middleware(['auth', 'aktif', 'role:karyawan'])
    ->prefix('karyawan')
    ->name('karyawan.')
    ->group(function () use ($halamanSementara) {
        Route::get('/pengantaran', $halamanSementara('karyawan', 'Pengantaran', 'Daftar tugas pengiriman barang pelanggan.'))
            ->name('pengantaran');
        Route::get('/pengantaran/{pengiriman}', $halamanSementara('karyawan', 'Detail Pengantaran'))
            ->name('pengantaran.detail');
        Route::get('/profil', Profil::class)->name('profil');
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
