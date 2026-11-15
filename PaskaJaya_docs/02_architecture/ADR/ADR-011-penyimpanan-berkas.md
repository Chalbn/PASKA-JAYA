# ADR-011: Penyimpanan berkas: bukti pengiriman privat

**Status**: Accepted · **Tanggal**: 14 November 2026 · **Diputuskan oleh**: Kelompok 7 (PASKA JAYA)

## Context
Foto bukti pengantaran sensitif (lokasi/alamat pelanggan); foto barang/profil tidak sensitif.

## Decision
`foto_bukti` disimpan di disk **privat** (`storage/app/private/bukti-pengiriman`) dan disajikan lewat route yang mengecek Policy. Foto barang, profil, tim di disk `public`. Aset brand statis (logo, foto toko) di `public/images/`. Validasi jpg/png/webp, maks 4 MB, nama acak.

## Consequences
Akses bukti terkontrol. Perlu route unduh & `storage:link` untuk disk public.

## Alternatives Considered
- Semua di public (ditolak: URL bukti dapat ditebak/dibagikan).
