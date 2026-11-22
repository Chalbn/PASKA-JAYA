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
1. Jalankan Laragon → Start All. Pastikan database `paska_jaya` tersedia (dapat dibuat otomatis saat menjalankan `php artisan migrate`, atau dibuat manual via HeidiSQL/terminal MySQL dengan charset `utf8mb4`).
2. Jalankan migrasi dan seeder referensi (`roles`, `system_configs`) — di lokal bisa memakai akun root/migrator, saat deploy gunakan akun `pj_migrator`:
```bash
php artisan migrate
php artisan migrate --seed
```
   (Jika menjalankan langsung dari awal, `php artisan migrate --seed` sekaligus membuat tabel dan mengisi data referensi `roles` & `system_configs`.)
   Migrasi membuat 22 tabel serta memasang seluruh objek database lanjutan dari `database/migrations/` (`2026_11_16_081337_create_functions.php`, `2026_11_16_081958_create_views.php`, `2026_11_16_083330_create_triggers.php`, `2026_11_16_083846_create_procedures.php`: 9 function, 19 view, 19 trigger, 13 stored procedure).
   - **Catatan Error `1419`:** Jika `pj_migrator` mengalami error `1419` (`log_bin_trust_function_creators`) saat menjalankan migration function/trigger, jalankan sekali sebagai `root`:
     ```sql
     SET GLOBAL log_bin_trust_function_creators = 1;
     ```
3. Jalankan skrip hak akses `database/privileges.sql` (**opsional di lokal, wajib saat deploy**):
   - Buka `database/privileges.sql` dan **ganti seluruh placeholder password** (`GANTI_PASSWORD_APP`, `GANTI_PASSWORD_MIGRATOR`, `GANTI_PASSWORD_REPORT`, `GANTI_PASSWORD_BACKUP`) dengan password kuat (jangan simpan password asli ke repo).
   - Jalankan skrip `database/privileges.sql` di HeidiSQL/terminal MySQL sebagai `root` **setelah** `php artisan migrate` selesai (agar view & stored procedure untuk `pj_report` sudah tersedia).
4. Kembalikan `.env` aplikasi ke akun `pj_app`, lalu verifikasi akun `pj_app` ditolak melakukan DDL (`ERROR 1142`):
```bash
mysql -u pj_app -p paska_jaya -e "CREATE TABLE _uji_ddl (id INT);"
```
   Output yang diharapkan: `ERROR 1142 (42000): CREATE command denied to user 'pj_app'@'localhost' for table '_uji_ddl'`.

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
Tempatkan `logo.svg`, `logo-mark.svg`, `favicon.ico`, dan `foto-toko.jpg` di `public/images/` (foto toko: `07_extras/aset/foto_toko.jpg`).

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
| `Access denied for user 'pj_app'` | Cek `.env` dan grant di `database/privileges.sql` |
| `Specified key was too long` | Pastikan MySQL 8 & charset `utf8mb4` |
| Trigger/procedure gagal dibuat (Error `1419`) | Jalankan migration dengan akun `pj_migrator` atau set sekali sebagai `root`: `SET GLOBAL log_bin_trust_function_creators = 1;` |
| Foto tidak tampil | `php artisan storage:link` |
