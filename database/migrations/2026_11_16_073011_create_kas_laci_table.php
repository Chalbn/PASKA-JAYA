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
            CREATE TABLE kas_laci (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tanggal DATE NOT NULL,
            waktu DATETIME NOT NULL,
            keterangan VARCHAR(255) NOT NULL,
            metode ENUM('cash','transfer') NOT NULL,
            jumlah BIGINT NOT NULL,
            sumber_tipe ENUM('penjualan','pembayaran_bon') NOT NULL,
            sumber_id INT NOT NULL,
            penutupan_id INT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (penutupan_id) REFERENCES penutupan_laci(id) ON DELETE SET NULL
            )
            ");
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS kas_laci');
    }
};
