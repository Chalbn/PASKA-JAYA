-- PASKA JAYA — STORED PROCEDURE (MySQL 8). Dipanggil Laravel: DB::select('CALL sp_x(?, ?)', [...]).
DELIMITER $$

-- P1. Restock barang (tambah stok barang baru datang)
CREATE PROCEDURE sp_restock_barang(IN p_barang_id BIGINT UNSIGNED, IN p_tambah INT UNSIGNED, IN p_user_id BIGINT UNSIGNED)
BEGIN
  DECLARE v_sebelum INT;
  DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
  IF p_tambah = 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Jumlah restock harus lebih dari 0';
  END IF;
  START TRANSACTION;
  SELECT stok INTO v_sebelum FROM barang WHERE id = p_barang_id AND deleted_at IS NULL FOR UPDATE;
  UPDATE barang SET stok = stok + p_tambah, tanggal_masuk_terakhir = CURDATE() WHERE id = p_barang_id;
  INSERT INTO mutasi_stok (barang_id, jenis, jumlah, stok_sebelum, stok_sesudah, user_id, keterangan, created_at)
  VALUES (p_barang_id, 'restock', p_tambah, v_sebelum, v_sebelum + p_tambah, p_user_id, 'Barang baru datang', NOW());
  COMMIT;
END$$

-- P2. Kirim bon ke karyawan (buat pengiriman, salin detail, potong stok, catat log)
CREATE PROCEDURE sp_kirim_bon(IN p_bon_id BIGINT UNSIGNED, IN p_karyawan_id BIGINT UNSIGNED,
                              IN p_jadwal DATETIME, IN p_catatan TEXT, IN p_user_id BIGINT UNSIGNED)
BEGIN
  DECLARE v_penjualan_id BIGINT UNSIGNED;
  DECLARE v_pengiriman_id BIGINT UNSIGNED;
  DECLARE v_aktif INT;
  DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
  START TRANSACTION;
  SELECT penjualan_id INTO v_penjualan_id FROM bon_piutang WHERE id = p_bon_id AND deleted_at IS NULL FOR UPDATE;
  SELECT COUNT(*) INTO v_aktif FROM pengiriman_barang WHERE bon_id = p_bon_id AND status <> 'dibatalkan';
  IF v_aktif > 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Bon sudah dikirim';
  END IF;

  INSERT INTO pengiriman_barang (bon_id, karyawan_id, status, jadwal_kirim, catatan_kasir, dibuat_oleh, created_at, updated_at)
  VALUES (p_bon_id, p_karyawan_id, 'dikirim_ke_karyawan', p_jadwal, p_catatan, p_user_id, NOW(), NOW());
  SET v_pengiriman_id = LAST_INSERT_ID();

  INSERT INTO detail_pengiriman (pengiriman_id, barang_id, nama_barang, satuan, jumlah)
  SELECT v_pengiriman_id, barang_id, nama_barang, satuan, jumlah
  FROM detail_penjualan WHERE penjualan_id = v_penjualan_id;

  INSERT INTO mutasi_stok (barang_id, jenis, jumlah, stok_sebelum, stok_sesudah, referensi_tipe, referensi_id, user_id, keterangan, created_at)
  SELECT b.id, 'pengiriman', -d.jumlah, b.stok, b.stok - d.jumlah, 'pengiriman', v_pengiriman_id, p_user_id,
         CONCAT('Bon dikirim #', p_bon_id), NOW()
  FROM detail_pengiriman d JOIN barang b ON b.id = d.barang_id
  WHERE d.pengiriman_id = v_pengiriman_id;

  UPDATE barang b JOIN detail_pengiriman d ON d.barang_id = b.id
  SET b.stok = b.stok - d.jumlah
  WHERE d.pengiriman_id = v_pengiriman_id;           -- T1 menolak bila stok kurang

  INSERT INTO log_status_pengiriman (bon_id, pengiriman_id, status, user_id, created_at)
  VALUES (p_bon_id, v_pengiriman_id, 'dikirim_ke_karyawan', p_user_id, NOW());
  COMMIT;
  SELECT v_pengiriman_id AS pengiriman_id;
END$$

-- P3. Batalkan pengiriman (stok kembali) — hanya bila belum selesai
CREATE PROCEDURE sp_batalkan_pengiriman(IN p_bon_id BIGINT UNSIGNED, IN p_user_id BIGINT UNSIGNED)
BEGIN
  DECLARE v_pengiriman_id BIGINT UNSIGNED;
  DECLARE v_status VARCHAR(30);
  DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
  START TRANSACTION;
  SELECT id, status INTO v_pengiriman_id, v_status FROM pengiriman_barang
    WHERE bon_id = p_bon_id AND status <> 'dibatalkan' FOR UPDATE;
  IF v_status = 'selesai' THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pengiriman yang sudah selesai tidak dapat dibatalkan';
  END IF;

  INSERT INTO mutasi_stok (barang_id, jenis, jumlah, stok_sebelum, stok_sesudah, referensi_tipe, referensi_id, user_id, keterangan, created_at)
  SELECT b.id, 'batal_pengiriman', d.jumlah, b.stok, b.stok + d.jumlah, 'pengiriman', v_pengiriman_id, p_user_id,
         CONCAT('Pembatalan pengiriman bon #', p_bon_id), NOW()
  FROM detail_pengiriman d JOIN barang b ON b.id = d.barang_id WHERE d.pengiriman_id = v_pengiriman_id;

  UPDATE barang b JOIN detail_pengiriman d ON d.barang_id = b.id
  SET b.stok = b.stok + d.jumlah WHERE d.pengiriman_id = v_pengiriman_id;

  UPDATE pengiriman_barang SET status = 'dibatalkan', updated_at = NOW() WHERE id = v_pengiriman_id;
  INSERT INTO log_status_pengiriman (bon_id, pengiriman_id, status, user_id, catatan, created_at)
  VALUES (p_bon_id, v_pengiriman_id, 'belum_dikirim', p_user_id, 'Pengiriman dibatalkan', NOW());
  COMMIT;
END$$

-- P4. Reset / tutup laci (otomatis oleh scheduler: p_user_id NULL, atau manual oleh kasir)
CREATE PROCEDURE sp_reset_laci(IN p_user_id BIGINT UNSIGNED, IN p_tipe VARCHAR(10))
BEGIN
  DECLARE v_cash BIGINT UNSIGNED DEFAULT 0;
  DECLARE v_transfer BIGINT UNSIGNED DEFAULT 0;
  DECLARE v_id BIGINT UNSIGNED;
  DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
  START TRANSACTION;
  SELECT COALESCE(SUM(CASE WHEN metode='cash' THEN jumlah END),0),
         COALESCE(SUM(CASE WHEN metode='transfer' THEN jumlah END),0)
    INTO v_cash, v_transfer FROM kas_laci WHERE penutupan_id IS NULL FOR UPDATE;
  INSERT INTO penutupan_laci (tanggal, total_cash, total_transfer, total, waktu_reset, direset_oleh, tipe_reset, created_at)
  VALUES (IF(p_tipe='otomatis', DATE_SUB(CURDATE(), INTERVAL 1 DAY), CURDATE()),
          v_cash, v_transfer, v_cash + v_transfer, NOW(), p_user_id, p_tipe, NOW());
  SET v_id = LAST_INSERT_ID();
  UPDATE kas_laci SET penutupan_id = v_id WHERE penutupan_id IS NULL;
  COMMIT;
  SELECT v_id AS penutupan_id;
END$$

-- P5. Laporan penjualan langsung per rentang tanggal (+ filter metode, NULL = semua)
CREATE PROCEDURE sp_laporan_penjualan(IN p_dari DATE, IN p_sampai DATE, IN p_metode VARCHAR(10))
BEGIN
  SELECT p.id, p.kode_transaksi, p.waktu_transaksi, p.nama_pelanggan, p.metode_bayar, p.total
  FROM penjualan p
  WHERE p.tipe = 'langsung'
    AND DATE(p.waktu_transaksi) BETWEEN p_dari AND p_sampai
    AND (p_metode IS NULL OR p.metode_bayar = p_metode)
  ORDER BY p.waktu_transaksi DESC;
END$$

DELIMITER ;
