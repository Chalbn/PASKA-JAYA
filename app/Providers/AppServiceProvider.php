<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\AnggotaTim;
use App\Models\Barang;
use App\Models\PenggunaanBahan;
use App\Models\PengirimanBarang;
use App\Models\User;
use App\Policies\AnggotaTimPolicy;
use App\Policies\BarangPolicy;
use App\Policies\PenggunaanBahanPolicy;
use App\Policies\PenggunaPolicy;
use App\Policies\PengirimanBarangPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $helperPath = app_path('Helpers/helpers.php');
        if (file_exists($helperPath)) {
            require_once $helperPath;
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
        Gate::policy(User::class, PenggunaPolicy::class);
        Gate::policy(AnggotaTim::class, AnggotaTimPolicy::class);
    }
}
