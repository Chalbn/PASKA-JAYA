# PROMPT: Setup Project

| Atribut | Isi |
|---|---|
| PIC | Vascha |
| Branch | `feat/setup-project` |
| Bergantung pada | – |
| Dokumen terkait | `01_guides/SETUP_GUIDE.md`, `02_architecture/TECH_STACK.md`, `FOLDER_STRUCTURE.md` |

## PROMPT
````
Kamu adalah senior Laravel developer. Proyek: **PASKA JAYA** — sistem manajemen Toko Bahan Bangunan (Laravel 11 + Livewire 3 + Tailwind CSS + MySQL 8, dev di Laragon).
BACA DULU: `PRD_PASKAJAYA.md`, `02_architecture/DATABASE_SCHEMA.md` + `ADR/`, fitur terkait di `03_features/`, `01_guides/AGENT_GUIDELINES.md`, dan PDF desain `07_extras/referensi_desain/uiux_paska_jaya.pdf`.
ATURAN KERAS:
- Database dimulai KOSONG. JANGAN membuat seeder/data dummy. Nama & angka di PDF hanya contoh tampilan; semua data diambil dari database.
- Nama tabel/kolom persis `DATABASE_SCHEMA.md`. Jangan ubah migration yang sudah merge.
- Logika kritis stok/kas/status bon mengikuti trigger & stored procedure (ADR-007).
- Semua halaman wajib punya empty state; kartu/grafik bernilai 0 saat data kosong.
- Otorisasi di server (middleware `role:`, Policy), bukan hanya menyembunyikan menu.
- UI hanya Tailwind + komponen `x-ui.*`; teks Bahasa Indonesia sesuai PDF; uang `Rp 1.820.000`; zona Asia/Jakarta.
- Kerja di branch jatah, commit Conventional Commits, PR ke `develop`. Setelah selesai update `04_plans/TASK_DISTRIBUTION.md` dan `06_reports/PROGRESS_TRACKER.md`.
FORMAT LAPORAN AKHIR: ✅ Selesai · 📁 File · 🧪 Test · ⚠️ Asumsi · ➡️ Berikutnya.

## TUGAS: Setup Project
1. Buat proyek Laravel 11 (PHP 8.3). Install `livewire/livewire:^3`, `maatwebsite/excel`, `barryvdh/laravel-dompdf`; dev: Pest + plugin Laravel, Pint.
2. Frontend: Tailwind CSS + Vite; `chart.js`; `@fontsource/plus-jakarta-sans` (font lokal). Token warna di `resources/css/app.css` sesuai `UI_UX_GUIDELINES.md`.
3. Konfigurasi `.env.example` sesuai `TECH_STACK.md`: `APP_NAME="PASKA JAYA"`, `APP_TIMEZONE=Asia/Jakarta`, `APP_LOCALE=id`, `AUTH_OTP_ENABLED=false`, DB `paska_jaya`.
4. Buat struktur folder persis `FOLDER_STRUCTURE.md` (folder kosong + `.gitkeep`). Alias middleware `role` dan `aktif` di `bootstrap/app.php` (kelas kosong dulu).
5. Helper global: `rupiah(int $n)` → `Rp 1.820.000`, `tanggal_id($date)` → `14 November 2026`, `inisial($nama)`.
6. Layout minimal + 1 komponen Livewire "Hello" untuk verifikasi.
7. Pest terkonfigurasi memakai MySQL `paska_jaya_test` (trigger/procedure tidak jalan di SQLite).
8. `.gitignore`: `.env`, `vendor`, `node_modules`, `storage/app/private`, `*.sql` dump.
## ACCEPTANCE
`php artisan serve` + `npm run dev` menampilkan komponen Livewire; `php artisan test` dan `./vendor/bin/pint --test` hijau; tidak ada package di luar daftar.
````
