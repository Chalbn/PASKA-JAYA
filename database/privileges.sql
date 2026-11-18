-- =============================================================================
-- Skrip Privilege Database Paska Jaya (Prinsip Least Privilege - ADR-013)
-- PENTING:
-- 1. Ganti semua placeholder GANTI_PASSWORD_* sebelum menjalankan skrip ini.
-- 2. Jalankan skrip ini SETELAH `php artisan migrate` agar seluruh view dan
--    stored procedure untuk `pj_report` sudah tersedia di database.
-- 3. Jika `pj_migrator` mengalami error 1419 saat migrate, jalankan sekali
--    sebagai root: SET GLOBAL log_bin_trust_function_creators = 1;
-- =============================================================================

-- 1. Pembuatan 4 Akun Database
CREATE USER IF NOT EXISTS 'pj_app'@'localhost' IDENTIFIED BY 'GANTI_PASSWORD_APP';
CREATE USER IF NOT EXISTS 'pj_migrator'@'localhost' IDENTIFIED BY 'GANTI_PASSWORD_MIGRATOR';
CREATE USER IF NOT EXISTS 'pj_report'@'localhost' IDENTIFIED BY 'GANTI_PASSWORD_REPORT';
CREATE USER IF NOT EXISTS 'pj_backup'@'localhost' IDENTIFIED BY 'GANTI_PASSWORD_BACKUP';

REVOKE ALL PRIVILEGES, GRANT OPTION FROM 'pj_app'@'localhost';
REVOKE ALL PRIVILEGES, GRANT OPTION FROM 'pj_migrator'@'localhost';
REVOKE ALL PRIVILEGES, GRANT OPTION FROM 'pj_report'@'localhost';
REVOKE ALL PRIVILEGES, GRANT OPTION FROM 'pj_backup'@'localhost';

-- 2. pj_app (Akun Utama Aplikasi Laravel)
--    - Hanya boleh manipulasi data (SELECT, INSERT, UPDATE, DELETE) & EXECUTE
--    - DILARANG melakukan DDL (CREATE, ALTER, DROP, TRUNCATE, INDEX)
GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE
ON `paska_jaya`.* TO 'pj_app'@'localhost';

-- 3. pj_migrator (Akun Khusus Schema & Migration Laravel)
--    - Hak penuh untuk mengelola tabel, view, trigger, function, dan procedure
GRANT ALL PRIVILEGES
ON `paska_jaya`.* TO 'pj_migrator'@'localhost';

-- 4. pj_report (Akun Khusus Pelaporan / Read-Only)
--    - Terbatas hanya pada view laporan dan 3 stored procedure baca
--    - Syarat: jalankan setelah `php artisan migrate`
GRANT SELECT ON `paska_jaya`.`v_laporan_barang_keluar` TO 'pj_report'@'localhost';
GRANT SELECT ON `paska_jaya`.`v_penjualan_harian`      TO 'pj_report'@'localhost';
GRANT SELECT ON `paska_jaya`.`v_ringkasan_toko`        TO 'pj_report'@'localhost';
GRANT SELECT ON `paska_jaya`.`v_riwayat_saldo_laci`    TO 'pj_report'@'localhost';

GRANT EXECUTE ON PROCEDURE `paska_jaya`.`sp_laporan_penjualan`  TO 'pj_report'@'localhost';
GRANT EXECUTE ON PROCEDURE `paska_jaya`.`sp_penjualan_mingguan` TO 'pj_report'@'localhost';
GRANT EXECUTE ON PROCEDURE `paska_jaya`.`sp_detail_bon`         TO 'pj_report'@'localhost';

-- 5. pj_backup (Akun Khusus Backup Otomatis / mysqldump)
--    - Saat backup gunakan opsi: mysqldump --routines --no-tablespaces ...
GRANT SELECT, SHOW VIEW, TRIGGER, LOCK TABLES, EVENT
ON `paska_jaya`.* TO 'pj_backup'@'localhost';

GRANT SHOW_ROUTINE
ON *.* TO 'pj_backup'@'localhost';

-- 6. Terapkan Perubahan Hak Akses
FLUSH PRIVILEGES;