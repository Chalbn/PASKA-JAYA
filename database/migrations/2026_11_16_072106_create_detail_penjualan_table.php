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
            CREATE TABLE detail_penjualan (
            id INT AUTO_INCREMENT PRIMARY KEY,
            penjualan_id INT NOT NULL,
            barang_id INT NOT NULL,
            nama_barang VARCHAR(150) NOT NULL,
            satuan VARCHAR(30) NOT NULL,
            jumlah INT NOT NULL,
            harga_satuan BIGINT NOT NULL,
            subtotal BIGINT NOT NULL,
            UNIQUE (penjualan_id, barang_id),
            FOREIGN KEY (penjualan_id) REFERENCES penjualan(id) ON DELETE CASCADE,
            FOREIGN KEY (barang_id) REFERENCES barang(id)
            )
            ");
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS detail_penjualan');
    }
};