<?php

declare(strict_types=1);

namespace App\Livewire\Kasir;

use App\Services\LaciService;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Lihat Saldo — PASKA JAYA')]
class LihatSaldo extends Component
{
    public bool $modalResetOpen = false;

    public function bukaModalReset(): void
    {
        $this->modalResetOpen = true;
    }

    public function tutupModalReset(): void
    {
        $this->modalResetOpen = false;
    }

    public function resetManual(LaciService $laciService): void
    {
        try {
            $hasil = $laciService->resetManual(Auth::id());

            $this->modalResetOpen = false;
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Saldo laci berhasil direset manual sebesar '.rupiah($hasil['total']).'.',
            ]);
        } catch (DomainException $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Gagal mereset laci: '.$e->getMessage(),
            ]);
        }
    }

    #[Computed]
    public function saldo(): array
    {
        return app(LaciService::class)->getSaldoAktif();
    }

    #[Computed]
    public function transaksiTerbaru(): Collection
    {
        return app(LaciService::class)->getTransaksiTerbaru(10);
    }

    #[Computed]
    public function riwayat7Hari(): Collection
    {
        return app(LaciService::class)->getRiwayat7Hari(7);
    }

    public function render(): View
    {
        return view('livewire.kasir.lihat-saldo', [
            'saldo' => $this->saldo,
            'transaksiTerbaru' => $this->transaksiTerbaru,
            'riwayat7Hari' => $this->riwayat7Hari,
        ]);
    }
}
