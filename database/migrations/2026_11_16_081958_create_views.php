<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Membuat 19 view untuk Paska Jaya.
     * Harus dijalankan SETELAH migration tabel dan migration function
     * (view memakai fn_status_stok dan fn_inisial).
     */
    public function up(): void
    {
        // v_stok_menipis
        DB::unprepared("
            CREATE OR REPLACE VIEW v_stok_menipis AS
            SELECT
            b.id AS barang_id,
            b.kode_barang,
            b.nama_barang,
            k.nama AS kategori,
            b.satuan,
            b.stok,
            b.stok_minimum,
            CAST(b.stok_minimum AS SIGNED) - CAST(b.stok AS SIGNED) AS kekurangan,
            fn_status_stok(b.stok, b.stok_minimum) AS status_stok,
            b.foto
            FROM barang b
            JOIN kategori_barang k ON k.id = b.kategori_id
            WHERE b.deleted_at IS NULL
            AND b.stok <= b.stok_minimum
            ");

        // v_piutang_aktif
        DB::unprepared("
            CREATE OR REPLACE VIEW v_piutang_aktif AS
            SELECT
            bp.id AS bon_id,
            bp.kode_bon,
            bp.pelanggan_id,
            pl.nama AS nama_pelanggan,
            pl.no_hp,
            bp.alamat_pengantaran,
            bp.tanggal_bon,
            bp.jatuh_tempo,
            bp.total_tagihan,
            bp.total_terbayar,
            CAST(bp.total_tagihan AS SIGNED) - CAST(bp.total_terbayar AS SIGNED) AS sisa,
            (bp.jatuh_tempo < CURDATE()) AS lewat_tempo,
            GREATEST(DATEDIFF(CURDATE(), bp.jatuh_tempo), 0) AS hari_lewat_tempo,
            COALESCE(pg.status, 'belum_dikirim') AS status_pengiriman,
            u.nama AS nama_karyawan
            FROM bon_piutang bp
            JOIN pelanggan pl ON pl.id = bp.pelanggan_id
            LEFT JOIN pengiriman_barang pg ON pg.bon_id = bp.id AND pg.status <> 'dibatalkan'
            LEFT JOIN users u ON u.id = pg.karyawan_id
            WHERE bp.status_bayar = 'belum_lunas'
            AND bp.deleted_at IS NULL
            ");

        // v_penjualan_harian
        DB::unprepared("
            CREATE OR REPLACE VIEW v_penjualan_harian AS
            SELECT
            DATE(p.waktu_transaksi) AS tanggal,
            SUM(p.tipe = 'langsung') AS jumlah_transaksi,
            SUM(p.tipe = 'bon') AS jumlah_bon,
            SUM(IF(p.tipe = 'langsung' AND p.metode_bayar = 'cash', p.total, 0)) AS total_cash,
            SUM(IF(p.tipe = 'langsung' AND p.metode_bayar = 'transfer', p.total, 0)) AS total_transfer,
            SUM(IF(p.tipe = 'langsung', p.total, 0)) AS total_penjualan,
            SUM(IF(p.tipe = 'bon', p.total, 0)) AS total_bon
            FROM penjualan p
            GROUP BY DATE(p.waktu_transaksi)
            ");

        // v_pengguna_per_role
        DB::unprepared("
            CREATE OR REPLACE VIEW v_pengguna_per_role AS
            SELECT
            r.id AS role_id,
            r.nama AS role,
            r.label AS role_label,
            COUNT(u.id) AS jumlah_akun
            FROM roles r
            LEFT JOIN users u ON u.role_id = r.id AND u.deleted_at IS NULL
            GROUP BY r.id, r.nama, r.label
            ");

        // v_ringkasan_admin
        DB::unprepared("
            CREATE OR REPLACE VIEW v_ringkasan_admin AS
            SELECT
            (SELECT COUNT(*) FROM users WHERE deleted_at IS NULL) AS total_pengguna,
            (SELECT COUNT(*) FROM users WHERE deleted_at IS NULL AND status = 'aktif') AS pengguna_aktif,
            (SELECT COUNT(*) FROM barang WHERE deleted_at IS NULL) AS total_master_item,
            (SELECT COUNT(*) FROM kategori_barang) AS total_kategori,
            (SELECT COUNT(*) FROM penjualan WHERE tipe = 'langsung') AS total_transaksi_keluar,
            (SELECT COUNT(*) FROM pengiriman_barang
                WHERE status IN ('dikirim_ke_karyawan','sedang_disiapkan','siap_berangkat')) AS pengantaran_berjalan,
            (SELECT COUNT(*) FROM absensi_karyawan) AS catatan_kehadiran,
            (SELECT COUNT(*) FROM penggunaan_bahan) AS catatan_bahan_harian,
            (SELECT nilai FROM system_configs WHERE kunci = 'backup_terakhir') AS pencadangan_terakhir,
            (SELECT nilai FROM system_configs WHERE kunci = 'auto_backup') AS auto_backup
            ");

        // v_daftar_pengguna
        DB::unprepared("
            CREATE OR REPLACE VIEW v_daftar_pengguna AS
            SELECT
            u.id AS user_id,
            u.nama,
            fn_inisial(u.nama) AS inisial,
            u.email,
            u.no_hp,
            u.role_id,
            r.nama AS role,
            r.label AS role_label,
            u.status,
            u.created_at AS dibuat_pada
            FROM users u
            JOIN roles r ON r.id = u.role_id
            WHERE u.deleted_at IS NULL
            ");

        // v_anggota_tim
        DB::unprepared("
            CREATE OR REPLACE VIEW v_anggota_tim AS
            SELECT id, inisial, nama, jabatan, foto, urutan
            FROM anggota_tim
            ");

        // v_master_item
        DB::unprepared("
            CREATE OR REPLACE VIEW v_master_item AS
            SELECT
            b.id AS barang_id,
            b.kode_barang,
            b.nama_barang,
            b.kategori_id,
            k.nama AS kategori,
            k.slug AS kategori_slug,
            b.satuan,
            b.harga_beli,
            b.harga_jual,
            b.stok,
            b.stok_minimum,
            fn_status_stok(b.stok, b.stok_minimum) AS status_stok,
            b.stok * b.harga_beli AS nilai_stok,
            b.foto,
            b.tanggal_masuk_terakhir
            FROM barang b
            JOIN kategori_barang k ON k.id = b.kategori_id
            WHERE b.deleted_at IS NULL
            ");

        // v_ringkasan_master_item
        DB::unprepared("
            CREATE OR REPLACE VIEW v_ringkasan_master_item AS
            SELECT
            COUNT(*) AS total_barang,
            COUNT(DISTINCT b.kategori_id) AS jumlah_kategori,
            COALESCE(SUM(b.stok * b.harga_beli), 0) AS nilai_stok,
            COALESCE(SUM(b.stok <= b.stok_minimum), 0) AS stok_menipis,
            COALESCE(SUM(b.stok >  b.stok_minimum), 0) AS stok_aman
            FROM barang b
            WHERE b.deleted_at IS NULL
            ");

        // v_ringkasan_toko
        DB::unprepared("
            CREATE OR REPLACE VIEW v_ringkasan_toko AS
            SELECT
            (SELECT COALESCE(SUM(total), 0) FROM penjualan
                WHERE tipe = 'langsung'
                AND waktu_transaksi >= CURDATE()
                AND waktu_transaksi <  CURDATE() + INTERVAL 1 DAY) AS penjualan_hari_ini,
            (SELECT COUNT(*) FROM penjualan
                WHERE tipe = 'langsung'
                AND waktu_transaksi >= CURDATE()
                AND waktu_transaksi <  CURDATE() + INTERVAL 1 DAY) AS transaksi_hari_ini,
            (SELECT COALESCE(SUM(jumlah), 0) FROM kas_laci
                WHERE penutupan_id IS NULL AND metode = 'cash') AS saldo_cash,
            (SELECT COALESCE(SUM(jumlah), 0) FROM kas_laci
                WHERE penutupan_id IS NULL AND metode = 'transfer') AS saldo_transfer,
            (SELECT COUNT(*) FROM barang
                WHERE deleted_at IS NULL AND stok <= stok_minimum) AS stok_menipis,
            (SELECT COUNT(*) FROM barang
                WHERE deleted_at IS NULL AND stok >  stok_minimum) AS stok_aman,
            (SELECT COALESCE(SUM(stok * harga_beli), 0) FROM barang
                WHERE deleted_at IS NULL) AS nilai_stok,
            (SELECT COUNT(*) FROM bon_piutang
                WHERE status_bayar = 'belum_lunas' AND deleted_at IS NULL) AS bon_belum_lunas,
            (SELECT COUNT(DISTINCT pelanggan_id) FROM bon_piutang
                WHERE status_bayar = 'belum_lunas' AND deleted_at IS NULL) AS pelanggan_berutang,
            (SELECT COALESCE(SUM(CAST(total_tagihan AS SIGNED) - CAST(total_terbayar AS SIGNED)), 0)
                FROM bon_piutang
                WHERE status_bayar = 'belum_lunas' AND deleted_at IS NULL)  AS total_sisa_bon
            ");

        // v_laporan_barang_keluar
        DB::unprepared("
            CREATE OR REPLACE VIEW v_laporan_barang_keluar AS
            SELECT
            p.id AS penjualan_id,
            p.kode_transaksi,
            p.waktu_transaksi,
            p.nama_pelanggan,
            p.metode_bayar,
            p.total,
            u.nama AS kasir,
            (SELECT GROUP_CONCAT(CONCAT(d.nama_barang, ' x', d.jumlah) ORDER BY d.id SEPARATOR ', ')
                FROM detail_penjualan d
                WHERE d.penjualan_id = p.id) AS ringkasan_barang
            FROM penjualan p
            JOIN users u ON u.id = p.user_id
            WHERE p.tipe = 'langsung'
            ");

        // v_daftar_bon
        DB::unprepared("
            CREATE OR REPLACE VIEW v_daftar_bon AS
            SELECT
            bp.id AS bon_id,
            bp.kode_bon,
            bp.penjualan_id,
            bp.pelanggan_id,
            pl.nama AS nama_pelanggan,
            pl.no_hp,
            bp.alamat_pengantaran,
            bp.tanggal_bon,
            bp.jatuh_tempo,
            bp.total_tagihan,
            bp.total_terbayar,
            CAST(bp.total_tagihan AS SIGNED) - CAST(bp.total_terbayar AS SIGNED) AS sisa,
            bp.status_bayar,
            (bp.status_bayar = 'belum_lunas' AND bp.jatuh_tempo < CURDATE()) AS lewat_tempo,
            pg.id AS pengiriman_id,
            COALESCE(pg.status, 'belum_dikirim') AS status_pengiriman,
            pg.karyawan_id,
            kw.nama AS nama_karyawan,
            pg.jadwal_kirim,
            pg.catatan_kasir,
            pg.foto_bukti,
            pg.waktu_selesai,
            (SELECT GROUP_CONCAT(CONCAT(d.nama_barang, ' x', d.jumlah) ORDER BY d.id SEPARATOR ', ')
                FROM detail_penjualan d
                WHERE d.penjualan_id = bp.penjualan_id) AS ringkasan_barang,
            bp.dibuat_oleh,
            bp.created_at
            FROM bon_piutang bp
            JOIN pelanggan pl ON pl.id = bp.pelanggan_id
            LEFT JOIN pengiriman_barang pg ON pg.bon_id = bp.id AND pg.status <> 'dibatalkan'
            LEFT JOIN users kw ON kw.id = pg.karyawan_id
            WHERE bp.deleted_at IS NULL
            ");

        // v_saldo_laci_aktif
        DB::unprepared("
            CREATE OR REPLACE VIEW v_saldo_laci_aktif AS
            SELECT
            COALESCE(SUM(IF(metode = 'cash', jumlah, 0)), 0) AS saldo_cash,
            COALESCE(SUM(IF(metode = 'transfer', jumlah, 0)), 0) AS saldo_transfer,
            COALESCE(SUM(jumlah), 0) AS saldo_total,
            COUNT(*) AS jumlah_transaksi
            FROM kas_laci
            WHERE penutupan_id IS NULL
            ");

        // v_kas_laci_aktif
        DB::unprepared("
            CREATE OR REPLACE VIEW v_kas_laci_aktif AS
            SELECT id AS kas_id, tanggal, waktu, keterangan, metode, jumlah, sumber_tipe, sumber_id
            FROM kas_laci
            WHERE penutupan_id IS NULL
            ");

        // v_riwayat_saldo_laci
        DB::unprepared("
            CREATE OR REPLACE VIEW v_riwayat_saldo_laci AS
            SELECT
            pt.id AS penutupan_id,
            pt.tanggal,
            pt.total_cash,
            pt.total_transfer,
            pt.total,
            pt.waktu_reset,
            pt.tipe_reset,
            COALESCE(u.nama, 'Sistem') AS direset_oleh
            FROM penutupan_laci pt
            LEFT JOIN users u ON u.id = pt.direset_oleh
            ");

        // v_bahan_harian
        DB::unprepared("
            CREATE OR REPLACE VIEW v_bahan_harian AS
            SELECT
            pb.id AS bahan_id,
            pb.tanggal,
            pb.nama_bahan,
            pb.jumlah,
            pb.satuan,
            pb.harga,
            pb.keterangan,
            pb.user_id,
            u.nama AS pencatat
            FROM penggunaan_bahan pb
            JOIN users u ON u.id = pb.user_id
            ");

        // v_kehadiran_karyawan
        DB::unprepared("
            CREATE OR REPLACE VIEW v_kehadiran_karyawan AS
            SELECT
            a.id AS absensi_id,
            a.karyawan_id,
            k.nama AS nama_karyawan,
            a.tanggal,
            a.shift,
            (SELECT GROUP_CONCAT(d.deskripsi ORDER BY d.urutan, d.id SEPARATOR ' | ')
                FROM detail_pekerjaan_absensi d
                WHERE d.absensi_id = a.id) AS pekerjaan,
            a.dibuat_oleh,
            c.nama AS dicatat_oleh
            FROM absensi_karyawan a
            JOIN users k ON k.id = a.karyawan_id
            JOIN users c ON c.id = a.dibuat_oleh
            ");

        // v_pengantaran_karyawan
        DB::unprepared("
            CREATE OR REPLACE VIEW v_pengantaran_karyawan AS
            SELECT
            pg.id AS pengiriman_id,
            pg.bon_id,
            bp.kode_bon,
            pg.karyawan_id,
            pl.nama AS nama_pelanggan,
            bp.alamat_pengantaran,
            pg.status,
            pg.jadwal_kirim,
            pg.catatan_kasir,
            pg.foto_bukti,
            pg.dibaca_pada,
            (pg.dibaca_pada IS NULL)  AS is_baru,
            pg.waktu_selesai,
            pg.created_at AS dikirim_pada,
            (SELECT GROUP_CONCAT(CONCAT(dp.nama_barang, ' ', dp.jumlah, ' ', dp.satuan) ORDER BY dp.id SEPARATOR ', ')
                FROM detail_pengiriman dp
                WHERE dp.pengiriman_id = pg.id) AS daftar_barang
            FROM pengiriman_barang pg
            JOIN bon_piutang bp ON bp.id = pg.bon_id
            JOIN pelanggan pl ON pl.id = bp.pelanggan_id
            WHERE pg.status <> 'dibatalkan'
            ");

        // v_ringkasan_pengantaran
        DB::unprepared("
            CREATE OR REPLACE VIEW v_ringkasan_pengantaran AS
            SELECT
            pg.karyawan_id,
            SUM(pg.dibaca_pada IS NULL AND pg.status <> 'selesai') AS pengantaran_baru,
            SUM(pg.status IN ('dikirim_ke_karyawan','sedang_disiapkan','siap_berangkat')) AS sedang_berjalan,
            SUM(pg.status = 'selesai') AS selesai
            FROM pengiriman_barang pg
            WHERE pg.status <> 'dibatalkan'
            AND pg.karyawan_id IS NOT NULL
            GROUP BY pg.karyawan_id
            ");

    }

    public function down(): void
    {
        DB::unprepared('DROP VIEW IF EXISTS v_ringkasan_pengantaran');
        DB::unprepared('DROP VIEW IF EXISTS v_pengantaran_karyawan');
        DB::unprepared('DROP VIEW IF EXISTS v_kehadiran_karyawan');
        DB::unprepared('DROP VIEW IF EXISTS v_bahan_harian');
        DB::unprepared('DROP VIEW IF EXISTS v_riwayat_saldo_laci');
        DB::unprepared('DROP VIEW IF EXISTS v_kas_laci_aktif');
        DB::unprepared('DROP VIEW IF EXISTS v_saldo_laci_aktif');
        DB::unprepared('DROP VIEW IF EXISTS v_daftar_bon');
        DB::unprepared('DROP VIEW IF EXISTS v_laporan_barang_keluar');
        DB::unprepared('DROP VIEW IF EXISTS v_ringkasan_toko');
        DB::unprepared('DROP VIEW IF EXISTS v_ringkasan_master_item');
        DB::unprepared('DROP VIEW IF EXISTS v_master_item');
        DB::unprepared('DROP VIEW IF EXISTS v_anggota_tim');
        DB::unprepared('DROP VIEW IF EXISTS v_daftar_pengguna');
        DB::unprepared('DROP VIEW IF EXISTS v_ringkasan_admin');
        DB::unprepared('DROP VIEW IF EXISTS v_pengguna_per_role');
        DB::unprepared('DROP VIEW IF EXISTS v_penjualan_harian');
        DB::unprepared('DROP VIEW IF EXISTS v_piutang_aktif');
        DB::unprepared('DROP VIEW IF EXISTS v_stok_menipis');
    }
};