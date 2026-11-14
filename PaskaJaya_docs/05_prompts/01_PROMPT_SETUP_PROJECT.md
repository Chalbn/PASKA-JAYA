# PROMPT 01 — Setup Project

> **Konteks wajib**: Proyek PASKA JAYA (toko bahan bangunan). Stack Laravel 11 + Inertia + React/TS + Tailwind (lihat `02_architecture/TECH_STACK.md`). Ikuti `01_guides/*`, desain PDF `07_extras/referensi_desain/uiux_paska_jaya.pdf`, dan spesifikasi fitur di `03_features/`. UI berbahasa Indonesia. Uang integer rupiah. Selesai → jalankan test, update `06_reports/PROGRESS_TRACKER.md`, commit.

## Tugas
1. Buat proyek Laravel + Inertia + React TS + Tailwind + Vite sesuai `01_guides/SETUP_GUIDE.md`.
2. Konfigurasi: `APP_NAME="PASKA JAYA"`, timezone `Asia/Jakarta`, locale `id`, Pint, ESLint/Prettier, Pest.
3. Tailwind: font Plus Jakarta Sans, token warna (primary navy/biru, cyan muda, kuning, hijau, merah) di `tailwind.config`.
4. Buat struktur folder sesuai `02_architecture/FOLDER_STRUCTURE.md` (folder kosong + `.gitkeep`).
5. Middleware Inertia: share `auth.user` (id, name, role, avatar, initials), `flash`, `today` (tanggal format Indonesia).
6. `lib/format.ts`: `formatRupiah(n)` → `Rp 1.820.000`, `formatTanggal`, `formatWaktu`, `inisial(name)`.
## Acceptance
`php artisan serve` + `npm run dev` menampilkan halaman Inertia "Hello"; `composer test` & `npm run lint` hijau.
