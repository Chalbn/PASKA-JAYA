# PASKA JAYA — Indeks Dokumentasi

Sistem manajemen **Toko Bahan Bangunan PASKA JAYA**: stok, kasir, bon pelanggan, pengantaran, bahan harian, dan kehadiran karyawan dalam satu aplikasi web.

Sumber kebenaran desain: `07_extras/referensi_desain/uiux_paska_jaya.pdf` (+ screenshot desain dari tim).

## Cara pakai paket ini
1. Baca `PRD_PaskaJaya.md` (apa yang dibangun).
2. Baca `01_guides/` dan `02_architecture/` (aturan & arsitektur).
3. Jalankan prompt di `05_prompts/` **berurutan** (lihat `04_plans/URUTAN_KERJA.md`), atau pakai `05_prompts/00_PROMPT_MASTER.md` untuk satu kali jalan.
4. Cek tiap fitur terhadap `03_features/Fxx_*.md` (acceptance criteria).
5. Catat progres di `06_reports/PROGRESS_TRACKER.md`.

## Peta folder
| Folder | Isi |
|---|---|
| `01_guides` | Panduan agent, coding standard, git, glossary, security, setup, testing, UI/UX |
| `02_architecture` | Tech stack, struktur folder, skema DB, rute, ADR |
| `03_features` | 14 spesifikasi fitur (F01–F14) per role |
| `04_plans` | Roadmap, sprint, distribusi tugas, urutan kerja, mapping layar |
| `05_prompts` | Prompt siap pakai untuk AI coding agent |
| `06_reports` | Progress tracker & laporan |
| `07_extras` | Data demo, ERD, desain referensi (PDF) |

## Role
`admin` · `owner` · `kasir` · `karyawan`
