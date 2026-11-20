SET GLOBAL log_bin_trust_function_creators = 1;

-- Buat 4 akun database (isi password jika untuk server/production)
CREATE USER IF NOT EXISTS 'pj_app'@'localhost' IDENTIFIED BY '';
CREATE USER IF NOT EXISTS 'pj_migrator'@'localhost' IDENTIFIED BY '';
CREATE USER IF NOT EXISTS 'pj_report'@'localhost' IDENTIFIED BY '';
CREATE USER IF NOT EXISTS 'pj_backup'@'localhost' IDENTIFIED BY '';

REVOKE ALL PRIVILEGES, GRANT OPTION FROM 'pj_app'@'localhost';
REVOKE ALL PRIVILEGES, GRANT OPTION FROM 'pj_migrator'@'localhost';
REVOKE ALL PRIVILEGES, GRANT OPTION FROM 'pj_report'@'localhost';
REVOKE ALL PRIVILEGES, GRANT OPTION FROM 'pj_backup'@'localhost';

-- Aplikasi utama (CRUD & EXECUTE)
GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE
ON `paska_jaya`.* TO 'pj_app'@'localhost';

-- Migrasi schema
GRANT ALL PRIVILEGES
ON `paska_jaya`.* TO 'pj_migrator'@'localhost';

-- Pelaporan Owner (read-only)
GRANT SELECT ON `paska_jaya`.`v_laporan_barang_keluar` TO 'pj_report'@'localhost';
GRANT SELECT ON `paska_jaya`.`v_penjualan_harian`      TO 'pj_report'@'localhost';
GRANT SELECT ON `paska_jaya`.`v_ringkasan_toko`        TO 'pj_report'@'localhost';
GRANT SELECT ON `paska_jaya`.`v_riwayat_saldo_laci`    TO 'pj_report'@'localhost';
GRANT EXECUTE ON PROCEDURE `paska_jaya`.`sp_laporan_penjualan`  TO 'pj_report'@'localhost';
GRANT EXECUTE ON PROCEDURE `paska_jaya`.`sp_penjualan_mingguan` TO 'pj_report'@'localhost';
GRANT EXECUTE ON PROCEDURE `paska_jaya`.`sp_detail_bon`         TO 'pj_report'@'localhost';

-- Backup otomatis
GRANT SELECT, SHOW VIEW, TRIGGER, LOCK TABLES, EVENT
ON `paska_jaya`.* TO 'pj_backup'@'localhost';
GRANT SHOW_ROUTINE
ON *.* TO 'pj_backup'@'localhost';

FLUSH PRIVILEGES;