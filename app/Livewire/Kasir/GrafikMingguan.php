<?php

declare(strict_types=1);

namespace App\Livewire\Kasir;

use App\Services\LaciService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Grafik Penjualan Mingguan — PASKA JAYA')]
class GrafikMingguan extends Component
{
    #[Url]
    public string $mode = 'minggu_ini'; // 'minggu_ini', 'minggu_lalu', 'pilih_tanggal'

    #[Url]
    public ?string $customTanggal = null;

    public function mount(): void
    {
        if (empty($this->customTanggal)) {
            $this->customTanggal = now()->toDateString();
        }
    }

    public function updatedMode(): void
    {
        $this->dispatch('chart-data-updated', chartData: $this->grafikData);
    }

    public function updatedCustomTanggal(): void
    {
        if ($this->mode === 'pilih_tanggal') {
            $this->dispatch('chart-data-updated', chartData: $this->grafikData);
        }
    }

    #[Computed]
    public function grafikData(): array
    {
        return app(LaciService::class)->getGrafikMingguan($this->mode, $this->customTanggal);
    }

    public function render(): View
    {
        return view('livewire.kasir.grafik-mingguan', [
            'data' => $this->grafikData,
        ]);
    }
}
