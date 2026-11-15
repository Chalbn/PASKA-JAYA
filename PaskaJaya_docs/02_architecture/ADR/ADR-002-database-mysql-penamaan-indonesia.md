# ADR-002: Database MySQL, penamaan Indonesia, 14 entitas laporan + 8 pendukung

**Status**: Accepted · **Tanggal**: 14 November 2026 · **Diputuskan oleh**: Kelompok 7 (PASKA JAYA)

## Context
Laporan mendefinisikan 14 entitas (roles … absensi_karyawan). Layar PDF membutuhkan data tambahan (timeline status, kas laci, riwayat reset, mutasi stok, cicilan bon).

## Decision
Pakai nama tabel/kolom Indonesia persis seperti laporan untuk 14 entitas inti; tambah 8 tabel pendukung (lihat DATABASE_SCHEMA.md). Nama tabel bawaan Laravel tetap Inggris.

## Consequences
Konsistensi dengan laporan akademik. Model Eloquent perlu `$table` eksplisit bila nama tidak sesuai konvensi plural Inggris.

## Alternatives Considered
- Nama Inggris (ditolak: menyimpang dari laporan).
