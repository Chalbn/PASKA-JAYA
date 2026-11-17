
<?php

use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\BuktiPengirimanController;
use App\Livewire\Hello;
use App\Livewire\Karyawan\Pengantaran\Detail as PengantaranDetail;
use App\Livewire\Karyawan\Pengantaran\Index as PengantaranIndex;
use App\Livewire\Shared\MasterItem\Index as MasterItemIndex;
use App\Livewire\Shared\Profil;
use Illuminate\Support\Facades\Route;

Route::get('/', Hello::class)->name('home');

// Login
Route::get('/login', function () {
    return redirect('/');
})->name('login');

// Logout
Route::post('/logout', LogoutController::class)->name('logout');

// Master Item Owner
Route::middleware(['auth', 'aktif', 'role:owner,admin'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {
        Route::get('/master-item', MasterItemIndex::class)->name('master-item');
    });

// Master Item Kasir
Route::middleware(['auth', 'aktif', 'role:kasir,admin'])
    ->prefix('kasir')
    ->name('kasir.')
    ->group(function () {
        Route::get('/master-item', MasterItemIndex::class)->name('master-item');
    });

// Profil
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/profil', Profil::class)->name('admin.profil');
    Route::get('/owner/profil', Profil::class)->name('owner.profil');
    Route::get('/kasir/profil', Profil::class)->name('kasir.profil');
    Route::get('/karyawan/profil', Profil::class)->name('karyawan.profil');
});

// Development Login
Route::get('/dev-login', function () {
    auth()->login(\App\Models\User::first());

    return redirect()->route('karyawan.profil');
});

// Galeri UI/UX
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

    Route::get('/admin/ringkasan', function () {
        return view('dev.ui-gallery', [
            'initialTab' => 'ringkasan',
            'role' => 'admin',
        ]);
    })->name('admin.dashboard');

    Route::get('/admin/pengguna', function () {
        return view('dev.ui-gallery', [
            'initialTab' => 'pengguna',
            'role' => 'admin',
        ]);
    })->name('admin.pengguna');

    Route::get('/owner/ringkasan', function () {
        return view('dev.ui-gallery', ['role' => 'owner']);
    })->name('owner.dashboard');

    Route::get('/owner/daftar-bon', function () {
        return view('dev.owner-bon-preview');
    })->name('owner.bon');
}

// Pengantaran Karyawan (F14)
Route::middleware(['auth', 'aktif', 'role:karyawan'])
    ->prefix('karyawan')
    ->group(function () {
        Route::get('/pengantaran', PengantaranIndex::class)
            ->name('karyawan.pengantaran.index');

        Route::get('/pengantaran-menu', PengantaranIndex::class)
            ->name('karyawan.pengantaran');

        Route::get('/pengantaran/{id}', PengantaranDetail::class)
            ->name('karyawan.pengantaran.detail');
    });

// Berkas privat (ADR-011)
Route::middleware(['auth'])->group(function () {
    Route::get('/berkas/bukti-pengiriman/{id}', BuktiPengirimanController::class)
        ->name('berkas.bukti-pengiriman');
});
