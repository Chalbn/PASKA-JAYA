<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE TABLE pengiriman_barang (
            id INT AUTO_INCREMENT PRIMARY KEY,
            bon_id INT NOT NULL,
            karyawan_id INT NULL,
            status ENUM('dikirim_ke_karyawan','sedang_disiapkan','siap_berangkat','selesai','dibatalkan')
                     NOT NULL DEFAULT 'dikirim_ke_karyawan',
            jadwal_kirim DATETIME NULL,
            catatan_kasir TEXT,
            foto_bukti VARCHAR(255),
            dibaca_pada TIMESTAMP NULL,
            waktu_selesai DATETIME NULL,
            dibuat_oleh INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (bon_id) REFERENCES bon_piutang(id) ON DELETE CASCADE,
            FOREIGN KEY (karyawan_id) REFERENCES users(id) ON DELETE SET NULL,
            FOREIGN KEY (dibuat_oleh) REFERENCES users(id)
            )
            ");
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS pengiriman_barang');
    }
};
