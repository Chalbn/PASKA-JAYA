<?php

namespace App\Providers;

use App\Http\Middleware\PastikanAktif;
use App\Http\Middleware\PastikanRole;
use App\Models\Barang;
use App\Models\PenggunaanBahan;
use App\Models\PengirimanBarang;
use App\Policies\BarangPolicy;
use App\Policies\PenggunaanBahanPolicy;
use App\Policies\PengirimanBarangPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (file_exists(app_path('helpers.php'))) {
            require_once app_path('helpers.php');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Barang::class, BarangPolicy::class);
        Gate::policy(PengirimanBarang::class, PengirimanBarangPolicy::class);
        Gate::policy(PenggunaanBahan::class, PenggunaanBahanPolicy::class);

        Livewire::addPersistentMiddleware([
            PastikanAktif::class,
            PastikanRole::class,
        ]);
    }
}