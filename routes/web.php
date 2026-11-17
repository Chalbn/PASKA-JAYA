<?php

use App\Livewire\Hello;
use App\Livewire\Shared\MasterItem\Index as MasterItemIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', Hello::class)->name('home');

Route::get('/login', function () {
    return redirect('/');
})->name('login');

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/login');
})->name('logout');

// Route Master Item untuk Owner & Kasir (Akses Penuh Keduanya)
Route::middleware(['auth', 'aktif', 'role:owner,admin'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/master-item', MasterItemIndex::class)->name('master-item');
});

Route::middleware(['auth', 'aktif', 'role:kasir,admin'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/master-item', MasterItemIndex::class)->name('master-item');
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
