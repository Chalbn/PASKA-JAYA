# PASKA JAYA — Tech Stack Extras

## 1. Laragon (Lingkungan Lokal)
- Laragon Full: Apache/Nginx + PHP 8.3 + MySQL 8 + Composer + Node.
- Virtual host otomatis `paska-jaya.test` (Menu → Apache → Auto virtual hosts).
- HeidiSQL (bawaan) untuk melihat tabel, view, trigger, dan procedure.
- Aktifkan ekstensi PHP: `pdo_mysql`, `mbstring`, `gd`, `fileinfo`, `zip` (untuk Excel).

## 2. Livewire 3 — Interaksi Tanpa Reload
- Full-page component per halaman; `#[Url]` untuk filter, `WithPagination`, `WithFileUploads`, `#[Computed]`, `#[Locked]`.
- Cocok untuk POS (keranjang), pencarian live, modal, dan pembaruan stok.
- Alpine.js (bundled) untuk modal, dropdown, dan grafik.
- Testing: `Livewire::test(Komponen::class)`.

## 3. Chart.js
- Bar chart (7 hari) dan stacked bar (Cash + Transfer).
- Inisialisasi di Alpine; data dikirim `@js($data)`; tooltip format Rupiah.

## 4. Ekspor
- `maatwebsite/excel`: CSV (Laporan Barang Keluar), XLSX (Riwayat Saldo Laci).
- `barryvdh/laravel-dompdf`: PDF Riwayat Saldo Laci.

## 5. MySQL — Objek Lanjutan
- View, trigger, stored procedure, privilege: `07_extras/database_sql/`.
- Dipasang lewat migration (`DB::unprepared`) agar konsisten antar anggota tim.
- Test pada MySQL (bukan SQLite).

## 6. Backup
- `mysqldump` via akun `pj_backup`, dijadwalkan 02:00; hasil di `storage/app/private/backup/`; tulis `system_configs.backup_terakhir`.
- Uji restore berkala.

## 7. Aset Brand
- Logo: `PROMPT_LOGO.md` → `public/images/logo*.svg`, favicon.
- Foto toko: `07_extras/aset/foto_toko.jpg` → `public/images/foto-toko.jpg` (Login & Register).

## 8. Tools Pendukung
Git + GitHub (repo private), VS Code (Intelephense, Laravel Blade, Tailwind IntelliSense), Pest, Pint, Postman (opsional untuk unduhan), draw.io/dbdiagram (ERD, lihat Mermaid di DATABASE_SCHEMA).

## 9. Biaya
Rp 0 — seluruh perangkat lunak open source (sesuai laporan §2.2.2). Domain & hosting belum dibebankan.

## 10. CV-ready Summary
PASKA JAYA — Sistem Manajemen Toko Bangunan (Laravel 11, Livewire 3, Tailwind, MySQL 8 dengan trigger/stored procedure/view, RBAC 4 peran, Chart.js, Pest).
