<?php

use App\Http\Controllers\BuktiPengirimanController;
use App\Livewire\Hello;
use App\Livewire\Karyawan\Pengantaran\Detail as PengantaranDetail;
use App\Livewire\Karyawan\Pengantaran\Index as PengantaranIndex;
use App\Livewire\Kasir\CatatBon;
use App\Livewire\Kasir\DaftarBon as KasirDaftarBon;
use App\Livewire\Shared\Profil;
use Illuminate\Support\Facades\Route;

Route::get('/', Hello::class);

// Route Profil Saya (F10) untuk semua role yang sudah login
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/profil', Profil::class)->name('admin.profil');
    Route::get('/owner/profil', Profil::class)->name('owner.profil');
    Route::get('/kasir/profil', Profil::class)->name('kasir.profil');
    Route::get('/karyawan/profil', Profil::class)->name('karyawan.profil');

    // Placeholder logout sementara untuk layout app.blade.php
    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});

// Shortcut Login Khusus Pengujian Lokal (Langsung login sebagai Kasir)
Route::get('/dev-login', function () {
    $kasir = \App\Models\User::where('role_id', 3)->first() ?? \App\Models\User::first();
    auth()->login($kasir);
    return redirect()->route('kasir.daftar-bon');
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
        })->name('owner.bon');
    });

    Route::get('/admin/ringkasan', function () {
        return view('dev.ui-gallery', ['initialTab' => 'ringkasan', 'role' => 'admin']);
    })->name('admin.dashboard');

    Route::get('/admin/pengguna', function () {
        return view('dev.ui-gallery', ['initialTab' => 'pengguna', 'role' => 'admin']);
    })->name('admin.pengguna');

    Route::get('/owner/ringkasan', function () {
        return view('dev.ui-gallery', ['role' => 'owner']);
    })->name('owner.dashboard');

    Route::get('/owner/daftar-bon', function () {
        return view('dev.owner-bon-preview');
    })->name('owner.daftar-bon');
}

// ── Karyawan Panel: Pengantaran (F14) ──
Route::middleware(['auth', 'aktif', 'role:karyawan'])->prefix('karyawan')->group(function () {
    Route::get('/pengantaran', PengantaranIndex::class)->name('karyawan.pengantaran.index');
    Route::get('/pengantaran-menu', PengantaranIndex::class)->name('karyawan.pengantaran');
    Route::get('/pengantaran/{id}', PengantaranDetail::class)->name('karyawan.pengantaran.detail');
});

// ── Kasir Panel: Bon & Piutang (F07, F13) ──
Route::middleware(['auth', 'aktif', 'role:kasir'])->prefix('kasir')->group(function () {
    Route::get('/daftar-bon', KasirDaftarBon::class)->name('kasir.daftar-bon');
    Route::get('/catat-bon', CatatBon::class)->name('kasir.catat-bon');
});

// ── Berkas privat dengan Policy (ADR-011) ──
Route::middleware(['auth'])->group(function () {
    Route::get('/berkas/bukti-pengiriman/{id}', BuktiPengirimanController::class)->name('berkas.bukti-pengiriman');
});

// Fallback login route if auth not yet configured
if (! Route::has('login')) {
    Route::get('/login', function () {
        return redirect('/');
    })->name('login');
}