# CHANGELOG

## [0.3.0] — 2026-11-18
### Changed
- Objek database kini hanya berada di file migration (`database/migrations/2026_11_16_081337_create_functions.php`, `2026_11_16_081958_create_views.php`, `2026_11_16_083330_create_triggers.php`, `2026_11_16_083846_create_procedures.php`): **9 function, 19 view, 19 trigger, dan 13 stored procedure**.
- Lokasi skrip privilege MySQL dipindahkan dan diperbarui ke `database/privileges.sql`.
### Removed
- Folder `07_extras/database_sql/` (`01_views.sql`, `02_triggers.sql`, `03_stored_procedures.sql`, `04_privileges.sql`) dihapus karena seluruh definisi telah berada di `database/migrations/` dan `database/privileges.sql`.

## [0.2.0] — 2026-11-14
### Changed
- **Stack** diselaraskan dengan laporan MSBD: Laravel + **Livewire** (sebelumnya React/Inertia) — ADR-001.
- **Database** mengikuti 14 entitas laporan (nama Indonesia) + 8 tabel pendukung = 22 tabel — ADR-002, ADR-005.
- `TASK_DISTRIBUTION.md` memakai nama & peran Kelompok 7.
- Foto toko pada Login/Register diganti foto toko Paska Jaya.
### Added
- Objek database: view, trigger, stored procedure (`database/migrations/`) dan skrip privilege (`database/privileges.sql`) — ADR-007, ADR-013.
- ADR-001…013; `PROMPT_LOGO.md`; `PROMPT_DATABASE_ROUTINES.md`; `07_extras/aset/`; `07_extras/referensi_laporan/`.
- Struktur dokumentasi mengikuti contoh (00_INDEX, PRD, 01–07).
### Removed
- Data demo (`Demo_data`), seeder demo, template CSV, prompt impor CSV (data dari database asli) — ADR-012.

## [0.1.0] — 2026-11-14
- Dokumentasi awal dari PDF UI/UX (4 role, 33 layar).
