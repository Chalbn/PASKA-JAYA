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
            CREATE TABLE barang (
            id INT AUTO_INCREMENT PRIMARY KEY,
            kode_barang VARCHAR(20) UNIQUE,
            kategori_id INT NOT NULL,
            nama_barang VARCHAR(150) NOT NULL,
            satuan VARCHAR(30) NOT NULL,
            harga_beli BIGINT NOT NULL,
            harga_jual BIGINT NOT NULL,
            stok INT NOT NULL DEFAULT 0,
            stok_minimum INT NOT NULL DEFAULT 10,
            foto VARCHAR(255),
            tanggal_masuk_terakhir DATE NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            deleted_at TIMESTAMP NULL,
            FOREIGN KEY (kategori_id) REFERENCES kategori_barang(id)
            )
            ");
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS barang');
    }
};
