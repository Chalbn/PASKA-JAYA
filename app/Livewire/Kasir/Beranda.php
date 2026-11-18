<?php

declare(strict_types=1);

namespace App\Livewire\Kasir;

use App\Models\Barang;
use App\Models\BonPiutang;
use App\Models\KasLaci;
use App\Models\Penjualan;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Beranda Kasir')]
class Beranda extends Component
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

    public function resetManual(): void
    {
        try {
            DB::select('CALL sp_reset_laci(?, ?)', ['manual', Auth::id()]);

            $this->modalResetOpen = false;
            $this->dispatch('toast', [
                'message' => 'Saldo laci kasir berhasil direset manual ke penutupan laci.',
                'type' => 'success',
            ]);
        } catch (\Throwable $e) {
            $this->dispatch('toast', [
                'message' => 'Gagal mereset laci: '.$e->getMessage(),
                'type' => 'error',
            ]);
        }
    }

    #[Computed]
    public function saldoTotal(): int
    {
        return (int) (KasLaci::query()->aktif()->sum('jumlah') ?? 0);
    }

    #[Computed]
    public function saldoCash(): int
    {
        return (int) (KasLaci::query()->aktif()->where('metode', 'cash')->sum('jumlah') ?? 0);
    }

    #[Computed]
    public function saldoTransfer(): int
    {
        return (int) (KasLaci::query()->aktif()->where('metode', 'transfer')->sum('jumlah') ?? 0);
    }

    #[Computed]
    public function transaksiTerbaru()
    {
        return KasLaci::query()->aktif()->latest('id')->take(5)->get();
    }

    #[Computed]
    public function transaksiHariIniCount(): int
    {
        return Penjualan::query()->langsung()->whereDate('waktu_transaksi', today())->count();
    }

    #[Computed]
    public function transaksiHariIniTotal(): int
    {
        return (int) (Penjualan::query()->langsung()->whereDate('waktu_transaksi', today())->sum('total') ?? 0);
    }

    #[Computed]
    public function stokMenipisCount(): int
    {
        return Barang::query()->stokMenipis()->count();
    }

    #[Computed]
    public function bonBelumLunasCount(): int
    {
        return BonPiutang::query()->belumLunas()->count();
    }

    #[Computed]
    public function bonBelumLunasSisa(): int
    {
        return (int) (BonPiutang::query()->belumLunas()->sum(
            DB::raw('CAST(total_tagihan AS SIGNED) - CAST(total_terbayar AS SIGNED)')
        ) ?? 0);
    }

    public function render(): View
    {
        return view('livewire.kasir.beranda', [
            'saldoTotal' => $this->saldoTotal,
            'saldoCash' => $this->saldoCash,
            'saldoTransfer' => $this->saldoTransfer,
            'transaksiTerbaru' => $this->transaksiTerbaru,
            'transaksiHariIniCount' => $this->transaksiHariIniCount,
            'transaksiHariIniTotal' => $this->transaksiHariIniTotal,
            'stokMenipisCount' => $this->stokMenipisCount,
            'bonBelumLunasCount' => $this->bonBelumLunasCount,
            'bonBelumLunasSisa' => $this->bonBelumLunasSisa,
        ]);
    }
}
