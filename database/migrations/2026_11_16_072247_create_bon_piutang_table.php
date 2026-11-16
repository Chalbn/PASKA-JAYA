<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE TABLE bon_piutang (
            id INT AUTO_INCREMENT PRIMARY KEY,
            kode_bon VARCHAR(20) UNIQUE,
            penjualan_id INT NOT NULL UNIQUE,
            pelanggan_id INT NOT NULL,
            alamat_pengantaran TEXT,
            tanggal_bon DATE NOT NULL,
            jatuh_tempo DATE NOT NULL,
            total_tagihan BIGINT NOT NULL,
            total_terbayar BIGINT NOT NULL DEFAULT 0,
            status_bayar ENUM('belum_lunas','lunas') NOT NULL DEFAULT 'belum_lunas',
            dibuat_oleh INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            deleted_at TIMESTAMP NULL,
            FOREIGN KEY (penjualan_id) REFERENCES penjualan(id) ON DELETE CASCADE,
            FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id),
            FOREIGN KEY (dibuat_oleh) REFERENCES users(id)
            )
            ");
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS bon_piutang');
    }
};
