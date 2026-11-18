<?php

declare(strict_types=1);

namespace App\Livewire\Owner;

use App\Enums\RolePengguna;
use App\Models\BonPiutang;
use App\Models\User;
use App\Services\LaporanService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Daftar Bon')]
class DaftarBon extends Component
{
    use WithPagination;

    #[Url]
    public string $filterStatus = 'semua';

    public bool $modalDetailOpen = false;

    /**
     * @var array{
     *     bon: object,
     *     items: array<int, object>,
     *     logs: array<int, object>,
     *     pembayaran: array<int, object>,
     *     timeline: array<int, array{key: string, title: string, date: ?string, status: string, note: ?string}>
     * }|null
     */
    public ?array $detailBon = null;

    public function mount(): void
    {
        $this->otorisasiOwner();
    }

    public function setFilter(string $status): void
    {
        $this->otorisasiOwner();
        $this->filterStatus = in_array($status, ['semua', 'belum_lunas', 'lunas'], true)
            ? $status
            : 'semua';
        $this->resetPage();
    }

    public function bukaDetail(int $bonId, LaporanService $laporanService): void
    {
        $this->otorisasiOwner();
        $detail = $laporanService->detailBonOwner($bonId);

        if (! $detail) {
            $this->dispatch('toast', [
                'message' => 'Data bon tidak ditemukan.',
                'type' => 'error',
            ]);

            return;
        }

        $this->detailBon = $detail;
        $this->modalDetailOpen = true;
    }

    public function tutupDetail(): void
    {
        $this->modalDetailOpen = false;
        $this->detailBon = null;
    }

    public function render(LaporanService $laporanService): View
    {
        $this->otorisasiOwner();

        $hasil = $laporanService->daftarBonOwner($this->filterStatus, 10);

        return view('livewire.owner.daftar-bon', [
            'daftarBon' => $hasil['daftarBon'],
            'itemsByPenjualanId' => $hasil['itemsByPenjualanId'],
        ]);
    }

    private function otorisasiOwner(): void
    {
        $user = Auth::user();
        if (! $user instanceof User || ! $user->hasRole(RolePengguna::Owner)) {
            abort(403, 'Hanya Owner yang diizinkan memantau Daftar Bon Owner.');
        }

        Gate::authorize('viewAny', BonPiutang::class);
    }
}
