<?php

namespace App\Livewire\Karyawan\Pengantaran;

use App\Enums\StatusPengiriman;
use App\Models\PengirimanBarang;
use App\Services\PengirimanService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.karyawan')]
#[Title('Detail Pengantaran')]
class Detail extends Component
{
    use WithFileUploads;

    #[Locked]
    public int $pengirimanId;

    public $fotoBukti;

    public function mount(int $id): void
    {
        $this->pengirimanId = $id;

        $pengiriman = PengirimanBarang::findOrFail($id);

        // Policy: hanya karyawan pemilik
        $this->authorize('view', $pengiriman);

        // Tandai dibaca saat pertama kali membuka
        app(PengirimanService::class)->tandaiDibaca($pengiriman);
    }

    public function getPengirimanProperty(): PengirimanBarang
    {
        return PengirimanBarang::with([
            'bonPiutang.pelanggan',
            'bonPiutang.penjualan',
            'detailPengiriman',
            'logStatus' => fn ($q) => $q->orderBy('created_at', 'asc'),
            'karyawan',
        ])->findOrFail($this->pengirimanId);
    }

    public function getStatusBerikutnyaProperty(): ?StatusPengiriman
    {
        return app(PengirimanService::class)->statusBerikutnya($this->pengiriman->status);
    }

    public function getLabelTombolProperty(): string
    {
        return app(PengirimanService::class)->labelStatusBerikutnya($this->pengiriman->status);
    }

    public function getPerluFotoProperty(): bool
    {
        // Foto diperlukan saat mau ke status Selesai
        return $this->statusBerikutnya === StatusPengiriman::Selesai;
    }

    public function getStepperProperty(): array
    {
        $steps = [
            StatusPengiriman::SedangDisiapkan,
            StatusPengiriman::SiapBerangkat,
            StatusPengiriman::Selesai,
        ];

        $currentStatus = $this->pengiriman->status;

        $statusOrder = [
            StatusPengiriman::DikirimKeKaryawan->value => -1,
            StatusPengiriman::SedangDisiapkan->value => 0,
            StatusPengiriman::SiapBerangkat->value => 1,
            StatusPengiriman::Selesai->value => 2,
        ];

        $currentIndex = $statusOrder[$currentStatus->value] ?? -1;

        return collect($steps)->map(function ($step, $index) use ($currentIndex) {
            return [
                'label' => $step->label(),
                'status' => $step->value,
                'selesai' => $index <= $currentIndex,
                'aktif' => $index === $currentIndex,
            ];
        })->toArray();
    }

    public function ubahStatus(): void
    {
        $pengiriman = PengirimanBarang::findOrFail($this->pengirimanId);

        $this->authorize('updateStatus', $pengiriman);

        $service = app(PengirimanService::class);
        $statusBaru = $service->statusBerikutnya($pengiriman->status);

        if (! $statusBaru) {
            $this->dispatch('toast', type: 'error', message: 'Tidak ada status berikutnya.');

            return;
        }

        $fotoBuktiPath = null;

        // Jika menuju selesai, foto wajib
        if ($statusBaru === StatusPengiriman::Selesai) {
            $this->validate([
                'fotoBukti' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            ], [
                'fotoBukti.required' => 'Foto bukti wajib diunggah sebelum menyelesaikan pengantaran.',
                'fotoBukti.image' => 'File harus berupa gambar.',
                'fotoBukti.mimes' => 'Format gambar harus jpg, jpeg, png, atau webp.',
                'fotoBukti.max' => 'Ukuran gambar maksimal 4 MB.',
            ]);

            $fotoBuktiPath = $this->fotoBukti->store('bukti-pengiriman', 'local');
        }

        try {
            $service->ubahStatus($pengiriman, $statusBaru, Auth::user(), $fotoBuktiPath);
            $this->fotoBukti = null;
            $this->dispatch('toast', type: 'success', message: 'Status berhasil diubah ke '.$statusBaru->label());
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.karyawan.pengantaran.detail');
    }
}
