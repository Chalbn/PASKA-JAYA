<?php

use App\Models\SystemConfig;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('laci:reset')
    ->dailyAt('00:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

Schedule::command('backup:run')
    ->dailyAt('02:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->when(function (): bool {
        try {
            return SystemConfig::nilai('auto_backup', '1') === '1';
        } catch (Throwable) {
            return false;
        }
    });
