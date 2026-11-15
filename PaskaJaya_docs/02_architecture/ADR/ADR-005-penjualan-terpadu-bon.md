# ADR-005: Penjualan terpadu: tipe langsung & bon, bon_piutang 1:1

**Status**: Accepted · **Tanggal**: 14 November 2026 · **Diputuskan oleh**: Kelompok 7 (PASKA JAYA)

## Context
Laporan mendefinisikan `bon_piutang` sebagai catatan penjualan kredit. Barang keluar tunai dan bon memakai rincian item yang sama.

## Decision
`penjualan.tipe` = `langsung` (Barang Keluar, lunas cash/transfer) atau `bon` (kredit). Tipe bon memiliki tepat 1 baris `bon_piutang`. Laporan Barang Keluar Owner hanya `tipe='langsung'`; piutang dilihat di Daftar Bon.

## Consequences
Satu pola item untuk dua alur. Pembayaran bon dicatat di `pembayaran_bon` dan masuk kas laci.

## Alternatives Considered
- Dua tabel item terpisah (ditolak: duplikasi).
