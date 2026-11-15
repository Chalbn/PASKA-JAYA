-- PASKA JAYA — TRIGGER (MySQL 8). Referensi; dipasang lewat migration (DB::unprepared, tanpa DELIMITER).
DELIMITER $$

-- T1. Stok tidak boleh negatif
CREATE TRIGGER trg_barang_bu_stok BEFORE UPDATE ON barang
FOR EACH ROW
BEGIN
  IF NEW.stok < 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stok tidak mencukupi';
  END IF;
END$$

-- T2. Penjualan langsung: kurangi stok + catat mutasi_stok setiap baris detail
CREATE TRIGGER trg_detail_penjualan_ai AFTER INSERT ON detail_penjualan
FOR EACH ROW
BEGIN
  DECLARE v_tipe VARCHAR(10);
  DECLARE v_user BIGINT UNSIGNED;
  DECLARE v_kode VARCHAR(20);
  DECLARE v_sebelum INT;
  SELECT tipe, user_id, kode_transaksi INTO v_tipe, v_user, v_kode
    FROM penjualan WHERE id = NEW.penjualan_id;
  IF v_tipe = 'langsung' THEN
    SELECT stok INTO v_sebelum FROM barang WHERE id = NEW.barang_id FOR UPDATE;
    UPDATE barang SET stok = stok - NEW.jumlah WHERE id = NEW.barang_id;  -- T1 menjaga >= 0
    INSERT INTO mutasi_stok (barang_id, jenis, jumlah, stok_sebelum, stok_sesudah,
                             referensi_tipe, referensi_id, user_id, keterangan, created_at)
    VALUES (NEW.barang_id, 'penjualan', -NEW.jumlah, v_sebelum, v_sebelum - NEW.jumlah,
            'penjualan', NEW.penjualan_id, v_user, CONCAT('Penjualan ', v_kode), NOW());
  END IF;
END$$

-- T3. Penjualan langsung: otomatis masuk kas laci
CREATE TRIGGER trg_penjualan_ai_kas AFTER INSERT ON penjualan
FOR EACH ROW
BEGIN
  IF NEW.tipe = 'langsung' THEN
    INSERT INTO kas_laci (tanggal, waktu, keterangan, metode, jumlah, sumber_tipe, sumber_id, created_at)
    VALUES (DATE(NEW.waktu_transaksi), NEW.waktu_transaksi,
            CONCAT('Penjualan ', NEW.kode_transaksi, ' - ', NEW.nama_pelanggan),
            NEW.metode_bayar, NEW.total, 'penjualan', NEW.id, NOW());
  END IF;
END$$

-- T4. Bon: validasi pembayaran & status otomatis
CREATE TRIGGER trg_bon_piutang_bu_status BEFORE UPDATE ON bon_piutang
FOR EACH ROW
BEGIN
  IF NEW.total_terbayar > NEW.total_tagihan THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pembayaran melebihi sisa tagihan';
  END IF;
  SET NEW.status_bayar = IF(NEW.total_terbayar >= NEW.total_tagihan, 'lunas', 'belum_lunas');
END$$

-- T5. Pembayaran bon: tambah total_terbayar + masuk kas laci
CREATE TRIGGER trg_pembayaran_bon_ai AFTER INSERT ON pembayaran_bon
FOR EACH ROW
BEGIN
  DECLARE v_kode VARCHAR(20);
  DECLARE v_nama VARCHAR(120);
  SELECT bp.kode_bon, pl.nama INTO v_kode, v_nama
    FROM bon_piutang bp JOIN pelanggan pl ON pl.id = bp.pelanggan_id WHERE bp.id = NEW.bon_id;
  UPDATE bon_piutang SET total_terbayar = total_terbayar + NEW.jumlah WHERE id = NEW.bon_id;
  INSERT INTO kas_laci (tanggal, waktu, keterangan, metode, jumlah, sumber_tipe, sumber_id, created_at)
  VALUES (DATE(NEW.waktu_bayar), NEW.waktu_bayar,
          CONCAT('Pembayaran ', v_kode, ' - ', v_nama),
          NEW.metode, NEW.jumlah, 'pembayaran_bon', NEW.id, NOW());
END$$

-- T6. Pengiriman selesai wajib foto bukti
CREATE TRIGGER trg_pengiriman_bu_selesai BEFORE UPDATE ON pengiriman_barang
FOR EACH ROW
BEGIN
  IF NEW.status = 'selesai' AND OLD.status <> 'selesai' THEN
    IF NEW.foto_bukti IS NULL OR NEW.foto_bukti = '' THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Foto bukti wajib diunggah sebelum selesai';
    END IF;
    SET NEW.waktu_selesai = NOW();
  END IF;
END$$

-- T7. Admin aktif terakhir tidak boleh dinonaktifkan / diubah rolenya / dihapus
CREATE TRIGGER trg_users_bu_jaga_admin BEFORE UPDATE ON users
FOR EACH ROW
BEGIN
  DECLARE v_admin_role TINYINT UNSIGNED;
  DECLARE v_lain INT;
  SELECT id INTO v_admin_role FROM roles WHERE nama = 'admin' LIMIT 1;
  IF OLD.role_id = v_admin_role AND OLD.status = 'aktif' AND OLD.deleted_at IS NULL
     AND (NEW.status <> 'aktif' OR NEW.role_id <> OLD.role_id OR NEW.deleted_at IS NOT NULL) THEN
    SELECT COUNT(*) INTO v_lain FROM users
      WHERE role_id = v_admin_role AND status = 'aktif' AND deleted_at IS NULL AND id <> OLD.id;
    IF v_lain = 0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Minimal harus ada 1 admin aktif';
    END IF;
  END IF;
END$$

-- T8. activity_logs bersifat append-only
CREATE TRIGGER trg_activity_logs_bu BEFORE UPDATE ON activity_logs
FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'activity_logs tidak boleh diubah'$$
CREATE TRIGGER trg_activity_logs_bd BEFORE DELETE ON activity_logs
FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'activity_logs tidak boleh dihapus'$$

DELIMITER ;
