<?php

declare(strict_types=1);

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RiwayatSaldoLaciExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected ?string $tanggalAwal;

    protected ?string $tanggalAkhir;

    public function __construct(?string $tanggalAwal = null, ?string $tanggalAkhir = null)
    {
        $this->tanggalAwal = $tanggalAwal;
        $this->tanggalAkhir = $tanggalAkhir;
    }

    public function collection(): Collection
    {
        $query = DB::table('v_riwayat_saldo_laci');

        if (! empty($this->tanggalAwal)) {
            $query->whereDate('tanggal', '>=', $this->tanggalAwal);
        }

        if (! empty($this->tanggalAkhir)) {
            $query->whereDate('tanggal', '<=', $this->tanggalAkhir);
        }

        return $query->orderByDesc('penutupan_id')->get();
    }

    public function headings(): array
    {
        return [
            'ID Penutupan',
            'Tanggal',
            'Saldo Cash (Rp)',
            'Saldo Transfer (Rp)',
            'Total Saldo (Rp)',
            'Waktu Reset',
            'Tipe Reset',
            'Direset Oleh',
        ];
    }

    /**
     * @param  object  $row
     */
    public function map($row): array
    {
        return [
            $row->penutupan_id,
            $row->tanggal,
            $row->total_cash,
            $row->total_transfer,
            $row->total,
            $row->waktu_reset,
            ucfirst($row->tipe_reset ?? 'manual'),
            $row->direset_oleh,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E3A8A'],
                ],
            ],
        ];
    }
}
