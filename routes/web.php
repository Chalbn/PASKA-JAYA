<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (app()->environment(['local', 'testing'])) {
        return redirect()->route('dev.ui');
    }

    return view('welcome');
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
    });
}
