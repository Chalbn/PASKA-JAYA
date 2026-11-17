# PASKA JAYA — Urutan Kerja Tim (Estafet)

> Cara membaca: kerjakan **berurutan**. Tiap langkah punya: **Apa** · **Bergantung pada** · **File utama** · **Selesai bila**. Nomor langkah = urutan prompt di `05_prompts/`.

## 0. Sebelum mulai — WAJIB semua orang
1. Baca `PRD_PASKAJAYA.md`, `DATABASE_SCHEMA.md`, `GIT_WORKFLOW.md`, `AGENT_GUIDELINES.md`.
2. Clone repo, checkout branch jatah, jalankan `SETUP_GUIDE.md` sampai aplikasi hidup.
3. Aturan ketergantungan yang mengikat:
   - Langkah 2–3 (database) harus selesai sebelum fitur apa pun yang menyentuh data.
   - Langkah 6 (design system) harus ada sebelum UI panel lain (7–15).
   - Langkah 8 (Master Item) sebelum 9 (Barang Keluar) dan 11 (Bon); langkah 11 sebelum 12 (Pengantaran).

| # | Prompt | PIC | Bergantung pada |
|---|---|---|---|
| 1 | PROMPT_SETUP_PROJECT | Vascha | – |
| 2 | PROMPT_MIGRATIONS | Vascha, Patricia | 1 |
| 3 | PROMPT_DATABASE_ROUTINES | Vascha | 2 |
| 4 | PROMPT_MODELS | Patricia | 2 |
| 5 | PROMPT_LOGO | Jeli | – |
| 6 | PROMPT_FRONTEND | Jeli | 1, 5 |
| 7 | PROMPT_AUTH | Vascha | 3, 4, 6 |
| 8 | PROMPT_INVENTORY | Patricia | 3, 4, 6, 7 |
| 9 | PROMPT_KASIR_PENJUALAN | Jeli | 8 |
| 10 | PROMPT_KASIR_SALDO | Patricia | 9 |
| 11 | PROMPT_BON | Jona | 8, 10 |
| 12 | PROMPT_KARYAWAN_PANEL | Chyntia | 11 |
| 13 | PROMPT_OPERASIONAL | Chyntia, Jona | 7 |
| 14 | PROMPT_OWNER_PANEL | Jeli | 9, 10, 11 |
| 15 | PROMPT_ADMIN_PANEL | Vascha | 7 |
| 16 | PROMPT_QA_DEPLOY | Jona + semua | semua |
| – | PROMPT_MASTER | satu kali jalan (alternatif) | – |

## LANGKAH 1 — Vascha — Setup project
- **Apa**: Laravel 13, Livewire 3, Tailwind, Vite, Pest, Pint; repo private; branch protection.
- **Bergantung pada**: –
- **File utama**: `composer.json`, `package.json`, `vite.config.js`, `.env.example`
- **Selesai bila**: halaman Livewire "Hello" tampil; `php artisan test` & `pint` hijau.
- **Branch**: `feat/setup-project`

## LANGKAH 2 — Vascha & Patricia — Migrations (22 tabel)
- **Apa**: semua tabel sesuai `DATABASE_SCHEMA.md` urut FK; `RoleSeeder` & `ConfigSeeder` (data referensi saja).
- **Bergantung pada**: Langkah 1
- **File utama**: `database/migrations/*`
- **Selesai bila**: `migrate:fresh` & rollback sukses; database kosong (tanpa dummy).
- **Branch**: `feat/database-schema`

## LANGKAH 3 — Vascha — View, trigger, procedure, privilege
- **Apa**: 3 view, T1–T8, P1–P5 lewat migration; skrip privilege.
- **Bergantung pada**: Langkah 2
- **File utama**: `database/migrations/*_buat_view_*`, `*_buat_trigger_*`, `*_buat_procedure_*`; `07_extras/database_sql/*`
- **Selesai bila**: test `tests/Database` hijau (stok negatif ditolak, status bon otomatis, kirim/batal bon, reset laci).
- **Branch**: `feat/database-routines`

## LANGKAH 4 — Patricia & Jona — Model, enum, factory (test), command admin
- **Apa**: 22 model + relasi + scope; `app:buat-admin`.
- **Bergantung pada**: Langkah 2
- **Selesai bila**: relasi diuji; `app:buat-admin` membuat 1 admin.
- **Branch**: `feat/database-schema` (lanjutan)

## LANGKAH 5 — Jeli — Logo & brand
- **Apa**: ikuti `PROMPT_LOGO.md`; hasil `logo.svg`, `logo-mark.svg`, `favicon`; foto toko ke `public/images/foto-toko.jpg`.
- **Selesai bila**: logo konsisten dengan referensi "PJ / PASKA JAYA / TOKO BAHAN BANGUNAN".
- **Branch**: `feat/design-system-layout`

## LANGKAH 6 — Jeli & Chyntia — Design system & layout
- **Apa**: komponen `x-ui.*`, layout app per role, layout auth, menu per role.
- **Bergantung pada**: Langkah 1, 5
- **Selesai bila**: galeri komponen `/dev/ui` tampil sesuai PDF; responsif.

## LANGKAH 7 — Vascha (+Chyntia) — Auth & Profil
- **Apa**: login (email/HP), register, OTP, lupa password, middleware, profil.
- **Bergantung pada**: Langkah 3, 4, 6
- **Selesai bila**: foto toko baru tampil di Login/Register; tanpa demo account; 403 lintas role.
- **Branch**: `feat/auth`, `feat/profile`

## LANGKAH 8 — Patricia (+Jeli) — Master Item
- **Selesai bila**: kategori otomatis, restock benar, kartu/tabel, hapus.
- **Branch**: `feat/master-item`

## LANGKAH 9 — Patricia (+Chyntia) — Beranda Kasir & Barang Keluar
- **Selesai bila**: stok turun, kas laci naik, kode TRX berurutan. **Branch**: `feat/kasir-sales`

## LANGKAH 10 — Patricia (+Chyntia) — Saldo, Grafik, Riwayat
- **Selesai bila**: reset otomatis/manual tercatat; ekspor Excel/PDF; grafik 3 rentang. **Branch**: `feat/kasir-balance`

## LANGKAH 11 — Jona (+Chyntia) — Bon (Kasir & Owner)
- **Selesai bila**: alur buat → kirim → bayar sebagian → lunas konsisten; Owner read-only. **Branch**: `feat/bon-kasir`, `feat/bon-owner-readonly`

## LANGKAH 12 — Chyntia (+Jona) — Pengantaran Karyawan
- **Selesai bila**: foto bukti wajib & privat; status sinkron ke Kasir/Owner. **Branch**: `feat/delivery-karyawan`

## LANGKAH 13 — Chyntia & Jona — Bahan Harian & Kehadiran
- **Branch**: `feat/daily-materials`, `feat/attendance`

## LANGKAH 14 — Patricia (+Jeli) — Panel Owner
- **Selesai bila**: grafik 7 hari berisi data nyata; laporan + CSV. **Branch**: `feat/owner-dashboard`, `feat/owner-sales-report`

## LANGKAH 15 — Vascha (+Jeli) — Panel Admin & Tim
- **Branch**: `feat/admin-panel`, `feat/admin-team`

## LANGKAH 16 — Jona + semua — QA, UAT, deploy
- **Apa**: checklist E2E, uji hak akses, uji database kosong, UAT mitra, perbaikan, deploy, dokumentasi akhir.
- **Branch**: `feat/qa-polish` → `release/1.0.0` → `main`

## Yang boleh jalan BERSAMAAN
| Gelombang | Boleh paralel | Catatan |
|---|---|---|
| A | Langkah 2–3 (Vascha) ∥ Langkah 5 (Jeli) | logo tidak bergantung DB |
| B | Langkah 6 (UI) ∥ Langkah 4 (model) | |
| C | Langkah 8 (Patricia) ∥ Langkah 13 (Chyntia/Jona) | setelah Auth |
| D | Langkah 14 ∥ Langkah 15 | setelah 9–11 |
Sepanjang proyek: Vascha me-review setiap PR perubahan DB; Jona menjalankan QA tiap sprint.
