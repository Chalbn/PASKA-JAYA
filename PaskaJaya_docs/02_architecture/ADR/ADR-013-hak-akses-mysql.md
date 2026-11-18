# ADR-013: Hak akses MySQL berlapis (least privilege)

**Status**: Accepted · **Tanggal**: 14 November 2026 · **Diputuskan oleh**: Kelompok 7 (PASKA JAYA)

## Context
Laporan menyebut manajemen hak akses (privilege) sebagai tujuan akademik dan keamanan.

## Decision
Empat akun MySQL: `pj_app` (CRUD + EXECUTE, tanpa DDL), `pj_migrator` (DDL, deploy), `pj_report` (SELECT view & EXECUTE procedure pelaporan), `pj_backup` (mysqldump). Skrip: `database/privileges.sql`.

## Consequences
Deploy memakai kredensial migrasi terpisah. Password disimpan di `.env` server, bukan repo.

## Alternatives Considered
- Satu akun root/app (ditolak: berisiko).
