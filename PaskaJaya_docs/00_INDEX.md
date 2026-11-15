# PASKA JAYA — Indeks Dokumentasi

Sistem manajemen **Toko Bangunan "Paska Jaya"** berbasis web (Laravel + Livewire + MySQL): stok, kasir, bon/piutang, pengantaran, bahan harian, kehadiran, dan laporan dalam satu aplikasi. Disusun oleh **Kelompok 7** — Prodi Teknologi Informasi, FASILKOM-TI, Universitas Sumatera Utara (Mata kuliah Manajemen Sistem Basis Data).

## Struktur Folder Dokumentasi
```
PaskaJaya_docs/
├─ 00_INDEX.md
├─ CHANGELOG.md
├─ PRD_PASKAJAYA.md
├─ 01_guides/            AGENT_GUIDELINES · CODING_STANDARDS · GIT_WORKFLOW · GLOSSARY
│                        SECURITY_GUIDELINES · SETUP_GUIDE · TESTING_STRATEGY · UI_UX_GUIDELINES
├─ 02_architecture/      API_ROUTES · DATABASE_SCHEMA · FOLDER_STRUCTURE · TECH_STACK
│  └─ ADR/               ADR-001 … ADR-013 (+ README)
├─ 03_features/          F01 … F14
├─ 04_plans/             MASTER_ROADMAP · SCREEN_MAPPING · SPRINT_PLAN · TASK_DISTRIBUTION · URUTAN_KERJA
├─ 05_prompts/           PROMPT_*.md (16 prompt bertahap + PROMPT_MASTER + PROMPT_LOGO)
├─ 06_reports/           PROGRESS_TRACKER.md
└─ 07_extras/            referensi_desain/ · referensi_laporan/ · aset/ · database_sql/ · TECH_STACK_EXTRAS.md
```
> `Demo_data` **ditiadakan**: semua data berasal dari database asli yang akan dibuat tim.

## Urutan Membaca
1. `PRD_PASKAJAYA.md` → 2. `02_architecture/DATABASE_SCHEMA.md` + `ADR/` → 3. `01_guides/AGENT_GUIDELINES.md` → 4. `03_features/Fxx` → 5. `04_plans/URUTAN_KERJA.md` → 6. `05_prompts/`.

## Peta Fitur
| Role | Fitur | Dokumen |
|---|---|---|
| Semua | Auth, Profil | F01, F10 |
| Admin | Ringkasan Sistem, Pengguna, Tim | F02–F04 |
| Owner | Ringkasan, Laporan, Bon (lihat), Master Item, Kehadiran (lihat) | F05–F09 |
| Kasir | Beranda, Barang Keluar, Saldo/Grafik/Riwayat, Catat Bon, Bahan Harian | F11–F13 (+F07–F09) |
| Karyawan | Pengantaran | F14 |

## Prompt (urutan eksekusi)
1 SETUP_PROJECT → 2 MIGRATIONS → 3 DATABASE_ROUTINES → 4 MODELS → 5 LOGO → 6 FRONTEND → 7 AUTH → 8 INVENTORY → 9 KASIR_PENJUALAN → 10 KASIR_SALDO → 11 BON → 12 KARYAWAN_PANEL → 13 OPERASIONAL → 14 OWNER_PANEL → 15 ADMIN_PANEL → 16 QA_DEPLOY. Satu kali jalan: `PROMPT_MASTER`.

## Aturan Penting
- **Tanpa data dummy**; database dimulai kosong; admin pertama via `php artisan app:buat-admin`.
- Desain PDF = kebenaran tampilan; laporan MSBD = kebenaran database & latar.
- Foto toko baru dipakai di Login & Register (`07_extras/aset/foto_toko.jpg`); logo dibuat dengan `PROMPT_LOGO.md`.
- Branch fitur sudah ditentukan (`GIT_WORKFLOW.md`); PR ke `develop`.

## Tim
Vascha U. Lumbantoruan · Jona Vebrian Gultom · Patricia Putri Josephine Situmeang · Jeli Alvonzo Laia · Chyntia R. A. Hutabarat — Dosen pengampu: Dedy Arisandi, S.T., M.Kom. Detail peran: `04_plans/TASK_DISTRIBUTION.md`.
