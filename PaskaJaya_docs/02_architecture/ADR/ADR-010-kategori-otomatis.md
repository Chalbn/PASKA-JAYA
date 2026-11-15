# ADR-010: Kategori barang otomatis

**Status**: Accepted · **Tanggal**: 14 November 2026 · **Diputuskan oleh**: Kelompok 7 (PASKA JAYA)

## Context
Catatan PDF: dropdown kategori di Master Item dibuat otomatis sesuai kelompok produk dan bertambah saat produk baru ditambahkan.

## Decision
Form barang memakai combobox kategori: pilih yang ada atau ketik baru → `kategori_barang` dibuat otomatis (slug unik). Filter menampilkan hanya kategori yang punya barang aktif.

## Consequences
Tidak ada CRUD kategori terpisah di v1. Kategori kosong tidak muncul di filter.

## Alternatives Considered
- Daftar kategori statis (ditolak: bertentangan dengan catatan PDF).
