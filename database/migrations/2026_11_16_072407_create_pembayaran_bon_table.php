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
            CREATE TABLE pembayaran_bon (
            id INT AUTO_INCREMENT PRIMARY KEY,
            bon_id INT NOT NULL,
            jumlah BIGINT NOT NULL,
            metode ENUM('cash','transfer') NOT NULL,
            waktu_bayar DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            user_id INT NOT NULL,
            catatan VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (bon_id) REFERENCES bon_piutang(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id)
            )
            ");
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS pembayaran_bon');
    }
};
