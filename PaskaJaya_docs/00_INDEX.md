# PASKA JAYA — Indeks Dokumentasi

Sistem manajemen **Toko Bangunan "Paska Jaya"** berbasis web (Laravel + Blade + Alpine.js + MySQL): stok, kasir, bon/piutang, pengantaran, bahan harian, kehadiran, dan laporan dalam satu aplikasi. Disusun oleh **Kelompok 7** — Prodi Teknologi Informasi, FASILKOM-TI, Universitas Sumatera Utara (Mata kuliah Manajemen Sistem Basis Data).

## Struktur Folder Dokumentasi
```
PaskaJaya_docs/
├─ 00_INDEX.md
├─ CHANGELOG.md
├─ PRD_PASKAJAYA.md
├─ 01_guides/            CODING_STANDARDS · GIT_WORKFLOW · GLOSSARY · SECURITY_GUIDELINES
│                        SETUP_GUIDE · TESTING_STRATEGY · UI_UX_GUIDELINES
├─ 02_architecture/      API_ROUTES · DATABASE_SCHEMA · FOLDER_STRUCTURE · TECH_STACK
│  └─ ADR/               ADR-001 … ADR-013 (+ README)
├─ 03_features/          F01 … F14
├─ 04_plans/             MASTER_ROADMAP · SCREEN_MAPPING · SPRINT_PLAN · TASK_DISTRIBUTION
├─ 06_reports/           PROGRESS_TRACKER.md
└─ 07_extras/            referensi_desain/ · referensi_laporan/ · aset/ · TECH_STACK_EXTRAS.md
```
> Objek database (function, view, trigger, procedure) berada di `database/migrations/`.

## Urutan Membaca
1. `PRD_PASKAJAYA.md` → 2. `02_architecture/DATABASE_SCHEMA.md` + `ADR/` → 3. `03_features/Fxx` → 4. `06_reports/PROGRESS_TRACKER.md`.

## Peta Fitur
| Role | Fitur | Dokumen |
|---|---|---|
| Semua | Auth, Profil | F01, F10 |
| Admin | Ringkasan Sistem, Pengguna, Tim | F02–F04 |
| Owner | Ringkasan, Laporan, Bon (lihat), Master Item, Kehadiran (lihat) | F05–F09 |
| Kasir | Beranda, Barang Keluar, Saldo/Grafik/Riwayat, Catat Bon, Bahan Harian | F11–F13 (+F07–F09) |
| Karyawan | Pengantaran | F14 |

## Tim
Vascha U. Lumbantoruan · Jona Vebrian Gultom · Patricia Putri Josephine Situmeang · Jeli Alvonzo Laia · Chyntia R. A. Hutabarat — Dosen pengampu: Dedy Arisandi, S.T., M.Kom. Detail peran: `04_plans/TASK_DISTRIBUTION.md`.
