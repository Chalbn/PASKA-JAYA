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
            CREATE TABLE penjualan (
            id INT AUTO_INCREMENT PRIMARY KEY,
            kode_transaksi VARCHAR(20) UNIQUE,
            tipe ENUM('langsung','bon') NOT NULL,
            pelanggan_id INT NULL,
            nama_pelanggan VARCHAR(120) DEFAULT 'Umum',
            metode_bayar ENUM('cash','transfer') NULL,
            total BIGINT NOT NULL,
            user_id INT NOT NULL,
            waktu_transaksi DATETIME NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id) ON DELETE SET NULL,
            FOREIGN KEY (user_id) REFERENCES users(id)
            )
            ");
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS penjualan');
    }
};
