<?php

declare(strict_types=1);

namespace App\Livewire\Kasir;

use App\Exports\RiwayatSaldoLaciExport;
use App\Services\LaciService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

#[Layout('components.layouts.app')]
#[Title('Riwayat Saldo Laci — PASKA JAYA')]
class RiwayatSaldoLaci extends Component
{
    use WithPagination;

    #[Url]
    public ?string $tanggalAwal = null;

    #[Url]
    public ?string $tanggalAkhir = null;

    public bool $modalDetailOpen = false;

    public ?array $detailData = null;

    public function updatingTanggalAwal(): void
    {
        $this->resetPage();
    }

    public function updatingTanggalAkhir(): void
    {
        $this->resetPage();
    }

    public function resetFilter(): void
    {
        $this->tanggalAwal = null;
        $this->tanggalAkhir = null;
        $this->resetPage();
    }

    public function bukaModalDetail(int $penutupanId, LaciService $laciService): void
    {
        $this->detailData = $laciService->getRincianPenutupan($penutupanId);
        $this->modalDetailOpen = true;
    }

    public function tutupModalDetail(): void
    {
        $this->modalDetailOpen = false;
        $this->detailData = null;
    }

    public function exportExcel(): BinaryFileResponse
    {
        $namaFile = 'riwayat-saldo-laci-'.now()->format('Ymd-His').'.xlsx';

        return Excel::download(
            new RiwayatSaldoLaciExport($this->tanggalAwal, $this->tanggalAkhir),
            $namaFile
        );
    }

    public function exportPdf(): Response
    {
        $query = DB::table('v_riwayat_saldo_laci');

        if (! empty($this->tanggalAwal)) {
            $query->whereDate('tanggal', '>=', $this->tanggalAwal);
        }

        if (! empty($this->tanggalAkhir)) {
            $query->whereDate('tanggal', '<=', $this->tanggalAkhir);
        }

        $riwayat = $query->orderByDesc('penutupan_id')->get();

        $pdf = Pdf::loadView('exports.riwayat-saldo-laci-pdf', [
            'riwayat' => $riwayat,
            'tanggalAwal' => $this->tanggalAwal,
            'tanggalAkhir' => $this->tanggalAkhir,
        ])->setPaper('a4', 'portrait');

        $namaFile = 'riwayat-saldo-laci-'.now()->format('Ymd-His').'.pdf';

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            $namaFile,
            [
                'Content-Type' => 'application/pdf',
            ]
        );
    }

    #[Computed]
    public function riwayat(): LengthAwarePaginator
    {
        return app(LaciService::class)->getRiwayatSaldoLaci($this->tanggalAwal, $this->tanggalAkhir, 10);
    }

    public function render(): View
    {
        return view('livewire.kasir.riwayat-saldo-laci', [
            'riwayat' => $this->riwayat,
        ]);
    }
}
