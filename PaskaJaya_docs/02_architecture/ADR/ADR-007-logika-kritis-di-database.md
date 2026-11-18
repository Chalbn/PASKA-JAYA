# ADR-007: Logika kritis di database: trigger, stored procedure, view

**Status**: Accepted · **Tanggal**: 14 November 2026 · **Diputuskan oleh**: Kelompok 7 (PASKA JAYA)

## Context
Tujuan laporan: menerapkan triggers, stored procedures, dan manajemen privilege; stok/uang harus konsisten walau ada bug di aplikasi.

## Decision
Integritas dijaga di MySQL: trigger, stored procedure, function, dan view didefinisikan langsung di migration (`database/migrations/2026_11_16_081337_create_functions.php`, `database/migrations/2026_11_16_081958_create_views.php`, `database/migrations/2026_11_16_083330_create_triggers.php`, `database/migrations/2026_11_16_083846_create_procedures.php`). Laravel memanggil procedure via `DB::select('CALL …')` dalam service tipis. Semua dipasang lewat migration.

## Consequences
Konsistensi tinggi, tetapi logika terbagi dua tempat; wajib ada test database (`tests/Database`). Perubahan trigger = migration baru.

## Alternatives Considered
- Semua logika di PHP (ditolak: tidak memenuhi tujuan akademik & rawan inkonsistensi).
