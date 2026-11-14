# ADR-004 Stok bon berkurang saat dikirim

**Status**: Accepted · 2026-11-14

## Konteks
Bon bisa dibuat tapi belum tentu jadi dikirim.

## Keputusan
Stok dipotong saat 'Kirim ke Karyawan'; 'Batalkan Pengiriman' mengembalikan stok.

## Konsekuensi
Perlu stock_movements untuk jejak audit.
