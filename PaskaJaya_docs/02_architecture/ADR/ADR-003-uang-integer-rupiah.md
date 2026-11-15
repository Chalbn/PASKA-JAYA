# ADR-003: Nominal uang disimpan sebagai integer rupiah

**Status**: Accepted · **Tanggal**: 14 November 2026 · **Diputuskan oleh**: Kelompok 7 (PASKA JAYA)

## Context
Float/decimal rawan selisih pembulatan; rupiah tidak memakai sen pada transaksi toko.

## Decision
Semua kolom uang `BIGINT UNSIGNED` (rupiah utuh). Format tampilan `Rp 1.820.000` lewat helper.

## Consequences
Perhitungan sederhana dan akurat. Tidak mendukung pecahan sen (di luar kebutuhan).

## Alternatives Considered
- DECIMAL(15,2) (ditolak: kompleksitas tanpa manfaat).
