-- PASKA JAYA — VIEW (MySQL 8). Jalankan SETELAH semua migration tabel selesai.
-- Catatan: dikelola lewat migration Laravel (DB::unprepared); file ini adalah referensi.

CREATE OR REPLACE VIEW v_stok_menipis AS
SELECT b.id, b.kode_barang, b.nama_barang, k.nama AS kategori, b.satuan,
       b.stok, b.stok_minimum
FROM barang b
JOIN kategori_barang k ON k.id = b.kategori_id
WHERE b.deleted_at IS NULL AND b.stok <= b.stok_minimum;

CREATE OR REPLACE VIEW v_piutang_aktif AS
SELECT bp.id AS bon_id, bp.kode_bon, p.nama AS pelanggan, bp.tanggal_bon, bp.jatuh_tempo,
       bp.total_tagihan, bp.total_terbayar,
       (bp.total_tagihan - bp.total_terbayar) AS sisa,
       (bp.jatuh_tempo < CURDATE()) AS lewat_tempo
FROM bon_piutang bp
JOIN pelanggan p ON p.id = bp.pelanggan_id
WHERE bp.deleted_at IS NULL AND bp.status_bayar = 'belum_lunas';

CREATE OR REPLACE VIEW v_penjualan_harian AS
SELECT DATE(waktu_transaksi) AS tanggal,
       COUNT(*) AS jumlah_transaksi,
       SUM(CASE WHEN metode_bayar = 'cash' THEN total ELSE 0 END) AS total_cash,
       SUM(CASE WHEN metode_bayar = 'transfer' THEN total ELSE 0 END) AS total_transfer,
       SUM(total) AS total_penjualan
FROM penjualan
WHERE tipe = 'langsung'
GROUP BY DATE(waktu_transaksi);
