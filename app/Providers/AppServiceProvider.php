<?php

namespace App\Providers;

use App\Http\Middleware\PastikanAktif;
use App\Http\Middleware\PastikanRole;
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
        Livewire::addPersistentMiddleware([
            PastikanAktif::class,
            PastikanRole::class,
        ]);
    }
}
