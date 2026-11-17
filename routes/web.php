<?php

use App\Livewire\Hello;
use Illuminate\Support\Facades\Route;

Route::get('/', Hello::class);

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
