# CHANGELOG

## [0.2.0] — 2026-11-14
### Changed
- **Stack** diselaraskan dengan laporan MSBD: Laravel + **Livewire** (sebelumnya React/Inertia) — ADR-001.
- **Database** mengikuti 14 entitas laporan (nama Indonesia) + 8 tabel pendukung = 22 tabel — ADR-002, ADR-005.
- `TASK_DISTRIBUTION.md` memakai nama & peran Kelompok 7.
- Foto toko pada Login/Register diganti foto toko Paska Jaya.
### Added
- Objek database: 3 view, 8 trigger, 5 stored procedure, skrip privilege (`07_extras/database_sql/`) — ADR-007, ADR-013.
- ADR-001…013; `PROMPT_LOGO.md`; `PROMPT_DATABASE_ROUTINES.md`; `07_extras/aset/`; `07_extras/referensi_laporan/`.
- Struktur dokumentasi mengikuti contoh (00_INDEX, PRD, 01–07).
### Removed
- Data demo (`Demo_data`), seeder demo, template CSV, prompt impor CSV (data dari database asli) — ADR-012.

## [0.1.0] — 2026-11-14
- Dokumentasi awal dari PDF UI/UX (4 role, 33 layar).
