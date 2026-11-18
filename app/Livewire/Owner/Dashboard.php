<?php

declare(strict_types=1);

namespace App\Livewire\Owner;

use App\Enums\RolePengguna;
use App\Models\User;
use App\Services\LaporanService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Ringkasan Toko')]
class Dashboard extends Component
{
    public function mount(): void
    {
        $this->otorisasiOwner();
    }

    public function render(LaporanService $laporanService): View
    {
        $this->otorisasiOwner();

        return view('livewire.owner.dashboard', [
            'ringkasan' => $laporanService->ringkasanToko(),
        ]);
    }

    private function otorisasiOwner(): void
    {
        $user = Auth::user();
        if (! $user instanceof User || ! $user->hasRole(RolePengguna::Owner)) {
            abort(403, 'Hanya Owner yang diizinkan mengakses halaman Ringkasan Toko.');
        }
    }
}
