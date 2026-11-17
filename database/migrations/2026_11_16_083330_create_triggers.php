<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Membuat 19 trigger Paska Jaya.
     * Jalankan SETELAH migration tabel dan function (trigger memanggil fn_kode_*, fn_slug, fn_inisial).
     * DELIMITER tidak dipakai karena bukan perintah SQL.
     */
    public function up(): void
    {
        // trg_t1_barang_bu_cek_stok
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t1_barang_bu_cek_stok');
        DB::unprepared("
            CREATE TRIGGER trg_t1_barang_bu_cek_stok
            BEFORE UPDATE ON barang
            FOR EACH ROW
            BEGIN
            IF CAST(NEW.stok AS SIGNED) < 0 THEN
                SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Stok barang tidak boleh kurang dari 0';
            END IF;
            END
            ");

        // trg_t2_detail_penjualan_ai_potong_stok
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t2_detail_penjualan_ai_potong_stok');
        DB::unprepared("
            CREATE TRIGGER trg_t2_detail_penjualan_ai_potong_stok
            AFTER INSERT ON detail_penjualan
            FOR EACH ROW
            BEGIN
            DECLARE v_tipe VARCHAR(10);
            DECLARE v_user BIGINT UNSIGNED;
            DECLARE v_kode VARCHAR(20);
            DECLARE v_stok INT UNSIGNED;

            SELECT tipe, user_id, kode_transaksi INTO v_tipe, v_user, v_kode FROM penjualan
            WHERE id = NEW.penjualan_id;

            IF v_tipe = 'langsung' THEN
                SELECT stok INTO v_stok
                FROM barang
                WHERE id = NEW.barang_id
                FOR UPDATE;

                IF v_stok IS NULL OR v_stok < NEW.jumlah THEN
                SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Stok tidak mencukupi untuk barang yang dijual';
                END IF;

                UPDATE barang
                SET stok = stok - NEW.jumlah
                WHERE id = NEW.barang_id;

                INSERT INTO mutasi_stok (barang_id, jenis, jumlah, stok_sebelum, stok_sesudah, referensi_tipe, referensi_id, user_id, keterangan)
                VALUES (NEW.barang_id, 'penjualan', 0 - CAST(NEW.jumlah AS SIGNED), v_stok, v_stok - NEW.jumlah,
                'penjualan', NEW.penjualan_id, v_user, CONCAT('Penjualan ', COALESCE(v_kode, '')));
            END IF;
            END
            ");

        // trg_t3_penjualan_ai_kas_laci
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t3_penjualan_ai_kas_laci');
        DB::unprepared("
            CREATE TRIGGER trg_t3_penjualan_ai_kas_laci
            AFTER INSERT ON penjualan
            FOR EACH ROW
            BEGIN
            IF NEW.tipe = 'langsung' AND NEW.metode_bayar IS NOT NULL THEN
                INSERT INTO kas_laci(tanggal, waktu, keterangan, metode, jumlah, sumber_tipe, sumber_id)
                VALUES (DATE(NEW.waktu_transaksi), NEW.waktu_transaksi,
                CONCAT('Penjualan ', COALESCE(NEW.kode_transaksi, ''), ' - ', COALESCE(NEW.nama_pelanggan, 'Umum')),
                NEW.metode_bayar, NEW.total, 'penjualan', NEW.id);
            END IF;
            END
            ");

        // trg_t4_bon_piutang_bi
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t4_bon_piutang_bi');
        DB::unprepared("
            CREATE TRIGGER trg_t4_bon_piutang_bi
            BEFORE INSERT ON bon_piutang
            FOR EACH ROW
            BEGIN
            IF NEW.kode_bon IS NULL OR NEW.kode_bon = '' THEN
                SET NEW.kode_bon = fn_kode_bon_berikutnya();
            END IF;
            IF NEW.total_terbayar >= NEW.total_tagihan THEN
                SET NEW.status_bayar = 'lunas';
            ELSE
                SET NEW.status_bayar = 'belum_lunas';
            END IF;
            END
            ");

        // trg_t4_bon_piutang_bu
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t4_bon_piutang_bu');
        DB::unprepared("
            CREATE TRIGGER trg_t4_bon_piutang_bu
            BEFORE UPDATE ON bon_piutang
            FOR EACH ROW
            BEGIN
            IF NEW.total_terbayar >= NEW.total_tagihan THEN
                SET NEW.status_bayar = 'lunas';
            ELSE
                SET NEW.status_bayar = 'belum_lunas';
            END IF;
            END
            ");

        // trg_t5_pembayaran_bon_bi
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t5_pembayaran_bon_bi');
        DB::unprepared("
            CREATE TRIGGER trg_t5_pembayaran_bon_bi
            BEFORE INSERT ON pembayaran_bon
            FOR EACH ROW
            BEGIN
            DECLARE v_sisa BIGINT;
            SELECT CAST(total_tagihan AS SIGNED) - CAST(total_terbayar AS SIGNED) INTO v_sisa FROM bon_piutang
            WHERE id = NEW.bon_id AND deleted_at IS NULL;

            IF v_sisa IS NULL THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Bon tidak ditemukan';
            END IF;
            IF NEW.jumlah > v_sisa THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Jumlah pembayaran melebihi sisa tagihan bon';
            END IF;
            END
            ");

        // trg_t5_pembayaran_bon_ai
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t5_pembayaran_bon_ai');
        DB::unprepared("
            CREATE TRIGGER trg_t5_pembayaran_bon_ai
            AFTER INSERT ON pembayaran_bon
            FOR EACH ROW
            BEGIN
            DECLARE v_kode  VARCHAR(20);
            DECLARE v_nama  VARCHAR(120);

            UPDATE bon_piutang SET total_terbayar = total_terbayar + NEW.jumlah
            WHERE id = NEW.bon_id;

            SELECT bp.kode_bon, pl.nama
                INTO v_kode, v_nama
                FROM bon_piutang bp
                JOIN pelanggan pl ON pl.id = bp.pelanggan_id
            WHERE bp.id = NEW.bon_id;

            INSERT INTO kas_laci (tanggal, waktu, keterangan, metode, jumlah, sumber_tipe, sumber_id)
            VALUES (DATE(NEW.waktu_bayar), NEW.waktu_bayar,
                CONCAT('Pembayaran ', COALESCE(v_kode, ''), ' - ', COALESCE(v_nama, '')),
                NEW.metode, NEW.jumlah, 'pembayaran_bon', NEW.id);
            END
            ");

        // trg_t6_pengiriman_bu_wajib_foto
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t6_pengiriman_bu_wajib_foto');
        DB::unprepared("
            CREATE TRIGGER trg_t6_pengiriman_bu_wajib_foto
            BEFORE UPDATE ON pengiriman_barang
            FOR EACH ROW
            BEGIN
            IF NEW.status = 'selesai' THEN
                IF NEW.foto_bukti IS NULL OR TRIM(NEW.foto_bukti) = '' THEN
                SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Foto bukti wajib diunggah sebelum pengiriman ditandai selesai';
                END IF;
                IF NEW.waktu_selesai IS NULL THEN
                SET NEW.waktu_selesai = NOW();
                END IF;
            END IF;
            END
            ");

        // trg_t7_users_bu_jaga_admin
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t7_users_bu_jaga_admin');
        DB::unprepared("
            CREATE TRIGGER trg_t7_users_bu_jaga_admin
            BEFORE UPDATE ON users
            FOR EACH ROW
            BEGIN
            DECLARE v_admin_id TINYINT UNSIGNED;
            DECLARE v_admin_lain INT DEFAULT 0;

            SELECT id INTO v_admin_id FROM roles WHERE nama = 'admin' LIMIT 1;

            IF OLD.role_id = v_admin_id
                AND OLD.status = 'aktif'
                AND OLD.deleted_at IS NULL
                AND (NEW.role_id <> OLD.role_id OR NEW.status <> 'aktif' OR NEW.deleted_at IS NOT NULL) THEN

                SELECT COUNT(*) INTO v_admin_lain FROM users
                WHERE role_id = v_admin_id
                AND status = 'aktif'
                AND deleted_at IS NULL
                AND id <> OLD.id;

                IF v_admin_lain = 0 THEN
                SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Admin terakhir tidak boleh diubah role-nya, dinonaktifkan, atau dihapus';
                END IF;
            END IF;
            END
            ");

        // trg_t7_users_bd_jaga_admin
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t7_users_bd_jaga_admin');
        DB::unprepared("
            CREATE TRIGGER trg_t7_users_bd_jaga_admin
            BEFORE DELETE ON users
            FOR EACH ROW
            BEGIN
            DECLARE v_admin_id TINYINT UNSIGNED;
            DECLARE v_admin_lain INT DEFAULT 0;

            SELECT id INTO v_admin_id FROM roles WHERE nama = 'admin' LIMIT 1;

            IF OLD.role_id = v_admin_id AND OLD.status = 'aktif' AND OLD.deleted_at IS NULL THEN
                SELECT COUNT(*) INTO v_admin_lain
                FROM users
                WHERE role_id = v_admin_id
                AND status = 'aktif'
                AND deleted_at IS NULL
                AND id <> OLD.id;

                IF v_admin_lain = 0 THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Admin terakhir tidak boleh dihapus';
                END IF;
            END IF;
            END
            ");

        // trg_t8_activity_logs_bu
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t8_activity_logs_bu');
        DB::unprepared("
            CREATE TRIGGER trg_t8_activity_logs_bu
            BEFORE UPDATE ON activity_logs
            FOR EACH ROW
            BEGIN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'activity_logs bersifat append-only: data tidak boleh diubah';
            END
            ");

        // trg_t8_activity_logs_bd
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t8_activity_logs_bd');
        DB::unprepared("
            CREATE TRIGGER trg_t8_activity_logs_bd
            BEFORE DELETE ON activity_logs
            FOR EACH ROW
            BEGIN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'activity_logs bersifat append-only: data tidak boleh dihapus';
            END
            ");

        // trg_t9_barang_bi_kode
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t9_barang_bi_kode');
        DB::unprepared("
            CREATE TRIGGER trg_t9_barang_bi_kode
            BEFORE INSERT ON barang
            FOR EACH ROW
            BEGIN
            IF NEW.kode_barang IS NULL OR TRIM(NEW.kode_barang) = '' THEN
                SET NEW.kode_barang = fn_kode_barang_berikutnya();
            END IF;
            END
            ");

        // trg_t10_penjualan_bi_kode
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t10_penjualan_bi_kode');
        DB::unprepared("
            CREATE TRIGGER trg_t10_penjualan_bi_kode
            BEFORE INSERT ON penjualan
            FOR EACH ROW
            BEGIN
            IF NEW.kode_transaksi IS NULL OR TRIM(NEW.kode_transaksi) = '' THEN
                SET NEW.kode_transaksi = fn_kode_transaksi_berikutnya();
            END IF;
            END
            ");

        // trg_t11_detail_penjualan_bi_subtotal
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t11_detail_penjualan_bi_subtotal');
        DB::unprepared('
            CREATE TRIGGER trg_t11_detail_penjualan_bi_subtotal
            BEFORE INSERT ON detail_penjualan
            FOR EACH ROW
            BEGIN
            SET NEW.subtotal = NEW.jumlah * NEW.harga_satuan;
            END
            ');

        // trg_t12_bon_piutang_ai_log_awal
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t12_bon_piutang_ai_log_awal');
        DB::unprepared("
            CREATE TRIGGER trg_t12_bon_piutang_ai_log_awal
            AFTER INSERT ON bon_piutang
            FOR EACH ROW
            BEGIN
            INSERT INTO log_status_pengiriman (bon_id, pengiriman_id, status, user_id, catatan)
            VALUES (NEW.id, NULL, 'belum_dikirim', NEW.dibuat_oleh, 'Bon dicatat');
            END
            ");

        // trg_t13_kategori_bi_slug
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t13_kategori_bi_slug');
        DB::unprepared("
            CREATE TRIGGER trg_t13_kategori_bi_slug
            BEFORE INSERT ON kategori_barang
            FOR EACH ROW
            BEGIN
            IF NEW.slug IS NULL OR TRIM(NEW.slug) = '' THEN
                SET NEW.slug = fn_slug(NEW.nama);
            END IF;
            END
            ");

        // trg_t14_anggota_tim_bi
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t14_anggota_tim_bi');
        DB::unprepared("
            CREATE TRIGGER trg_t14_anggota_tim_bi
            BEFORE INSERT ON anggota_tim
            FOR EACH ROW
            BEGIN
            IF NEW.inisial IS NULL OR TRIM(NEW.inisial) = '' THEN
                SET NEW.inisial = fn_inisial(NEW.nama);
            END IF;
            END
            ");

        // trg_t14_anggota_tim_bu
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t14_anggota_tim_bu');
        DB::unprepared('
            CREATE TRIGGER trg_t14_anggota_tim_bu
            BEFORE UPDATE ON anggota_tim
            FOR EACH ROW
            BEGIN
            IF NEW.nama <> OLD.nama THEN
                SET NEW.inisial = fn_inisial(NEW.nama);
            END IF;
            END
            ');

    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t1_barang_bu_cek_stok');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t2_detail_penjualan_ai_potong_stok');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t3_penjualan_ai_kas_laci');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t4_bon_piutang_bi');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t4_bon_piutang_bu');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t5_pembayaran_bon_bi');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t5_pembayaran_bon_ai');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t6_pengiriman_bu_wajib_foto');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t7_users_bu_jaga_admin');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t7_users_bd_jaga_admin');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t8_activity_logs_bu');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t8_activity_logs_bd');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t9_barang_bi_kode');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t10_penjualan_bi_kode');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t11_detail_penjualan_bi_subtotal');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t12_bon_piutang_ai_log_awal');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t13_kategori_bi_slug');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t14_anggota_tim_bi');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t14_anggota_tim_bu');
    }
};
