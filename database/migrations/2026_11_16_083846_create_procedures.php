<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Membuat 13 stored procedure Paska Jaya.
     * Jalankan TERAKHIR: setelah tabel, function, view, dan trigger
     * (sp_laporan_penjualan memakai view v_laporan_barang_keluar, sp_detail_bon memakai v_daftar_bon).
     * DELIMITER tidak dipakai karena bukan perintah SQL.
     */
    public function up(): void
    {
        // sp_catat_aktivitas
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_catat_aktivitas");
        DB::unprepared("
            CREATE PROCEDURE sp_catat_aktivitas(
            IN p_user_id BIGINT UNSIGNED,
            IN p_aksi VARCHAR(50),
            IN p_modul VARCHAR(50),
            IN p_target_id BIGINT UNSIGNED,
            IN p_deskripsi VARCHAR(255),
            IN p_data_lama LONGTEXT,
            IN p_data_baru LONGTEXT
            )
            BEGIN
            INSERT INTO activity_logs (user_id, aksi, modul, target_id, deskripsi, data_lama, data_baru)
            VALUES (p_user_id, p_aksi, p_modul, p_target_id, p_deskripsi, p_data_lama, p_data_baru);
            END
            ");

        // sp_tambah_barang
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_tambah_barang");
        DB::unprepared("
            CREATE PROCEDURE sp_tambah_barang(
            IN p_kode_barang  VARCHAR(20),
            IN p_kategori VARCHAR(80),
            IN p_nama_barang VARCHAR(150),
            IN p_satuan VARCHAR(30),
            IN p_harga_beli BIGINT UNSIGNED,
            IN p_harga_jual BIGINT UNSIGNED,
            IN p_stok_awal INT UNSIGNED,
            IN p_stok_minimum INT UNSIGNED,
            IN p_foto VARCHAR(255),
            IN p_user_id BIGINT UNSIGNED
            )
            BEGIN
            DECLARE v_kategori_id BIGINT UNSIGNED;
            DECLARE v_barang_id BIGINT UNSIGNED;
            DECLARE v_stok INT UNSIGNED;
            DECLARE EXIT HANDLER FOR SQLEXCEPTION
            BEGIN
                ROLLBACK;
                RESIGNAL;
            END;
            SET v_stok = COALESCE(p_stok_awal, 0);
            START TRANSACTION;
            SET v_kategori_id = (SELECT id FROM kategori_barang WHERE nama = TRIM(p_kategori) LIMIT 1);
            IF v_kategori_id IS NULL THEN
                INSERT INTO kategori_barang (nama, slug) VALUES (TRIM(p_kategori), fn_slug(p_kategori));
                SET v_kategori_id = LAST_INSERT_ID();
            END IF;
            INSERT INTO barang (kode_barang, kategori_id, nama_barang, satuan, harga_beli, harga_jual, stok, stok_minimum, foto, tanggal_masuk_terakhir)
            VALUES (NULLIF(TRIM(p_kode_barang), ''), v_kategori_id, TRIM(p_nama_barang), TRIM(p_satuan), p_harga_beli, p_harga_jual, v_stok, 
                COALESCE(p_stok_minimum, 10), p_foto, IF(v_stok > 0, CURDATE(), NULL));
            SET v_barang_id = LAST_INSERT_ID();
            IF v_stok > 0 THEN
                INSERT INTO mutasi_stok
                (barang_id, jenis, jumlah, stok_sebelum, stok_sesudah, referensi_tipe, user_id, keterangan)
                VALUES
                (v_barang_id, 'stok_awal', v_stok, 0, v_stok, 'barang', p_user_id, 'Stok awal barang baru');
            END IF;
            CALL sp_catat_aktivitas(p_user_id, 'tambah', 'barang', v_barang_id, CONCAT('Menambah barang ', TRIM(p_nama_barang)), NULL, NULL);
            COMMIT;
            SELECT id AS barang_id, kode_barang, nama_barang, kategori_id, stok FROM barang WHERE id = v_barang_id;
            END
            ");

        // sp_restock_barang
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_restock_barang");
        DB::unprepared("
            CREATE PROCEDURE sp_restock_barang(
            IN p_barang_id BIGINT UNSIGNED,
            IN p_jumlah_tambah INT UNSIGNED,
            IN p_harga_beli BIGINT UNSIGNED,
            IN p_harga_jual BIGINT UNSIGNED,
            IN p_stok_minimum INT UNSIGNED,
            IN p_user_id BIGINT UNSIGNED
            )
            BEGIN
            DECLARE v_stok INT UNSIGNED;
            DECLARE v_nama VARCHAR(150);
            DECLARE v_harga_beli BIGINT UNSIGNED;
            DECLARE v_harga_jual BIGINT UNSIGNED;
            DECLARE v_tambah INT UNSIGNED;
            DECLARE EXIT HANDLER FOR SQLEXCEPTION
            BEGIN
                ROLLBACK;
                RESIGNAL;
            END;
            SET v_tambah = COALESCE(p_jumlah_tambah, 0);
            START TRANSACTION;
            SELECT stok, nama_barang, harga_beli, harga_jual
                INTO v_stok, v_nama, v_harga_beli, v_harga_jual
                FROM barang
            WHERE id = p_barang_id AND deleted_at IS NULL
                FOR UPDATE;
            IF v_stok IS NULL THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Barang tidak ditemukan';
            END IF;
            UPDATE barang
                SET stok = stok + v_tambah,
                    harga_beli = COALESCE(p_harga_beli, harga_beli),
                    harga_jual = COALESCE(p_harga_jual, harga_jual),
                    stok_minimum = COALESCE(p_stok_minimum, stok_minimum),
                    tanggal_masuk_terakhir = IF(v_tambah > 0, CURDATE(), tanggal_masuk_terakhir)
            WHERE id = p_barang_id;
            IF v_tambah > 0 THEN
                INSERT INTO mutasi_stok
                (barang_id, jenis, jumlah, stok_sebelum, stok_sesudah, referensi_tipe, user_id, keterangan)
                VALUES
                (p_barang_id, 'restock', v_tambah, v_stok, v_stok + v_tambah, 'barang', p_user_id,
                CONCAT('Restock ', v_nama));
            END IF;
            CALL sp_catat_aktivitas(
                p_user_id, 'ubah', 'barang', p_barang_id,
                CONCAT('Restock/edit barang ', v_nama),
                JSON_OBJECT('stok', v_stok, 'harga_beli', v_harga_beli, 'harga_jual', v_harga_jual),
                JSON_OBJECT('stok', v_stok + v_tambah, 'harga_beli', COALESCE(p_harga_beli, v_harga_beli), 'harga_jual', 
                    COALESCE(p_harga_jual, v_harga_jual)));
            COMMIT;
            SELECT id AS barang_id, kode_barang, nama_barang, stok, harga_beli, harga_jual, stok_minimum FROM barang WHERE id = p_barang_id;
            END
            ");

        // sp_kirim_bon
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_kirim_bon");
        DB::unprepared("
            CREATE PROCEDURE sp_kirim_bon(
            IN p_bon_id BIGINT UNSIGNED,
            IN p_karyawan_id BIGINT UNSIGNED,
            IN p_jadwal_kirim DATETIME,
            IN p_catatan TEXT,
            IN p_user_id BIGINT UNSIGNED
            )
            BEGIN
            DECLARE v_penjualan_id  BIGINT UNSIGNED;
            DECLARE v_kode_bon VARCHAR(20);
            DECLARE v_role VARCHAR(30);
            DECLARE v_kurang INT DEFAULT 0;
            DECLARE v_pengiriman_id BIGINT UNSIGNED;
            DECLARE EXIT HANDLER FOR SQLEXCEPTION
            BEGIN
                ROLLBACK;
                RESIGNAL;
            END;
            START TRANSACTION;
            SELECT penjualan_id, kode_bon INTO v_penjualan_id, v_kode_bon FROM bon_piutang
            WHERE id = p_bon_id AND deleted_at IS NULL
                FOR UPDATE;
            IF v_penjualan_id IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Bon tidak ditemukan';
            END IF;
            IF EXISTS (SELECT 1 FROM pengiriman_barang WHERE bon_id = p_bon_id AND status <> 'dibatalkan') THEN
                SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Bon ini sudah memiliki pengiriman aktif (gunakan Ganti Karyawan atau Batalkan Pengiriman)';
            END IF;
            SELECT r.nama INTO v_role FROM users u JOIN roles r ON r.id = u.role_id
            WHERE u.id = p_karyawan_id AND u.status = 'aktif' AND u.deleted_at IS NULL;
            IF v_role IS NULL OR v_role <> 'karyawan' THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Penerima tugas harus pengguna aktif dengan role karyawan';
            END IF;
            SELECT COUNT(*) INTO v_kurang FROM detail_penjualan d JOIN barang b ON b.id = d.barang_id
            WHERE d.penjualan_id = v_penjualan_id
                AND b.stok < d.jumlah
                FOR UPDATE;
            IF v_kurang > 0 THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stok tidak mencukupi untuk satu atau lebih barang pada bon ini';
            END IF;
            INSERT INTO pengiriman_barang (bon_id, karyawan_id, status, jadwal_kirim, catatan_kasir, dibuat_oleh)
            VALUES (p_bon_id, p_karyawan_id, 'dikirim_ke_karyawan', p_jadwal_kirim, p_catatan, p_user_id);
            SET v_pengiriman_id = LAST_INSERT_ID();
            INSERT INTO detail_pengiriman (pengiriman_id, barang_id, nama_barang, satuan, jumlah)
            SELECT v_pengiriman_id, d.barang_id, d.nama_barang, d.satuan, d.jumlah FROM detail_penjualan d
            WHERE d.penjualan_id = v_penjualan_id;
            INSERT INTO mutasi_stok (barang_id, jenis, jumlah, stok_sebelum, stok_sesudah, referensi_tipe, referensi_id, user_id, keterangan)
            SELECT b.id, 'pengiriman', 0 - CAST(dp.jumlah AS SIGNED), b.stok, b.stok - dp.jumlah, 'pengiriman', v_pengiriman_id, p_user_id, 
                CONCAT('Pengiriman ', v_kode_bon)
                FROM detail_pengiriman dp
                JOIN barang b ON b.id = dp.barang_id
            WHERE dp.pengiriman_id = v_pengiriman_id;
            UPDATE barang b
                JOIN detail_pengiriman dp ON dp.barang_id = b.id
                SET b.stok = b.stok - dp.jumlah
            WHERE dp.pengiriman_id = v_pengiriman_id;
            INSERT INTO log_status_pengiriman (bon_id, pengiriman_id, status, user_id, catatan)
            VALUES (p_bon_id, v_pengiriman_id, 'dikirim_ke_karyawan', p_user_id, p_catatan);
            CALL sp_catat_aktivitas(p_user_id, 'kirim_bon', 'bon', p_bon_id, CONCAT('Mengirim ', v_kode_bon, ' ke karyawan'), NULL, NULL);
            COMMIT;
            SELECT v_pengiriman_id AS pengiriman_id;
            END
            ");

        // sp_batalkan_pengiriman
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_batalkan_pengiriman");
        DB::unprepared("
            CREATE PROCEDURE sp_batalkan_pengiriman(
            IN p_pengiriman_id BIGINT UNSIGNED,
            IN p_user_id BIGINT UNSIGNED,
            IN p_alasan VARCHAR(255)
            )
            BEGIN
            DECLARE v_status VARCHAR(30);
            DECLARE v_bon_id BIGINT UNSIGNED;
            DECLARE v_kode_bon VARCHAR(20);
            DECLARE EXIT HANDLER FOR SQLEXCEPTION
            BEGIN
                ROLLBACK;
                RESIGNAL;
            END;
            START TRANSACTION;
            SELECT status, bon_id INTO v_status, v_bon_id FROM pengiriman_barang
            WHERE id = p_pengiriman_id
                FOR UPDATE;
            IF v_status IS NULL THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pengiriman tidak ditemukan';
            END IF;
            IF v_status = 'selesai' THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pengiriman sudah selesai dan tidak dapat dibatalkan';
            END IF;
            IF v_status = 'dibatalkan' THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pengiriman sudah dibatalkan sebelumnya';
            END IF;
            SELECT kode_bon INTO v_kode_bon FROM bon_piutang WHERE id = v_bon_id;
            INSERT INTO mutasi_stok (barang_id, jenis, jumlah, stok_sebelum, stok_sesudah, referensi_tipe, referensi_id, user_id, keterangan)
            SELECT b.id, 'batal_pengiriman', dp.jumlah, b.stok, b.stok + dp.jumlah, 'pengiriman', p_pengiriman_id, p_user_id, 
                CONCAT('Pembatalan pengiriman ', v_kode_bon)
                FROM detail_pengiriman dp
                JOIN barang b ON b.id = dp.barang_id
            WHERE dp.pengiriman_id = p_pengiriman_id;
            UPDATE barang b
                JOIN detail_pengiriman dp ON dp.barang_id = b.id
                SET b.stok = b.stok + dp.jumlah
            WHERE dp.pengiriman_id = p_pengiriman_id;
            UPDATE pengiriman_barang SET status = 'dibatalkan' WHERE id = p_pengiriman_id;
            INSERT INTO log_status_pengiriman (bon_id, pengiriman_id, status, user_id, catatan)
            VALUES (v_bon_id, p_pengiriman_id, 'belum_dikirim', p_user_id, CONCAT('Pengiriman dibatalkan', 
                IF(p_alasan IS NULL OR p_alasan = '', '', CONCAT(': ', p_alasan))));
            CALL sp_catat_aktivitas(p_user_id, 'ubah', 'bon', v_bon_id, CONCAT('Membatalkan pengiriman ', v_kode_bon), NULL, NULL);
            COMMIT;
            END
            ");

        // sp_ganti_karyawan
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_ganti_karyawan");
        DB::unprepared("
            CREATE PROCEDURE sp_ganti_karyawan(
            IN p_pengiriman_id BIGINT UNSIGNED,
            IN p_karyawan_baru_id BIGINT UNSIGNED,
            IN p_user_id BIGINT UNSIGNED
            )
            BEGIN
            DECLARE v_status VARCHAR(30);
            DECLARE v_bon_id BIGINT UNSIGNED;
            DECLARE v_role VARCHAR(30);
            DECLARE EXIT HANDLER FOR SQLEXCEPTION
            BEGIN
                ROLLBACK;
                RESIGNAL;
            END;
            START TRANSACTION;
            SELECT status, bon_id INTO v_status, v_bon_id
                FROM pengiriman_barang
            WHERE id = p_pengiriman_id
                FOR UPDATE;
            IF v_status IS NULL THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pengiriman tidak ditemukan';
            END IF;
            IF v_status IN ('selesai', 'dibatalkan') THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Karyawan tidak dapat diganti pada pengiriman yang sudah selesai/dibatalkan';
            END IF;
            SELECT r.nama INTO v_role
                FROM users u JOIN roles r ON r.id = u.role_id
            WHERE u.id = p_karyawan_baru_id AND u.status = 'aktif' AND u.deleted_at IS NULL;
            IF v_role IS NULL OR v_role <> 'karyawan' THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Penerima tugas harus pengguna aktif dengan role karyawan';
            END IF;
            UPDATE pengiriman_barang
                SET karyawan_id = p_karyawan_baru_id,
                    status = 'dikirim_ke_karyawan',
                    dibaca_pada = NULL
            WHERE id = p_pengiriman_id;
            INSERT INTO log_status_pengiriman (bon_id, pengiriman_id, status, user_id, catatan)
            VALUES (v_bon_id, p_pengiriman_id, 'dikirim_ke_karyawan', p_user_id, 'Karyawan penerima tugas diganti');
            CALL sp_catat_aktivitas(p_user_id, 'ubah', 'bon', v_bon_id, 'Mengganti karyawan pengantar', NULL, NULL);
            COMMIT;
            END
            ");

        // sp_tandai_dibaca
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_tandai_dibaca");
        DB::unprepared("
            CREATE PROCEDURE sp_tandai_dibaca(
            IN p_pengiriman_id BIGINT UNSIGNED,
            IN p_user_id BIGINT UNSIGNED
            )
            BEGIN
            UPDATE pengiriman_barang
                SET dibaca_pada = NOW()
            WHERE id = p_pengiriman_id
                AND karyawan_id = p_user_id
                AND dibaca_pada IS NULL;
            END
            ");

        // sp_ubah_status_pengiriman
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_ubah_status_pengiriman");
        DB::unprepared("
            CREATE PROCEDURE sp_ubah_status_pengiriman(
            IN p_pengiriman_id BIGINT UNSIGNED,
            IN p_status_baru VARCHAR(30),
            IN p_foto_bukti VARCHAR(255),
            IN p_catatan VARCHAR(255),
            IN p_user_id BIGINT UNSIGNED
            )
            BEGIN
            DECLARE v_status VARCHAR(30);
            DECLARE v_bon_id BIGINT UNSIGNED;
            DECLARE v_karyawan BIGINT UNSIGNED;
            DECLARE v_berikut VARCHAR(30);
            DECLARE EXIT HANDLER FOR SQLEXCEPTION
            BEGIN
                ROLLBACK;
                RESIGNAL;
            END;
            START TRANSACTION;
            SELECT status, bon_id, karyawan_id INTO v_status, v_bon_id, v_karyawan
                FROM pengiriman_barang
            WHERE id = p_pengiriman_id
                FOR UPDATE;
            IF v_status IS NULL THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pengiriman tidak ditemukan';
            END IF;
            IF v_karyawan IS NULL OR v_karyawan <> p_user_id THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pengiriman ini bukan tugas Anda';
            END IF;
            IF v_status = 'dikirim_ke_karyawan' THEN
                SET v_berikut = 'sedang_disiapkan';
            ELSEIF v_status = 'sedang_disiapkan' THEN
                SET v_berikut = 'siap_berangkat';
            ELSEIF v_status = 'siap_berangkat' THEN
                SET v_berikut = 'selesai';
            ELSE
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Status pengiriman sudah final dan tidak dapat diubah';
            END IF;
            IF p_status_baru IS NULL OR p_status_baru <> v_berikut THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Urutan status tidak valid; ikuti alur status berikutnya';
            END IF;
            UPDATE pengiriman_barang
                SET status      = v_berikut,
                    foto_bukti  = COALESCE(p_foto_bukti, foto_bukti),
                    dibaca_pada = COALESCE(dibaca_pada, NOW())
            WHERE id = p_pengiriman_id;
            INSERT INTO log_status_pengiriman (bon_id, pengiriman_id, status, user_id, catatan)
            VALUES (v_bon_id, p_pengiriman_id, v_berikut, p_user_id, p_catatan);
            CALL sp_catat_aktivitas(p_user_id, 'ubah', 'pengiriman', p_pengiriman_id, 
                CONCAT('Status pengiriman menjadi ', v_berikut), NULL, NULL);
            COMMIT;
            END
            ");

        // sp_bayar_bon
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_bayar_bon");
        DB::unprepared("
            CREATE PROCEDURE sp_bayar_bon(
            IN p_bon_id BIGINT UNSIGNED,
            IN p_jumlah BIGINT UNSIGNED,
            IN p_metode VARCHAR(10),
            IN p_catatan VARCHAR(255),
            IN p_user_id BIGINT UNSIGNED
            )
            BEGIN
            DECLARE v_sisa BIGINT;
            DECLARE v_bayar BIGINT UNSIGNED;
            DECLARE v_kode_bon VARCHAR(20);
            DECLARE EXIT HANDLER FOR SQLEXCEPTION
            BEGIN
                ROLLBACK;
                RESIGNAL;
            END;
            START TRANSACTION;
            SELECT kode_bon, CAST(total_tagihan AS SIGNED) - CAST(total_terbayar AS SIGNED) INTO v_kode_bon, v_sisa FROM bon_piutang
            WHERE id = p_bon_id AND deleted_at IS NULL
                FOR UPDATE;
            IF v_sisa IS NULL THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Bon tidak ditemukan';
            END IF;
            IF v_sisa <= 0 THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Bon sudah lunas';
            END IF;
            IF p_metode NOT IN ('cash', 'transfer') THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Metode pembayaran harus cash atau transfer';
            END IF;
            SET v_bayar = COALESCE(p_jumlah, v_sisa);
            INSERT INTO pembayaran_bon (bon_id, jumlah, metode, waktu_bayar, user_id, catatan)
            VALUES (p_bon_id, v_bayar, p_metode, NOW(), p_user_id, p_catatan);
            CALL sp_catat_aktivitas(p_user_id, 'bayar_bon', 'bon', p_bon_id, 
                CONCAT('Pembayaran ', v_kode_bon, ' sebesar Rp ', v_bayar), NULL, NULL);
            COMMIT;
            SELECT id AS bon_id, kode_bon, total_tagihan, total_terbayar, CAST(total_tagihan AS SIGNED) - CAST(total_terbayar AS SIGNED) 
                AS sisa, status_bayar FROM bon_piutang WHERE id = p_bon_id;
            END
            ");

        // sp_reset_laci
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_reset_laci");
        DB::unprepared("
            CREATE PROCEDURE sp_reset_laci(
            IN p_tipe VARCHAR(10),
            IN p_user_id BIGINT UNSIGNED
            )
            BEGIN
            DECLARE v_cash BIGINT DEFAULT 0;
            DECLARE v_transfer BIGINT DEFAULT 0;
            DECLARE v_tanggal DATE;
            DECLARE v_id BIGINT UNSIGNED;
            DECLARE EXIT HANDLER FOR SQLEXCEPTION
            BEGIN
                ROLLBACK;
                RESIGNAL;
            END;
            IF p_tipe IS NULL OR p_tipe NOT IN ('otomatis', 'manual') THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Tipe reset harus otomatis atau manual';
            END IF;
            IF p_tipe = 'manual' AND p_user_id IS NULL THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Reset manual harus menyertakan pengguna';
            END IF;
            START TRANSACTION;
            SELECT COALESCE(SUM(IF(metode = 'cash', jumlah, 0)), 0), COALESCE(SUM(IF(metode = 'transfer', jumlah, 0)), 0), MAX(tanggal)
                INTO v_cash, v_transfer, v_tanggal FROM kas_laci
            WHERE penutupan_id IS NULL
                FOR UPDATE;
            IF v_tanggal IS NULL THEN
                SET v_tanggal = CURDATE();
            END IF;
            INSERT INTO penutupan_laci (tanggal, total_cash, total_transfer, total, waktu_reset, direset_oleh, tipe_reset)
            VALUES (v_tanggal, v_cash, v_transfer, v_cash + v_transfer, NOW(), p_user_id, p_tipe);
            SET v_id = LAST_INSERT_ID();
            UPDATE kas_laci SET penutupan_id = v_id WHERE penutupan_id IS NULL;
            CALL sp_catat_aktivitas(p_user_id, 'reset_laci', 'laci', v_id, 
                CONCAT('Reset laci (', p_tipe, ') total Rp ', v_cash + v_transfer), NULL, NULL);
            COMMIT;
            SELECT v_id AS penutupan_id, v_tanggal AS tanggal, v_cash AS total_cash, v_transfer AS total_transfer, v_cash + v_transfer AS total;
            END
            ");

        // sp_laporan_penjualan
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_laporan_penjualan");
        DB::unprepared("
            CREATE PROCEDURE sp_laporan_penjualan(
            IN p_dari DATE,
            IN p_sampai DATE,
            IN p_metode VARCHAR(10)
            )
            BEGIN
            DECLARE v_dari DATE;
            DECLARE v_sampai DATE;
            DECLARE v_metode VARCHAR(10);
            SET v_dari = COALESCE(p_dari,   DATE_SUB(CURDATE(), INTERVAL 6 DAY));
            SET v_sampai = COALESCE(p_sampai, CURDATE());
            SET v_metode = IF(p_metode IS NULL OR p_metode = '' OR LOWER(p_metode) = 'semua', NULL, LOWER(p_metode));
            SELECT COALESCE(SUM(p.total), 0) AS total_penjualan,
                    COALESCE(SUM(IF(p.metode_bayar = 'cash', p.total, 0)), 0) AS total_cash,
                    COALESCE(SUM(IF(p.metode_bayar = 'transfer', p.total, 0)), 0) AS total_transfer,
                    COUNT(*) AS jumlah_transaksi FROM penjualan p
            WHERE p.tipe = 'langsung'
                AND p.waktu_transaksi >= v_dari
                AND p.waktu_transaksi < DATE_ADD(v_sampai, INTERVAL 1 DAY)
                AND (v_metode IS NULL OR p.metode_bayar = v_metode);
            SELECT l.* FROM v_laporan_barang_keluar l
            WHERE l.waktu_transaksi >= v_dari
                AND l.waktu_transaksi < DATE_ADD(v_sampai, INTERVAL 1 DAY)
                AND (v_metode IS NULL OR l.metode_bayar = v_metode)
            ORDER BY l.waktu_transaksi DESC;
            END
            ");

        // sp_detail_bon
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_detail_bon");
        DB::unprepared("
            CREATE PROCEDURE sp_detail_bon(IN p_bon_id BIGINT UNSIGNED)
            BEGIN
            SELECT * FROM v_daftar_bon WHERE bon_id = p_bon_id;
            SELECT d.nama_barang, d.satuan, d.jumlah, d.harga_satuan, d.subtotal 
                FROM detail_penjualan d JOIN bon_piutang bp ON bp.penjualan_id = d.penjualan_id
            WHERE bp.id = p_bon_id
            ORDER BY d.id;
            SELECT l.status, l.created_at AS waktu, l.catatan, u.nama AS oleh 
                FROM log_status_pengiriman l LEFT JOIN users u ON u.id = l.user_id
            WHERE l.bon_id = p_bon_id
            ORDER BY l.created_at, l.id;
            SELECT pb.waktu_bayar, pb.jumlah, pb.metode, pb.catatan, u.nama AS kasir 
                FROM pembayaran_bon pb JOIN users u ON u.id = pb.user_id
            WHERE pb.bon_id = p_bon_id
            ORDER BY pb.waktu_bayar;
            END
            ");

        // sp_penjualan_mingguan
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_penjualan_mingguan");
        DB::unprepared("
            CREATE PROCEDURE sp_penjualan_mingguan(IN p_tanggal_mulai DATE)
            BEGIN
            DECLARE v_mulai DATE;
            DECLARE v_akhir DATE;
            DECLARE v_total BIGINT DEFAULT 0;
            DECLARE v_total_lalu BIGINT DEFAULT 0;
            SET v_mulai = COALESCE(p_tanggal_mulai, DATE_SUB(CURDATE(), INTERVAL 6 DAY));
            SET v_akhir = DATE_ADD(v_mulai, INTERVAL 7 DAY);
            SELECT d.tanggal,
                    COALESCE(SUM(IF(p.metode_bayar = 'cash', p.total, 0)), 0) AS total_cash,
                    COALESCE(SUM(IF(p.metode_bayar = 'transfer', p.total, 0)), 0) AS total_transfer,
                    COALESCE(SUM(p.total), 0) AS total,
                    COUNT(p.id) AS jumlah_transaksi
                FROM (SELECT DATE_ADD(v_mulai, INTERVAL 0 DAY) AS tanggal UNION ALL
                    SELECT DATE_ADD(v_mulai, INTERVAL 1 DAY) UNION ALL
                    SELECT DATE_ADD(v_mulai, INTERVAL 2 DAY) UNION ALL
                    SELECT DATE_ADD(v_mulai, INTERVAL 3 DAY) UNION ALL
                    SELECT DATE_ADD(v_mulai, INTERVAL 4 DAY) UNION ALL
                    SELECT DATE_ADD(v_mulai, INTERVAL 5 DAY) UNION ALL
                    SELECT DATE_ADD(v_mulai, INTERVAL 6 DAY)) d
                LEFT JOIN penjualan p
                    ON p.tipe = 'langsung'
                    AND p.waktu_transaksi >= d.tanggal
                    AND p.waktu_transaksi <  DATE_ADD(d.tanggal, INTERVAL 1 DAY)
            GROUP BY d.tanggal
            ORDER BY d.tanggal;
            SELECT COALESCE(SUM(total), 0) INTO v_total
                FROM penjualan
            WHERE tipe = 'langsung' AND waktu_transaksi >= v_mulai AND waktu_transaksi < v_akhir;
            SELECT COALESCE(SUM(total), 0) INTO v_total_lalu
                FROM penjualan
            WHERE tipe = 'langsung'
                AND waktu_transaksi >= DATE_SUB(v_mulai, INTERVAL 7 DAY)
                AND waktu_transaksi <  v_mulai;
            SELECT v_total AS total_minggu_ini,
                    v_total_lalu AS total_minggu_lalu,
                    IF(v_total_lalu = 0, NULL, ROUND((v_total - v_total_lalu) / v_total_lalu * 100, 1)) AS persen_perubahan,
                    (SELECT DATE(waktu_transaksi) FROM penjualan
                    WHERE tipe = 'langsung' AND waktu_transaksi >= v_mulai AND waktu_transaksi < v_akhir
                    GROUP BY DATE(waktu_transaksi)
                    ORDER BY SUM(total) DESC
                    LIMIT 1) AS hari_tertinggi;
            END
            ");

    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_catat_aktivitas");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_tambah_barang");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_restock_barang");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_kirim_bon");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_batalkan_pengiriman");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_ganti_karyawan");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_tandai_dibaca");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_ubah_status_pengiriman");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_bayar_bon");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_reset_laci");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_laporan_penjualan");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_detail_bon");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_penjualan_mingguan");
    }
};