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
            CREATE TABLE mutasi_stok (
            id INT AUTO_INCREMENT PRIMARY KEY,
            barang_id INT NOT NULL,
            jenis ENUM('stok_awal','restock','penjualan','pengiriman','batal_pengiriman','penyesuaian') NOT NULL,
            jumlah INT NOT NULL,
            stok_sebelum INT NOT NULL,
            stok_sesudah INT NOT NULL,
            referensi_tipe VARCHAR(30),
            referensi_id INT NULL,
            user_id INT NOT NULL,
            keterangan VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (barang_id) REFERENCES barang(id),
            FOREIGN KEY (user_id) REFERENCES users(id)
            )
            ");
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS mutasi_stok');
    }
};
