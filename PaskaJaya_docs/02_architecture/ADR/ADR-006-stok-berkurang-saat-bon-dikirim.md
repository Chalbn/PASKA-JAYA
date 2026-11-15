# ADR-006: Stok bon berkurang saat dikirim; pengiriman entitas sendiri

**Status**: Accepted · **Tanggal**: 14 November 2026 · **Diputuskan oleh**: Kelompok 7 (PASKA JAYA)

## Context
Bon dapat dibuat lebih dulu daripada barang keluar gudang; laporan mendefinisikan `pengiriman_barang` + `detail_pengiriman`.

## Decision
Penjualan langsung memotong stok saat disimpan (trigger T2). Bon memotong stok saat **Kirim ke Karyawan** (`sp_kirim_bon`) dan mengembalikannya saat **Batalkan Pengiriman** (`sp_batalkan_pengiriman`). Satu pengiriman aktif per bon (kolom virtual `aktif_key` UNIQUE).

## Consequences
Stok mencerminkan barang yang benar-benar keluar. Setiap perubahan tercatat di `mutasi_stok`.

## Alternatives Considered
- Potong stok saat bon dibuat (ditolak: stok palsu bila bon batal).
