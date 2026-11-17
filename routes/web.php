<?php

use App\Livewire\Hello;
use App\Livewire\Shared\Profil;
use Illuminate\Support\Facades\Route;

Route::get('/', Hello::class);

// Route Profil Saya (F10) untuk semua role yang sudah login
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/profil', Profil::class)->name('admin.profil');
    Route::get('/owner/profil', Profil::class)->name('owner.profil');
    Route::get('/kasir/profil', Profil::class)->name('kasir.profil');
    Route::get('/karyawan/profil', Profil::class)->name('karyawan.profil');

    // Placeholder logout sementara untuk layout app.blade.php (akan diganti LogoutController saat merge feat/auth)
    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});

Route::get('/dev-login', function () {
    auth()->login(\App\Models\User::first());
    return redirect()->route('karyawan.profil');
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

    // Preview navigasi admin sesuai config/menu.php
    Route::get('/admin/ringkasan', function () {
        return view('dev.ui-gallery', ['initialTab' => 'ringkasan', 'role' => 'admin']);
    })->name('admin.dashboard');

    Route::get('/admin/pengguna', function () {
        return view('dev.ui-gallery', ['initialTab' => 'pengguna', 'role' => 'admin']);
    })->name('admin.pengguna');

    // Preview navigasi owner sesuai config/menu.php
    Route::get('/owner/ringkasan', function () {
        return view('dev.ui-gallery', ['role' => 'owner']);
    })->name('owner.dashboard');

    Route::get('/owner/daftar-bon', function () {
        return view('dev.owner-bon-preview');
    })->name('owner.bon');
}