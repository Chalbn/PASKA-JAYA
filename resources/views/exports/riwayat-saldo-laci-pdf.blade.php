<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Riwayat Saldo Laci — PASKA JAYA</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1E293B;
            font-size: 11px;
            margin: 20px;
        }
        .header {
            border-bottom: 2px solid #1E3A8A;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #1E3A8A;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .subtitle {
            font-size: 11px;
            color: #64748B;
            margin: 4px 0 0 0;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 10px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th {
            background-color: #1E3A8A;
            color: #FFFFFF;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
            padding: 8px 6px;
            text-align: left;
        }
        .data-table td {
            padding: 7px 6px;
            border-bottom: 1px solid #E2E8F0;
            font-size: 10px;
        }
        .data-table tr:nth-child(even) {
            background-color: #F8FAFC;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .font-bold {
            font-weight: bold;
        }
        .total-row {
            background-color: #F1F5F9 !important;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            font-size: 9px;
            color: #94A3B8;
            text-align: right;
            border-top: 1px solid #E2E8F0;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">PASKA JAYA — LAPORAN RIWAYAT SALDO LACI</h1>
        <p class="subtitle">Sistem Manajemen Toko Bahan Bangunan • Rekap Penutupan & Reset Laci Kasir</p>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 50%;">
                <strong>Periode:</strong> 
                @if($tanggalAwal && $tanggalAkhir)
                    {{ tanggal_id($tanggalAwal) }} s.d. {{ tanggal_id($tanggalAkhir) }}
                @elseif($tanggalAwal)
                    Mulai {{ tanggal_id($tanggalAwal) }}
                @elseif($tanggalAkhir)
                    Sampai {{ tanggal_id($tanggalAkhir) }}
                @else
                    Semua Riwayat
                @endif
            </td>
            <td style="width: 50%; text-align: right;">
                <strong>Waktu Cetak:</strong> {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 17%;" class="text-right">Saldo Cash</th>
                <th style="width: 17%;" class="text-right">Saldo Transfer</th>
                <th style="width: 18%;" class="text-right">Total Saldo</th>
                <th style="width: 14%;" class="text-center">Waktu Reset</th>
                <th style="width: 14%;">Direset Oleh</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandCash = 0;
                $grandTransfer = 0;
                $grandTotal = 0;
            @endphp
            @forelse($riwayat as $index => $row)
                @php
                    $grandCash += $row->total_cash;
                    $grandTransfer += $row->total_transfer;
                    $grandTotal += $row->total;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ tanggal_id($row->tanggal) }}</td>
                    <td class="text-right">{{ rupiah($row->total_cash) }}</td>
                    <td class="text-right">{{ rupiah($row->total_transfer) }}</td>
                    <td class="text-right font-bold">{{ rupiah($row->total) }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($row->waktu_reset)->timezone('Asia/Jakarta')->format('H:i:s') }}</td>
                    <td>{{ $row->direset_oleh }} ({{ ucfirst($row->tipe_reset) }})</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px; color: #94A3B8;">
                        Tidak ada catatan riwayat penutupan laci pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if(count($riwayat) > 0)
            <tfoot>
                <tr class="total-row">
                    <td colspan="2" class="text-center font-bold">TOTAL KESELURUHAN</td>
                    <td class="text-right font-bold">{{ rupiah($grandCash) }}</td>
                    <td class="text-right font-bold">{{ rupiah($grandTransfer) }}</td>
                    <td class="text-right font-bold" style="color: #1E3A8A;">{{ rupiah($grandTotal) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="footer">
        Dicetak secara otomatis oleh Sistem PASKA JAYA. Dokumen ini sah sebagai rekapitulasi operasional toko.
    </div>
</body>
</html>
