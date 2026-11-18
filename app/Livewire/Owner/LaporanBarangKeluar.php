<?php

declare(strict_types=1);

namespace App\Livewire\Owner;

use App\Enums\RolePengguna;
use App\Models\User;
use App\Services\LaporanService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('components.layouts.app')]
#[Title('Laporan Barang Keluar')]
class LaporanBarangKeluar extends Component
{
    use WithPagination;

    #[Url]
    public string $dariTanggal = '';

    #[Url]
    public string $sampaiTanggal = '';

    #[Url]
    public string $metode = 'semua';

    /**
     * Daftar ID penjualan yang sub-baris rinciannya sedang dibuka.
     *
     * @var array<int, bool>
     */
    public array $expandedRows = [];

    public function mount(): void
    {
        $this->otorisasiOwner();

        $sekarang = Carbon::now('Asia/Jakarta');
        if (empty($this->dariTanggal)) {
            $this->dariTanggal = $sekarang->copy()->subDays(6)->toDateString();
        }
        if (empty($this->sampaiTanggal)) {
            $this->sampaiTanggal = $sekarang->toDateString();
        }
    }

    public function updatingDariTanggal(): void
    {
        $this->resetPage();
        $this->expandedRows = [];
    }

    public function updatingSampaiTanggal(): void
    {
        $this->resetPage();
        $this->expandedRows = [];
    }

    public function updatingMetode(): void
    {
        $this->resetPage();
        $this->expandedRows = [];
    }

    public function toggleRincian(int $penjualanId): void
    {
        if (! empty($this->expandedRows[$penjualanId])) {
            unset($this->expandedRows[$penjualanId]);
        } else {
            $this->expandedRows[$penjualanId] = true;
        }
    }

    public function unduhCsv(LaporanService $laporanService): StreamedResponse
    {
        $this->otorisasiOwner();

        $namaFile = 'laporan-barang-keluar-'.Carbon::now('Asia/Jakarta')->format('Ymd').'.csv';
        $csvContent = $laporanService->buatCsv($this->dariTanggal, $this->sampaiTanggal, $this->metode);

        return response()->streamDownload(
            static function () use ($csvContent): void {
                echo $csvContent;
            },
            $namaFile,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]
        );
    }

    public function render(LaporanService $laporanService): View
    {
        $this->otorisasiOwner();

        $hasil = $laporanService->laporanPenjualanPaginated(
            $this->dariTanggal,
            $this->sampaiTanggal,
            $this->metode,
            10,
            (int) $this->getPage(),
            route('owner.laporan')
        );

        return view('livewire.owner.laporan-barang-keluar', [
            'ringkasan' => $hasil['ringkasan'],
            'transaksi' => $hasil['transaksi'],
            'detailsByPenjualanId' => $hasil['detailsByPenjualanId'],
        ]);
    }

    private function otorisasiOwner(): void
    {
        $user = Auth::user();
        if (! $user instanceof User || ! $user->hasRole(RolePengguna::Owner)) {
            abort(403, 'Hanya Owner yang diizinkan mengakses Laporan Barang Keluar.');
        }
    }
}
