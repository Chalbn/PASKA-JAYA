<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PenggunaController as AdminPenggunaController;
use App\Http\Controllers\Admin\TimController as AdminTimController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\LupaPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerifikasiOtpController;
use App\Http\Controllers\BuktiPengirimanController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\Karyawan\PengantaranController;
use App\Http\Controllers\Kasir\BahanHarianController;
use App\Http\Controllers\Kasir\BarangKeluarController;
use App\Http\Controllers\Kasir\BerandaController as KasirBerandaController;
use App\Http\Controllers\Kasir\BonController;
use App\Http\Controllers\Kasir\KehadiranController;
use App\Http\Controllers\Kasir\LaciController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\LaporanController as OwnerLaporanController;
use App\Http\Controllers\Owner\NotificationController as OwnerNotificationController;
use App\Http\Controllers\Shared\MasterItemController;
use App\Http\Controllers\Shared\ProfilController;
use App\Http\Middleware\NoStoreCache;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Halaman utama
Route::get('/', function () {
    $user = Auth::user();

    return $user instanceof User
        ? redirect($user->urlBeranda())
        : redirect()->route('login');
})->name('home');

// Autentikasi
Route::middleware([NoStoreCache::class])->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.attempt');
    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
});

Route::middleware(['guest', NoStoreCache::class])->group(function () {
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

    Route::get('/verifikasi-otp', [VerifikasiOtpController::class, 'show'])->name('otp.verify');
    Route::post('/verifikasi-otp', [VerifikasiOtpController::class, 'verify'])->name('otp.verify.submit');
    Route::post('/verifikasi-otp/kirim-ulang', [VerifikasiOtpController::class, 'resend'])->name('otp.resend');

    Route::get('/lupa-password', [LupaPasswordController::class, 'showRequestForm'])->name('password.request');
    Route::post('/lupa-password', [LupaPasswordController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [LupaPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [LupaPasswordController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', LogoutController::class)
    ->middleware('auth')
    ->name('logout');

// ── Admin (/admin) ──
Route::middleware(['auth', 'aktif', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('/konfigurasi', [AdminDashboardController::class, 'simpanKonfigurasi'])->name('konfigurasi.simpan');

        Route::get('/pengguna', [AdminPenggunaController::class, 'index'])->name('pengguna');
        Route::post('/pengguna', [AdminPenggunaController::class, 'store'])->name('pengguna.store');
        Route::put('/pengguna/{id}', [AdminPenggunaController::class, 'update'])->name('pengguna.update');
        Route::patch('/pengguna/{id}/role', [AdminPenggunaController::class, 'ubahRole'])->name('pengguna.role');
        Route::patch('/pengguna/{id}/status', [AdminPenggunaController::class, 'toggleStatus'])->name('pengguna.status');
        Route::delete('/pengguna/{id}', [AdminPenggunaController::class, 'destroy'])->name('pengguna.destroy');

        Route::get('/tim', [AdminTimController::class, 'index'])->name('tim');
        Route::post('/tim', [AdminTimController::class, 'store'])->name('tim.store');
        Route::post('/tim/foto-bersama', [AdminTimController::class, 'updateFotoBersama'])->name('tim.foto-bersama');
        Route::delete('/tim/foto-bersama', [AdminTimController::class, 'hapusFotoBersama'])->name('tim.foto-bersama.hapus');

        Route::redirect('/profil', '/admin/tim')->name('profil');
    });

// ── Owner (/owner) ──
Route::middleware(['auth', 'aktif', 'role:owner'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {
        Route::get('/', [OwnerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/laporan-barang-keluar', [OwnerLaporanController::class, 'barangKeluar'])->name('laporan');
        Route::get('/daftar-bon', [OwnerLaporanController::class, 'daftarBon'])->name('bon');
        Route::get('/daftar-bon/{id}', [OwnerLaporanController::class, 'detailBon'])->name('bon.detail');
        Route::get('/riwayat-laci', [OwnerLaporanController::class, 'riwayatLaci'])->name('riwayat-laci');
        Route::get('/kehadiran', [OwnerLaporanController::class, 'kehadiran'])->name('kehadiran');

        Route::get('/master-item', [MasterItemController::class, 'index'])->name('master-item');
        Route::post('/master-item', [MasterItemController::class, 'store'])->name('master-item.store');
        Route::post('/master-item/{id}/restock', [MasterItemController::class, 'restock'])->name('master-item.restock');
        Route::delete('/master-item/{id}', [MasterItemController::class, 'destroy'])->name('master-item.destroy');

        Route::get('/profil', [ProfilController::class, 'show'])->name('profil');
        Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');
        Route::put('/profil/password', [ProfilController::class, 'ubahPassword'])->name('profil.password');
        Route::delete('/profil', [ProfilController::class, 'hapusAkun'])->name('profil.destroy');

        Route::get('/notifications', [OwnerNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/read-all', [OwnerNotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::post('/notifications/{id}/read', [OwnerNotificationController::class, 'markAsRead'])->name('notifications.read');
    });

// ── Kasir (/kasir) ──
Route::middleware(['auth', 'aktif', 'role:kasir'])
    ->prefix('kasir')
    ->name('kasir.')
    ->group(function () {
        Route::get('/', [KasirBerandaController::class, 'index'])->name('beranda');

        Route::get('/barang-keluar', [BarangKeluarController::class, 'index'])->name('barang-keluar');
        Route::post('/barang-keluar', [BarangKeluarController::class, 'store'])->name('barang-keluar.store');

        Route::get('/saldo', [LaciController::class, 'index'])->name('saldo');
        Route::get('/grafik-mingguan', [LaciController::class, 'grafikMingguan'])->name('grafik');
        Route::get('/riwayat-saldo', [LaciController::class, 'riwayatSaldo'])->name('riwayat-laci');
        Route::get('/riwayat-saldo/{penutupanId}/detail', [LaciController::class, 'detailPenutupan'])->name('riwayat-laci.detail');
        Route::get('/riwayat-saldo/export-excel', [LaciController::class, 'exportExcel'])->name('riwayat-laci.excel');
        Route::get('/riwayat-saldo/export-pdf', [LaciController::class, 'exportPdf'])->name('riwayat-laci.pdf');
        Route::post('/saldo/reset', [LaciController::class, 'resetManual'])->name('saldo.reset');

        Route::get('/catat-bon', [BonController::class, 'create'])->name('catat-bon');
        Route::get('/daftar-bon', [BonController::class, 'index'])->name('daftar-bon');
        Route::post('/bon', [BonController::class, 'store'])->name('bon.store');
        Route::get('/bon/{id}', [BonController::class, 'show'])->name('bon.show');
        Route::post('/bon/{id}/kirim', [BonController::class, 'kirim'])->name('bon.kirim');
        Route::post('/pengiriman/{pengirimanId}/batal', [BonController::class, 'batalkanPengiriman'])->name('pengiriman.batal');
        Route::post('/pengiriman/{pengirimanId}/ganti-karyawan', [BonController::class, 'gantiKaryawan'])->name('pengiriman.ganti-karyawan');
        Route::post('/bon/{id}/bayar', [BonController::class, 'bayar'])->name('bon.bayar');

        Route::get('/bahan-harian', [BahanHarianController::class, 'index'])->name('bahan-harian');
        Route::post('/bahan-harian', [BahanHarianController::class, 'store'])->name('bahan-harian.store');
        Route::put('/bahan-harian/{id}', [BahanHarianController::class, 'update'])->name('bahan-harian.update');
        Route::delete('/bahan-harian/{id}', [BahanHarianController::class, 'destroy'])->name('bahan-harian.destroy');

        Route::get('/kehadiran', [KehadiranController::class, 'index'])->name('kehadiran');
        Route::post('/kehadiran', [KehadiranController::class, 'store'])->name('kehadiran.store');
        Route::put('/kehadiran/{id}', [KehadiranController::class, 'update'])->name('kehadiran.update');
        Route::delete('/kehadiran/{id}', [KehadiranController::class, 'destroy'])->name('kehadiran.destroy');

        Route::get('/master-item', [MasterItemController::class, 'index'])->name('master-item');
        Route::post('/master-item', [MasterItemController::class, 'store'])->name('master-item.store');
        Route::post('/master-item/{id}/restock', [MasterItemController::class, 'restock'])->name('master-item.restock');
        Route::delete('/master-item/{id}', [MasterItemController::class, 'destroy'])->name('master-item.destroy');

        Route::get('/profil', [ProfilController::class, 'show'])->name('profil');
        Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');
        Route::put('/profil/password', [ProfilController::class, 'ubahPassword'])->name('profil.password');
        Route::delete('/profil', [ProfilController::class, 'hapusAkun'])->name('profil.destroy');
    });

// ── Karyawan (/karyawan) ──
Route::middleware(['auth', 'aktif', 'role:karyawan'])
    ->prefix('karyawan')
    ->name('karyawan.')
    ->group(function () {
        Route::get('/pengantaran', [PengantaranController::class, 'index'])->name('pengantaran.index');
        Route::get('/pengantaran-menu', fn () => redirect()->route('karyawan.pengantaran.index'))->name('pengantaran');
        Route::get('/pengantaran/{id}', [PengantaranController::class, 'show'])->name('pengantaran.detail');
        Route::post('/pengantaran/{id}/status', [PengantaranController::class, 'ubahStatus'])->name('pengantaran.status');

        Route::get('/profil', [ProfilController::class, 'show'])->name('profil');
        Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');
        Route::put('/profil/password', [ProfilController::class, 'ubahPassword'])->name('profil.password');
        Route::delete('/profil', [ProfilController::class, 'hapusAkun'])->name('profil.destroy');
    });

// Berkas privat (ADR-011) & Layanan Global Terautentikasi (Notifikasi Multi-Role & Pencarian Cepat)
Route::middleware(['auth', 'aktif'])->group(function () {
    Route::get('/berkas/bukti-pengiriman/{id}', BuktiPengirimanController::class)
        ->name('berkas.bukti-pengiriman');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    Route::get('/global-search', GlobalSearchController::class)->name('global-search');
});

// Galeri UI/UX (Lingkungan Pengembangan & Pengujian)
if (app()->environment(['local', 'testing'])) {
    Route::prefix('dev')->group(function () {
        Route::get('/ui', fn () => view('dev.ui-gallery'))->name('dev.ui');
        Route::get('/auth', fn () => view('dev.auth-preview'))->name('dev.auth');
        Route::get('/register', fn () => view('dev.register-preview'))->name('dev.register');
        Route::get('/owner-bon', fn () => view('dev.owner-bon-preview'))->name('dev.owner-bon');
    });
}