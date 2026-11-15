# PASKA JAYA — Tech Stack Detail

> Sumber: laporan MSBD Kelompok 7 §2.2.1 (Kelayakan Teknis): Laravel + Livewire, Laragon, VS Code, MySQL.

## Core Stack
| Lapisan | Pilihan | Catatan |
|---|---|---|
| Backend | **Laravel 11** (PHP 8.3) | Routing, auth, validasi, queue, scheduler |
| UI interaktif | **Livewire 3** (full-page components) | POS & stok tanpa reload halaman; Alpine.js sudah termasuk |
| Styling | **Tailwind CSS** + Vite | Tanpa custom CSS kecuali token & utilitas kecil |
| Chart | **Chart.js** (npm) | Grafik 7 hari (bar), grafik mingguan (stacked bar) |
| Database | **MySQL 8** | Trigger, stored procedure, view (lihat `07_extras/database_sql`) |
| Local server | **Laragon** | Apache/Nginx + PHP + MySQL di Windows |
| Editor | VS Code | Ekstensi: PHP Intelephense, Laravel Blade, Tailwind IntelliSense |
| Test | Pest + Livewire testing helpers | |
| Lint | Laravel Pint, Prettier (Blade/JS) | |
| Biaya | **Rp 0** (open source; sesuai laporan Tabel 2.1) | domain & hosting belum dibebankan |

## Composer Packages (Required)
| Package | Fungsi |
|---|---|
| `livewire/livewire` ^3 | Komponen full-page, form, upload file, event |
| `maatwebsite/excel` | Ekspor Excel & CSV (Laporan Barang Keluar, Riwayat Saldo Laci) |
| `barryvdh/laravel-dompdf` | Ekspor PDF Riwayat Saldo Laci |
Dev: `pestphp/pestphp`, `pestphp/pest-plugin-laravel`, `laravel/pint`.

## NPM Packages
`tailwindcss`, `postcss`, `autoprefixer`, `vite`, `laravel-vite-plugin`, `chart.js`, `@fontsource/plus-jakarta-sans` (font lokal, tanpa CDN).

## Environment Variables (`.env.example`)
```
APP_NAME="PASKA JAYA"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://paska-jaya.test
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=paska_jaya
DB_USERNAME=pj_app
DB_PASSWORD=
FILESYSTEM_DISK=local
AUTH_OTP_ENABLED=false
MAIL_MAILER=log
SESSION_LIFETIME=120
```
Akun `pj_migrator` hanya dipakai saat `php artisan migrate` (lihat SECURITY_GUIDELINES).

## Server Requirements (Production)
PHP 8.3 + ekstensi BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO MySQL, Tokenizer, XML, GD. MySQL 8.0+. HTTPS wajib. Cron: `* * * * * php artisan schedule:run`.

## Scheduler
| Command | Jadwal | Fungsi |
|---|---|---|
| `laci:reset` | 00:00 | `CALL sp_reset_laci(NULL, 'otomatis')` |
| `backup:run` | 02:00 | `mysqldump` (akun `pj_backup`) + tulis `system_configs.backup_terakhir` |
