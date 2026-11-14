# ADR-005 Saldo laci berbasis drawer_entries

**Status**: Accepted · 2026-11-14

## Konteks
Saldo harus bisa direset & diaudit per hari.

## Keputusan
Setiap uang masuk = baris drawer_entries; reset membuat drawer_closings.

## Konsekuensi
Detail riwayat per hari tinggal query.
