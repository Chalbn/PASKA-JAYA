# ADR-007: Logika kritis di database: trigger, stored procedure, view

**Status**: Accepted · **Tanggal**: 14 November 2026 · **Diputuskan oleh**: Kelompok 7 (PASKA JAYA)

## Context
Tujuan laporan: menerapkan triggers, stored procedures, dan manajemen privilege; stok/uang harus konsisten walau ada bug di aplikasi.

## Decision
Integritas dijaga di MySQL: 8 trigger (T1–T8), 5 stored procedure, 3 view (`07_extras/database_sql/`). Laravel memanggil procedure via `DB::select('CALL …')` dalam service tipis. Semua dipasang lewat migration.

## Consequences
Konsistensi tinggi, tetapi logika terbagi dua tempat; wajib ada test database (`tests/Database`). Perubahan trigger = migration baru.

## Alternatives Considered
- Semua logika di PHP (ditolak: tidak memenuhi tujuan akademik & rawan inkonsistensi).
