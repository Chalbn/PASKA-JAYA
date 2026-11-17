<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            CREATE TABLE detail_pengiriman (
            id INT AUTO_INCREMENT PRIMARY KEY,
            pengiriman_id INT NOT NULL,
            barang_id INT NOT NULL,
            nama_barang VARCHAR(150) NOT NULL,
            satuan VARCHAR(30) NOT NULL,
            jumlah INT NOT NULL,
            FOREIGN KEY (pengiriman_id) REFERENCES pengiriman_barang(id) ON DELETE CASCADE,
            FOREIGN KEY (barang_id) REFERENCES barang(id)
            )
            ');
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS detail_pengiriman');
    }
};
