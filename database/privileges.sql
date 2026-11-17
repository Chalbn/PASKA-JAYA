
-- 1. Pembuatan 4 Akun Database (Prinsip Least Privilege)
CREATE USER IF NOT EXISTS 'pj_app'@'localhost' IDENTIFIED BY 'PaskaJayaApp#2026!';
CREATE USER IF NOT EXISTS 'pj_migrator'@'localhost' IDENTIFIED BY 'PaskaJayaMigrator#2026!';
CREATE USER IF NOT EXISTS 'pj_report'@'localhost' IDENTIFIED BY 'PaskaJayaReport#2026!';
CREATE USER IF NOT EXISTS 'pj_backup'@'localhost' IDENTIFIED BY 'PaskaJayaBackup#2026!';

REVOKE ALL PRIVILEGES, GRANT OPTION FROM 'pj_app'@'localhost';
REVOKE ALL PRIVILEGES, GRANT OPTION FROM 'pj_migrator'@'localhost';
REVOKE ALL PRIVILEGES, GRANT OPTION FROM 'pj_report'@'localhost';
REVOKE ALL PRIVILEGES, GRANT OPTION FROM 'pj_backup'@'localhost';

-- 2. pj_app (Akun Utama Aplikasi Laravel)
--    - Hanya boleh manipulasi data (SELECT, INSERT, UPDATE, DELETE)
--    - Boleh menjalankan Stored Procedure & Function (EXECUTE)
--    - DILARANG keras melakukan DDL (CREATE, ALTER, DROP, TRUNCATE, INDEX)
GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE
ON `paska_jaya`.* TO 'pj_app'@'localhost';

-- 3. pj_migrator (Akun Khusus Schema & Migration Laravel)
--    - Memiliki akses penuh untuk membuat/mengubah tabel, view, trigger,
--      function, dan stored procedure saat menjalankan php artisan migrate
GRANT ALL PRIVILEGES
ON `paska_jaya`.* TO 'pj_migrator'@'localhost';

-- 4. pj_report (Akun Khusus Pelaporan / BI / Read-Only)
--    - Hanya boleh membaca tabel & view laporan serta menjalankan SP laporan
GRANT SELECT, SHOW VIEW, EXECUTE
ON `paska_jaya`.* TO 'pj_report'@'localhost';

-- 5. pj_backup (Akun Khusus Backup Otomatis / mysqldump)
--    - Hak akses minimum yang dibutuhkan mysqldump untuk mengekspor struktur,
--      data, view, trigger, dan event tanpa mengubah data
GRANT SELECT, SHOW VIEW, TRIGGER, LOCK TABLES, EVENT
ON `paska_jaya`.* TO 'pj_backup'@'localhost';

-- 6. Terapkan Perubahan Hak Akses
FLUSH PRIVILEGES;