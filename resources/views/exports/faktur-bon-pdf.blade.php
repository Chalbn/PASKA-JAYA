@php
    $status = ($bon->status_bayar instanceof \BackedEnum) ? $bon->status_bayar->value : (string) $bon->status_bayar;
    $namaPelanggan = $bon->pelanggan?->nama ?? $bon->penjualan?->nama_pelanggan ?? '-';
    $items = $bon->penjualan?->detailPenjualan ?? collect();
    $sisa = max(0, (int) $bon->total_tagihan - (int) $bon->total_dibayar);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Faktur {{ $bon->kode_bon }} — PASKA JAYA</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #1E293B; font-size: 11px; margin: 22px; }
        .header { border-bottom: 2px solid #1E3A8A; padding-bottom: 12px; margin-bottom: 16px; }
        .brand { font-size: 18px; font-weight: bold; color: #1E3A8A; margin: 0; }
        .sub { font-size: 11px; color: #64748B; margin: 3px 0 0 0; }
        .meta { width: 100%; margin-bottom: 16px; font-size: 10px; }
        .meta td { padding: 3px 0; vertical-align: top; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 4px; font-weight: bold; font-size: 9px; text-transform: uppercase; }
        .badge-lunas { background: #ECFDF5; color: #0F766E; }
        .badge-belum { background: #FEF3C7; color: #8A3B08; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .table th { background: #1E3A8A; color: #FFF; font-size: 9px; text-transform: uppercase; padding: 7px 6px; text-align: left; }
        .table td { padding: 7px 6px; border-bottom: 1px solid #E2E8F0; font-size: 10px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary { width: 45%; margin-left: auto; border-collapse: collapse; margin-bottom: 24px; }
        .summary td { padding: 5px 6px; font-size: 11px; border-bottom: 1px solid #E2E8F0; }
        .ttd { width: 100%; margin-top: 32px; text-align: center; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <p class="brand">TOKO BAHAN BANGUNAN PASKA JAYA</p>
        <p class="sub">Faktur Penjualan &amp; Piutang (Bon)</p>
    </div>

    <table class="meta">
        <tr>
            <td style="width: 55%;">
                <strong>Kepada:</strong> {{ $namaPelanggan }}<br>
                <strong>No. HP:</strong> {{ $bon->pelanggan?->no_hp ?? '-' }}<br>
                <strong>Alamat:</strong> {{ $bon->pelanggan?->alamat ?? '-' }}
            </td>
            <td style="width: 45%;" class="text-right">
                <strong>No. Bon:</strong> {{ $bon->kode_bon }}<br>
                <strong>Tanggal Bon:</strong> {{ $bon->tanggal_bon ? \Carbon\Carbon::parse($bon->tanggal_bon)->format('d/m/Y') : '-' }}<br>
                <strong>Jatuh Tempo:</strong> {{ $bon->jatuh_tempo ? \Carbon\Carbon::parse($bon->jatuh_tempo)->format('d/m/Y') : '-' }}<br>
                <strong>Status:</strong>
                <span class="badge {{ $status === 'lunas' ? 'badge-lunas' : 'badge-belum' }}">
                    {{ $status === 'lunas' ? 'LUNAS' : 'BELUM LUNAS' }}
                </span>
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Harga Satuan</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td class="text-center">{{ $item->jumlah }} {{ $item->satuan }}</td>
                    <td class="text-right">{{ rupiah($item->harga_satuan) }}</td>
                    <td class="text-right">{{ rupiah($item->subtotal) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">Tidak ada rincian barang.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="summary">
        <tr>
            <td>Total Tagihan</td>
            <td class="text-right"><strong>{{ rupiah($bon->total_tagihan) }}</strong></td>
        </tr>
        <tr>
            <td>Sudah Dibayar</td>
            <td class="text-right">{{ rupiah($bon->total_dibayar) }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; color: #1E3A8A;">Sisa Tagihan</td>
            <td class="text-right" style="font-weight: bold; color: #B91C1C;">{{ rupiah($sisa) }}</td>
        </tr>
    </table>

    @if($bon->pembayaran->isNotEmpty())
        <p style="font-weight: bold; font-size: 10px; margin-bottom: 6px; color: #1E3A8A;">Riwayat Pembayaran:</p>
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Metode</th>
                    <th>Catatan</th>
                    <th class="text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bon->pembayaran as $bayar)
                    @php $m = ($bayar->metode instanceof \BackedEnum) ? $bayar->metode->value : (string) $bayar->metode; @endphp
                    <tr>
                        <td>{{ $bayar->waktu_bayar ? \Carbon\Carbon::parse($bayar->waktu_bayar)->timezone('Asia/Jakarta')->format('d/m/Y H:i') : '-' }}</td>
                        <td>{{ strtoupper($m) }}</td>
                        <td>{{ $bayar->catatan ?: '-' }}</td>
                        <td class="text-right">{{ rupiah($bayar->jumlah) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <table class="ttd">
        <tr>
            <td style="width: 50%;">Penerima / Pelanggan<br><br><br><br>( {{ $namaPelanggan }} )</td>
            <td style="width: 50%;">Hormat Kami, Kasir<br><br><br><br>( {{ $bon->pembuatBon?->nama ?? 'Kasir Paska Jaya' }} )</td>
        </tr>
    </table>
</body>
</html>