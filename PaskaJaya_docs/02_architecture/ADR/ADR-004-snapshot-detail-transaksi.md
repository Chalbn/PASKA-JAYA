# ADR-004: Snapshot nama, satuan, dan harga pada detail transaksi

**Status**: Accepted · **Tanggal**: 14 November 2026 · **Diputuskan oleh**: Kelompok 7 (PASKA JAYA)

## Context
Harga/nama barang dan nama pelanggan dapat berubah di master, tetapi riwayat tidak boleh ikut berubah.

## Decision
`detail_penjualan` & `detail_pengiriman` menyimpan `nama_barang`, `satuan`, (dan `harga_satuan`); `penjualan` menyimpan `nama_pelanggan`.

## Consequences
Denormalisasi terkontrol (diterima pada 3NF karena sifatnya historis). Laporan lama tetap akurat.

## Alternatives Considered
- Join ke master saja (ditolak: riwayat berubah).
