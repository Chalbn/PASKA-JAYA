<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\LaciService;
use Illuminate\Console\Command;
use Throwable;

class ResetLaciHarian extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laci:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset saldo laci kasir harian secara otomatis pukul 00:00 (ADR-008)';

    /**
     * Execute the console command.
     */
    public function handle(LaciService $laciService): int
    {
        $this->info('Memulai reset laci kasir otomatis...');

        try {
            $hasil = $laciService->resetOtomatis();

            $this->info(sprintf(
                'Laci berhasil direset otomatis (ID: %d, Tanggal: %s, Total: Rp %s).',
                $hasil['penutupan_id'],
                $hasil['tanggal'],
                number_format($hasil['total'], 0, ',', '.')
            ));

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Gagal mereset laci: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
