# PASKA JAYA — Setup Guide (Laragon)

## Prerequisites
| Software | Versi |
|---|---|
| Laragon (Full) | terbaru (Apache/Nginx + PHP + MySQL) |
| PHP | 8.3 |
| Composer | 2.x |
| Node.js | 20+ |
| MySQL | 8.0+ |
| Git, VS Code | terbaru |
Spesifikasi laptop minimal: Core i5/Ryzen 5, RAM 8 GB, SSD (sesuai laporan).

## Step 1: Clone Repository
```bash
cd C:\laragon\www
git clone https://github.com/<org>/paska-jaya.git
cd paska-jaya
git checkout develop
```
Lalu checkout branch jatah Anda (lihat GIT_WORKFLOW.md).

## Step 2: Install Dependencies
```bash
composer install
npm install
```

## Step 3: Environment
```bash
copy .env.example .env
php artisan key:generate
```
Isi `.env`: `DB_DATABASE=paska_jaya`, `DB_USERNAME`, `DB_PASSWORD` (lihat TECH_STACK.md). `APP_URL=http://paska-jaya.test` (Laragon auto virtual host).

## Step 4: Setup Database
1. Jalankan Laragon → Start All.
2. Buat database & akun MySQL (HeidiSQL/terminal) dengan `07_extras/database_sql/04_privileges.sql` (ganti password).
3. Migrasi (pakai akun migrator di `.env` sementara):
```bash
php artisan migrate
```
Migrasi membuat 22 tabel + view + trigger + stored procedure, dan mengisi **hanya** data referensi (`roles`, `system_configs`).
4. Kembalikan `.env` ke akun `pj_app`.

## Step 5: Storage
```bash
php artisan storage:link
```
Foto bukti pengiriman memakai folder privat `storage/app/private` (tidak perlu link).

## Step 6: Buat Admin Pertama (tanpa data dummy)
```bash
php artisan app:buat-admin
```
Masukkan nama, email, no HP, password (min. 12 karakter). Tidak ada akun/password bawaan.

## Step 7: Aset Brand
Tempatkan `logo.svg`, `logo-mark.svg`, `favicon.ico`, dan `foto-toko.jpg` di `public/images/` (foto toko: `07_extras/aset/foto_toko.jpg`; logo: buat dengan `05_prompts/PROMPT_LOGO.md`).

## Step 8: Jalankan
```bash
npm run dev          # terminal 1
php artisan serve    # terminal 2 (atau pakai virtual host Laragon)
```
Buka `http://localhost:8000` (atau `http://paska-jaya.test`).

## Step 9: Mengisi Data Nyata
Login sebagai admin → buat akun Owner, Kasir, Karyawan (Manajemen Pengguna) → Kasir/Owner menambah barang & kategori (Master Item) → toko mulai bertransaksi. **Hitung stok fisik (stock opname) sebelum memasukkan stok awal.**

## Step 10: Scheduler (opsional di lokal)
```bash
php artisan schedule:work
```

## Troubleshooting
| Error | Solusi |
|---|---|
| `SQLSTATE[HY000] [1049] Unknown database` | Buat database `paska_jaya` dulu |
| `Vite manifest not found` | Jalankan `npm run dev` atau `npm run build` |
| `Access denied for user 'pj_app'` | Cek `.env` dan grant di `04_privileges.sql` |
| `Specified key was too long` | Pastikan MySQL 8 & charset `utf8mb4` |
| Trigger/procedure gagal dibuat | Jalankan migration dengan akun `pj_migrator` (butuh TRIGGER/CREATE ROUTINE) |
| Foto tidak tampil | `php artisan storage:link` |
