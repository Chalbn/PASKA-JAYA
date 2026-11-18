<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\StatusSistemService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Ringkasan Sistem (F02) — halaman beranda Admin.
 */
#[Layout('components.layouts.app')]
#[Title('Ringkasan Sistem')]
class Dashboard extends Component
{
    public function mount(): void
    {
        Gate::authorize('viewAny', User::class);
    }

    public function render(StatusSistemService $statusSistem): View
    {
        Gate::authorize('viewAny', User::class);

        return view('livewire.admin.dashboard', [
            'kartu' => $statusSistem->kartuRingkasan(),
            'perRole' => $statusSistem->penggunaPerRole(),
            'status' => $statusSistem->statusSistem(),
        ]);
    }
}
