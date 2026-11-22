@php
    $metode = ($penjualan->metode_bayar instanceof \BackedEnum)
        ? $penjualan->metode_bayar->value
        : (string) $penjualan->metode_bayar;
    $waktu = $penjualan->waktu_transaksi
        ? \Carbon\Carbon::parse($penjualan->waktu_transaksi)->timezone('Asia/Jakarta')->format('d/m/Y H:i')
        : '-';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota {{ $penjualan->kode_transaksi }} — PASKA JAYA</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #1E293B; font-size: 11px; margin: 18px; }
        .header { border-bottom: 2px solid #1E3A8A; padding-bottom: 10px; margin-bottom: 14px; }
        .brand { font-size: 16px; font-weight: bold; color: #1E3A8A; margin: 0; }
        .sub { font-size: 10px; color: #64748B; margin: 2px 0 0 0; }
        .meta { width: 100%; margin-bottom: 14px; font-size: 10px; }
        .meta td { padding: 2px 0; }
        .items { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .items th { background: #1E3A8A; color: #FFF; font-size: 9px; text-transform: uppercase; padding: 6px; text-align: left; }
        .items td { padding: 6px; border-bottom: 1px solid #E2E8F0; font-size: 10px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-box { width: 100%; border-top: 2px solid #1E3A8A; padding-top: 8px; }
        .footer { margin-top: 24px; text-align: center; font-size: 9px; color: #64748B; border-top: 1px dashed #CBD5E1; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <p class="brand">TOKO BAHAN BANGUNAN PASKA JAYA</p>
        <p class="sub">Nota Penjualan Barang Keluar</p>
    </div>

    <table class="meta">
        <tr>
            <td style="width: 55%;"><strong>No. Nota:</strong> {{ $penjualan->kode_transaksi }}</td>
            <td style="width: 45%;" class="text-right"><strong>Tanggal:</strong> {{ $waktu }} WIB</td>
        </tr>
        <tr>
            <td><strong>Pelanggan:</strong> {{ $penjualan->nama_pelanggan ?: ($penjualan->pelanggan?->nama ?? 'Umum') }}</td>
            <td class="text-right"><strong>Metode:</strong> {{ strtoupper($metode) }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Kasir:</strong> {{ $penjualan->user?->nama ?? '-' }}</td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Barang</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penjualan->detailPenjualan as $item)
                <tr>
                    <td>{{ $item->nama_barang }}</td>
                    <td class="text-center">{{ $item->jumlah }} {{ $item->satuan }}</td>
                    <td class="text-right">{{ rupiah($item->harga_satuan) }}</td>
                    <td class="text-right">{{ rupiah($item->subtotal) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="total-box">
        <tr>
            <td style="font-size: 12px; font-weight: bold; color: #1E3A8A;">TOTAL PEMBAYARAN</td>
            <td class="text-right" style="font-size: 14px; font-weight: bold; color: #1E3A8A;">{{ rupiah($penjualan->total) }}</td>
        </tr>
    </table>

    <div class="footer">
        Terima kasih telah berbelanja di Toko Bahan Bangunan PASKA JAYA.<br>
        Barang yang sudah dibeli tidak dapat ditukar kecuali ada perjanjian sebelumnya.
    </div>
</body>
</html>