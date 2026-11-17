<?php

namespace App\Livewire\Karyawan\Pengantaran;

use App\Enums\StatusPengiriman;
use App\Models\PengirimanBarang;
use App\Services\PengirimanService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.karyawan')]
#[Title('Pengantaran')]
class Index extends Component
{
    #[Url]
    public string $tab = 'akan_diantar';

    // ── Computed: statistik kartu ──

    #[Computed]
    public function jumlahBaru(): int
    {
        return PengirimanBarang::where('karyawan_id', Auth::id())
            ->whereNull('dibaca_pada')
            ->whereNotIn('status', [
                StatusPengiriman::Selesai->value,
                StatusPengiriman::Dibatalkan->value,
            ])
            ->count();
    }

    #[Computed]
    public function jumlahBerjalan(): int
    {
        return PengirimanBarang::where('karyawan_id', Auth::id())
            ->whereIn('status', [
                StatusPengiriman::DikirimKeKaryawan->value,
                StatusPengiriman::SedangDisiapkan->value,
                StatusPengiriman::SiapBerangkat->value,
            ])
            ->count();
    }

    #[Computed]
    public function jumlahSelesai(): int
    {
        return PengirimanBarang::where('karyawan_id', Auth::id())
            ->where('status', StatusPengiriman::Selesai->value)
            ->count();
    }

    // ── Computed: daftar pengiriman per tab ──

    #[Computed]
    public function pengirimanAkanDiantar()
    {
        return PengirimanBarang::with([
            'bonPiutang.pelanggan',
            'detailPengiriman',
        ])
            ->where('karyawan_id', Auth::id())
            ->whereIn('status', [
                StatusPengiriman::DikirimKeKaryawan->value,
                StatusPengiriman::SedangDisiapkan->value,
                StatusPengiriman::SiapBerangkat->value,
            ])
            ->orderByRaw("CASE WHEN jadwal_kirim IS NOT NULL THEN 0 ELSE 1 END ASC")
            ->orderBy('jadwal_kirim', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    #[Computed]
    public function pengirimanSelesai()
    {
        return PengirimanBarang::with([
            'bonPiutang.pelanggan',
            'detailPengiriman',
        ])
            ->where('karyawan_id', Auth::id())
            ->where('status', StatusPengiriman::Selesai->value)
            ->orderBy('waktu_selesai', 'desc')
            ->get();
    }

    /**
     * Cek apakah pengiriman ini punya jadwal terdekat (badge BERIKUTNYA).
     */
    public function isJadwalTerdekat(PengirimanBarang $pengiriman): bool
    {
        $akanDiantar = $this->pengirimanAkanDiantar;

        $denganJadwal = $akanDiantar->filter(fn ($p) => $p->jadwal_kirim !== null);

        if ($denganJadwal->isEmpty()) {
            return false;
        }

        $terdekat = $denganJadwal->sortBy('jadwal_kirim')->first();

        return $terdekat && $terdekat->id === $pengiriman->id;
    }

    public function render()
    {
        return view('livewire.karyawan.pengantaran.index');
    }
}

