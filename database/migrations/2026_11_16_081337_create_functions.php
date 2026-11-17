<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Membuat 9 function MySQL untuk Paska Jaya.
     * Memakai DB::unprepared() karena DELIMITER bukan perintah SQL
     * (hanya dikenali program klien seperti phpMyAdmin / mysql CLI).
     */
    public function up(): void
    {
        // fn_kode_barang_berikutnya
        DB::unprepared('DROP FUNCTION IF EXISTS fn_kode_barang_berikutnya');
        DB::unprepared("
            CREATE FUNCTION fn_kode_barang_berikutnya()
            RETURNS VARCHAR(20)
            READS SQL DATA
            BEGIN
            DECLARE v_max INT DEFAULT 0;
            SELECT COALESCE(MAX(CAST(SUBSTRING(kode_barang, 5) AS UNSIGNED)), 0) INTO v_max FROM barang
            WHERE kode_barang REGEXP '^BRG-[0-9]+$';
            RETURN CONCAT('BRG-', LPAD(v_max + 1, 3, '0'));
            END
            ");

        // fn_kode_transaksi_berikutnya
        DB::unprepared('DROP FUNCTION IF EXISTS fn_kode_transaksi_berikutnya');
        DB::unprepared("
            CREATE FUNCTION fn_kode_transaksi_berikutnya()
            RETURNS VARCHAR(20)
            READS SQL DATA
            BEGIN
            DECLARE v_max INT DEFAULT 1000;
            SELECT COALESCE(MAX(CAST(SUBSTRING(kode_transaksi, 5) AS UNSIGNED)), 1000) INTO v_max FROM penjualan
            WHERE kode_transaksi REGEXP '^TRX-[0-9]+$';
            RETURN CONCAT('TRX-', LPAD(v_max + 1, 4, '0'));
            END
            ");

        // fn_kode_bon_berikutnya
        DB::unprepared('DROP FUNCTION IF EXISTS fn_kode_bon_berikutnya');
        DB::unprepared("
            CREATE FUNCTION fn_kode_bon_berikutnya()
            RETURNS VARCHAR(20)
            READS SQL DATA
            BEGIN
            DECLARE v_max INT DEFAULT 0;
            SELECT COALESCE(MAX(CAST(SUBSTRING(kode_bon, 5) AS UNSIGNED)), 0) INTO v_max FROM bon_piutang
            WHERE kode_bon REGEXP '^BON-[0-9]+$';
            RETURN CONCAT('BON-', LPAD(v_max + 1, 4, '0'));
            END
            ");

        // fn_saldo_laci
        DB::unprepared('DROP FUNCTION IF EXISTS fn_saldo_laci');
        DB::unprepared("
            CREATE FUNCTION fn_saldo_laci(p_metode VARCHAR(10))
            RETURNS BIGINT
            READS SQL DATA
            BEGIN
            DECLARE v_saldo BIGINT DEFAULT 0;
            SELECT COALESCE(SUM(jumlah), 0) INTO v_saldo FROM kas_laci
            WHERE penutupan_id IS NULL
                AND (p_metode IS NULL OR p_metode = '' OR metode = p_metode);
            RETURN v_saldo;
            END
            ");

        // fn_sisa_bon
        DB::unprepared('DROP FUNCTION IF EXISTS fn_sisa_bon');
        DB::unprepared('
            CREATE FUNCTION fn_sisa_bon(p_bon_id BIGINT UNSIGNED)
            RETURNS BIGINT
            READS SQL DATA
            BEGIN
            DECLARE v_sisa BIGINT DEFAULT NULL;
            SELECT CAST(total_tagihan AS SIGNED) - CAST(total_terbayar AS SIGNED) INTO v_sisa FROM bon_piutang
            WHERE id = p_bon_id;
            RETURN v_sisa;
            END
            ');

        // fn_status_bon
        DB::unprepared('DROP FUNCTION IF EXISTS fn_status_bon');
        DB::unprepared("
            CREATE FUNCTION fn_status_bon(p_bon_id BIGINT UNSIGNED)
            RETURNS VARCHAR(15)
            READS SQL DATA
            BEGIN
            DECLARE v_status VARCHAR(15) DEFAULT NULL;
            DECLARE v_tempo DATE DEFAULT NULL;
            SELECT status_bayar, jatuh_tempo INTO v_status, v_tempo FROM bon_piutang
            WHERE id = p_bon_id;
            IF v_status IS NULL THEN
                RETURN NULL;
            ELSEIF v_status = 'lunas' THEN
                RETURN 'lunas';
            ELSEIF v_tempo < CURDATE() THEN
                RETURN 'lewat_tempo';
            END IF;
            RETURN 'belum_lunas';
            END
            ");

        // fn_status_stok
        DB::unprepared('DROP FUNCTION IF EXISTS fn_status_stok');
        DB::unprepared("
            CREATE FUNCTION fn_status_stok(p_stok INT UNSIGNED, p_stok_minimum INT UNSIGNED)
            RETURNS VARCHAR(10)
            DETERMINISTIC
            BEGIN
            IF p_stok = 0 THEN
                RETURN 'habis';
            ELSEIF p_stok <= p_stok_minimum THEN
                RETURN 'menipis';
            END IF;
            RETURN 'aman';
            END
            ");

        // fn_inisial
        DB::unprepared('DROP FUNCTION IF EXISTS fn_inisial');
        DB::unprepared("
            CREATE FUNCTION fn_inisial(p_nama VARCHAR(100))
            RETURNS VARCHAR(4)
            DETERMINISTIC
            BEGIN
            DECLARE v_nama VARCHAR(100);
            DECLARE v_pos  INT;
            SET v_nama = TRIM(COALESCE(p_nama, ''));
            IF v_nama = '' THEN
                RETURN '';
            END IF;
            SET v_pos = LOCATE(' ', v_nama);
            IF v_pos > 0 THEN
                RETURN UPPER(CONCAT(LEFT(v_nama, 1), LEFT(TRIM(SUBSTRING(v_nama, v_pos + 1)), 1)));
            END IF;
            RETURN UPPER(LEFT(v_nama, 2));
            END
            ");

        // fn_slug
        DB::unprepared('DROP FUNCTION IF EXISTS fn_slug');
        DB::unprepared("
            CREATE FUNCTION fn_slug(p_nama VARCHAR(100))
            RETURNS VARCHAR(100)
            DETERMINISTIC
            BEGIN
            RETURN LOWER(TRIM(
                    REPLACE(REPLACE(REPLACE(REPLACE(TRIM(p_nama), ' & ', '-'), '&', '-'), '/', '-'), ' ', '-')
                    ));
            END
            ");

    }

    public function down(): void
    {
        DB::unprepared('DROP FUNCTION IF EXISTS fn_kode_barang_berikutnya');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_kode_transaksi_berikutnya');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_kode_bon_berikutnya');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_saldo_laci');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_sisa_bon');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_status_bon');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_status_stok');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_inisial');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_slug');
    }
};
