# PASKA JAYA — Progress Tracker

> **Last updated**: 22 November 2026

## Summary
| Modul | PIC | Progress |
|---|---|---|
| Dokumentasi & desain (P0) | Tim | 100% |
| Setup & Database (P1) | Vascha, Patricia | 100% |
| Fondasi UI & Auth (P2) | Jeli, Vascha, Chyntia | 100% |
| Inventori & Penjualan (P3) | Patricia, Jeli, Chyntia | 100% |
| Bon & Pengiriman (P4) | Jona, Chyntia | 100% |
| Operasional & Panel (P5) | Chyntia, Jona, Patricia, Vascha, Jeli | 100% |
| QA & Persiapan Rilis (P6) | Jona + semua | 95% (tinggal deploy ke hosting) |
| **OVERALL** | | **±99%** |

## Phase Checklist
### P1 Setup & Database
- [x] Setup project Laravel & Vite/Tailwind
- [x] Migration seluruh tabel inti & notifikasi
- [x] Function (9), View (19), Trigger (19), Stored Procedure (13), dan hak akses MySQL (`pj_app`, `pj_report`, `pj_backup`)
- [x] Model, Enum, Service, dan Seeder data toko (`PaskaJayaDataSeeder`)

### P2 Fondasi UI & Auth
- [x] Logo, brand, dan foto toko
- [x] Design system Biru Royal, sidebar anti-lompat, dan topbar responsif
- [x] Auth (Login Email/HP, Register, OTP Email SMTP, Lupa Password, Login dengan Google OAuth) + Profil

### P3 Inventori & Penjualan
- [x] Master Item (Kartu 5 kolom & Tabel, restock, filter kategori, 36 foto lokal, tombol reset pencarian)
- [x] Beranda Kasir & Barang Keluar (POS + cetak Nota Penjualan PDF)
- [x] Saldo Laci, Grafik Mingguan, dan Riwayat Saldo Laci (Export Excel & PDF + reset otomatis/manual)

### P4 Bon & Pengiriman
- [x] Catat Bon & Daftar Bon Kasir + cetak Faktur Bon PDF
- [x] Panel Pengantaran Karyawan (update status & wajib foto bukti pengiriman)

### P5 Operasional & Panel
- [x] Bahan Harian & Kehadiran Karyawan
- [x] Panel Owner (Ringkasan, Laporan Barang Keluar, Daftar Bon, Master Item, Kehadiran)
- [x] Panel Admin & Tim (Ringkasan Sistem + AJAX pagination log, Manajemen Pengguna, Tim + background foto bersama)

### P6 QA & Rilis
- [x] Automated Feature Tests (41 test / 255 assertions lulus) & halaman error custom (`403`, `404`, `500`)
- [ ] Upload ke server hosting & konfigurasi Cron Job production

## Session Log
### Session 1 — 14 November 2026 — Dokumentasi & Arsitektur
- Penyusunan PRD, rancangan database (tabel, view, trigger, function, procedure), ADR-001 s.d. ADR-013, dan spesifikasi fitur F01–F14.

### Session 2 — 18 November 2026 — Auth (F01), Profil (F10), & Admin Panel (F02–F04)
- Penyelesaian modul autentikasi, middleware role/status aktif, profil pengguna, dashboard admin, manajemen pengguna, halaman tim, serta perintah backup database (`backup:run`).

### Session 3 — 20 November 2026 — Refactor MVC Controller & MySQL Tunggal
- Migrasi penuh ke arsitektur MVC Controller + Blade + Alpine.js dengan database tunggal MySQL `paska_jaya`.
- Aktivasi OTP email SMTP Gmail dan integrasi **Login dengan Google** (`laravel/socialite`).
- Penyesuaian hak akses least-privilege MySQL (`pj_app`, `pj_report`, `pj_backup`) dan verifikasi 41/41 test lulus.

### Session 4 — 22 November 2026 — Polishing UI, PDF, Error Pages, & Seeder Data Toko
- Perbaikan topbar responsif saat zoom, sidebar anti-lompat, dan pagination AJAX log aktivitas.
- Penambahan halaman error custom bertema Biru Royal (`403`, `404`, `500`).
- Penambahan cetak **Nota Barang Keluar PDF** dan **Faktur Bon PDF** (`barryvdh/laravel-dompdf`).
- Penyelesaian seeder data toko (`PaskaJayaDataSeeder`), 36 foto barang di `public/images/barang/`, serta tombol reset filter Master Item.
