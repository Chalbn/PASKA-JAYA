# PASKA JAYA — Progress Tracker

> **Last updated**: 17 November 2026 · PIC pembaruan: Jeli Alvonzo Laia (UI/UX Designer & Frontend)

## Summary
| Modul | PIC | Progress |
|---|---|---|
| Dokumentasi & desain (P0) | Tim | 100% |
| Setup & Database (P1) | Vascha, Patricia | 30% |
| Fondasi UI & Auth (P2) | Jeli, Vascha, Chyntia | 70% |
| Inventori & Penjualan (P3) | Patricia, Jeli, Chyntia | 0% |
| Bon & Pengiriman (P4) | Jona, Chyntia | 0% |
| Operasional & Panel (P5) | Chyntia, Jona, Patricia, Vascha, Jeli | 0% |
| QA & Rilis (P6) | Jona + semua | 0% |
| **OVERALL** | | **±25%** |

## Phase Checklist
### P1 Setup & Database
- [ ] Setup project (PROMPT_SETUP_PROJECT)
- [ ] 22 migration (PROMPT_MIGRATIONS)
- [ ] View, trigger T1–T8, procedure P1–P5, privilege (PROMPT_DATABASE_ROUTINES)
- [ ] Model, enum, command `app:buat-admin` (PROMPT_MODELS)
### P2 Fondasi UI & Auth
- [x] Logo & brand + foto toko (PROMPT_LOGO)
- [x] Design system & layout (PROMPT_FRONTEND)
- [ ] Auth + Profil (PROMPT_AUTH)
### P3 Inventori & Penjualan
- [ ] Master Item (PROMPT_INVENTORY)
- [ ] Beranda Kasir & Barang Keluar (PROMPT_KASIR_PENJUALAN)
- [ ] Saldo, Grafik, Riwayat (PROMPT_KASIR_SALDO)
### P4 Bon & Pengiriman
- [ ] Bon Kasir & Owner (PROMPT_BON)
- [ ] Pengantaran Karyawan (PROMPT_KARYAWAN_PANEL)
### P5 Operasional & Panel
- [ ] Bahan Harian & Kehadiran (PROMPT_OPERASIONAL)
- [ ] Panel Owner (PROMPT_OWNER_PANEL)
- [ ] Panel Admin & Tim (PROMPT_ADMIN_PANEL)
### P6 QA & Rilis
- [ ] QA, UAT, deploy (PROMPT_QA_DEPLOY)

## Checklist E2E (mulai dari database kosong)
0. Semua halaman render tanpa error pada database kosong + empty state
1. `app:buat-admin` → login admin; admin membuat akun Owner, Kasir, Karyawan
2. Register publik → login (tanpa demo account); foto toko tampil di Login & Register
3. Kasir tambah barang (kategori baru) → kategori muncul di filter
4. Barang Keluar cash 2 item → stok turun, saldo cash naik, kode TRX berurutan
5. Stok kurang ditolak tanpa mengubah data
6. Catat Bon → Daftar Bon (Belum Dikirim, stok belum berubah)
7. Kirim ke Karyawan → stok turun; karyawan melihat tugas + badge Baru
8. Karyawan: Sedang Disiapkan → Siap Berangkat → Selesai (foto wajib)
9. Kasir Bayar Sebagian → Tandai Lunas → saldo laci bertambah; status otomatis Lunas
10. Batalkan pengiriman (belum selesai) → stok kembali
11. Owner buka Daftar Bon → detail + timeline + foto; aksi tulis = 403
12. Owner: grafik 7 hari terisi dari transaksi nyata; Laporan filter + CSV
13. Reset laci manual & otomatis tercatat di Riwayat Saldo Laci; ekspor Excel/PDF
14. Kehadiran: Kasir CRUD; Owner read-only
15. Admin ubah role / nonaktifkan user → tidak bisa login; admin terakhir tidak bisa dinonaktifkan
16. activity_logs tercatat dan tidak bisa diubah/hapus
17. Foto bukti pengiriman tidak bisa dibuka tanpa login/izin

## Session Log
### Session 2 — 17 November 2026 (Jeli + Agent) — Design System & Layout (feat/design-system-layout)
- Dikerjakan: Implementasi Design System & Layout sesuai palet Industrial Precision Commerce dan PDF desain UI/UX.
- Token warna Tailwind CSS (@theme) di `resources/css/app.css` (Police Blue `#2E4365`, Marigold `#E59D2C`, Citrine Brown `#8A3B08`, Pearl Chip `#EBDDC5`, Surface, Emerald, Red).
- Font Plus Jakarta Sans lokal via Bunny font + utility `tabular-nums` untuk nominal Rupiah & angka stok.
- Helper global di `app/helpers.php`: `rupiah()`, `tanggal_id()`, `inisial()`.
- Konfigurasi menu 4 peran di `config/menu.php` (Admin, Owner, Kasir, Karyawan + badge pengantaran).
- Komponen brand `x-brand.logo` (varian default & white) dan `x-brand.mark`.
- Seluruh komponen Blade `x-ui.*`: `button`, `card`, `stat-card`, `badge`, `modal`, `confirm`, `input`, `select`, `date`, `table`, `pill-filter`, `toggle`, `timeline`, `empty-state`, `upload`, `toast`, `spinner`.
- Layout `x-layouts.app` dengan gaya modern **Liquid Glass & Floating Sidebar** (`backdrop-blur-xl`, border tipis bercahaya, shadow lembut) serta fitur **Collapsible Icon-Rail** (mode terbuka `w-[260px]` vs ciut `w-[76px]` dengan tombol bulat mengambang toggle `<` / `>`) & `x-layouts.auth` (split-screen + foto toko + headline resmi + subteks, tanpa demo account).
- Pembaruan `x-ui.stat-card` ke gaya **Bento Fintech Modern**: tanpa border tebal kaku, angka 1 baris utuh `whitespace-nowrap tabular-nums tracking-tight`, dan varian warna paling lembut (`primary`, `marigold`, `pearl`, `danger`).
- Helper Chart.js & Alpine.js di `resources/js/chart-helper.js` & `resources/js/app.js` (bar chart 7 hari & stacked bar kas mingguan, tooltip Rupiah, data kosong tetap menggambar sumbu 0).
- Halaman galeri `/dev/ui` dan pratinjau auth `/dev/auth` (aktif pada `APP_ENV=local`).
- Next: Implementasi Auth & Profil (PROMPT_AUTH).

### Session 1 — 14 November 2026 (Claude + Kelompok 7) — Dokumentasi
- Dibuat: dokumentasi lengkap (PRD, 01–07), 22 tabel + objek DB, 13 ADR, 16 prompt + master + logo.
- Disesuaikan dengan laporan MSBD (Livewire, 14 entitas) dan contoh struktur dari tim.
- Next: Vascha mulai PROMPT_SETUP_PROJECT.
