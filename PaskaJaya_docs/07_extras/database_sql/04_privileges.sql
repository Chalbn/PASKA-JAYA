-- PASKA JAYA — Manajemen hak akses MySQL (principle of least privilege)
-- Ganti password sebelum dijalankan. Jangan commit password asli.

CREATE DATABASE IF NOT EXISTS paska_jaya CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 1) Akun aplikasi (dipakai Laravel saat runtime): CRUD + EXECUTE, TANPA DDL
CREATE USER IF NOT EXISTS 'pj_app'@'localhost' IDENTIFIED BY 'GANTI_PASSWORD_APP';
GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE ON paska_jaya.* TO 'pj_app'@'localhost';
-- Catatan: activity_logs dijaga append-only oleh trigger T8 (MySQL tidak mendukung REVOKE per tabel dari GRANT tingkat database).

-- 2) Akun migrasi/deploy (hanya dipakai saat php artisan migrate)
CREATE USER IF NOT EXISTS 'pj_migrator'@'localhost' IDENTIFIED BY 'GANTI_PASSWORD_MIGRATOR';
GRANT ALL PRIVILEGES ON paska_jaya.* TO 'pj_migrator'@'localhost';

-- 3) Akun laporan (read-only, untuk Owner/BI/ekspor): hanya VIEW + SELECT
CREATE USER IF NOT EXISTS 'pj_report'@'localhost' IDENTIFIED BY 'GANTI_PASSWORD_REPORT';
GRANT SELECT ON paska_jaya.v_stok_menipis TO 'pj_report'@'localhost';
GRANT SELECT ON paska_jaya.v_piutang_aktif TO 'pj_report'@'localhost';
GRANT SELECT ON paska_jaya.v_penjualan_harian TO 'pj_report'@'localhost';

-- 4) Akun backup (mysqldump)
CREATE USER IF NOT EXISTS 'pj_backup'@'localhost' IDENTIFIED BY 'GANTI_PASSWORD_BACKUP';
GRANT SELECT, LOCK TABLES, SHOW VIEW, TRIGGER, EVENT ON paska_jaya.* TO 'pj_backup'@'localhost';
GRANT PROCESS ON *.* TO 'pj_backup'@'localhost';

FLUSH PRIVILEGES;
