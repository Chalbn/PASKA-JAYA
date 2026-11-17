<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            CREATE TABLE penggunaan_bahan (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tanggal DATE NOT NULL,
            nama_bahan VARCHAR(150) NOT NULL,
            jumlah DECIMAL(10,2) NOT NULL,
            satuan VARCHAR(30) NOT NULL,
            harga BIGINT NOT NULL,
            keterangan VARCHAR(255),
            user_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
            )
            ');
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS penggunaan_bahan');
    }
};
