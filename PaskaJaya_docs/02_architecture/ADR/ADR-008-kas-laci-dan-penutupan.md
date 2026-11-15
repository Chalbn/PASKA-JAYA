# ADR-008: Saldo laci berbasis kas_laci + penutupan_laci

**Status**: Accepted · **Tanggal**: 14 November 2026 · **Diputuskan oleh**: Kelompok 7 (PASKA JAYA)

## Context
Saldo laci harus bisa direset otomatis 00:00 dan manual, dengan riwayat rinci per reset.

## Decision
Setiap uang masuk = 1 baris `kas_laci` (trigger T3/T5). Saldo aktif = baris dengan `penutupan_id IS NULL`. `sp_reset_laci` membuat `penutupan_laci` dan menautkan baris aktif. `direset_oleh NULL` = Sistem.

## Consequences
Riwayat & rincian reset tinggal query. Beberapa reset dalam sehari diizinkan.

## Alternatives Considered
- Hitung dari penjualan langsung (ditolak: tidak mencakup pembayaran bon).
