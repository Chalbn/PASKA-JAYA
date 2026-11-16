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
            CREATE TABLE penutupan_laci (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tanggal DATE NOT NULL,
            total_cash BIGINT NOT NULL,
            total_transfer BIGINT NOT NULL,
            total BIGINT NOT NULL,
            waktu_reset DATETIME NOT NULL,
            direset_oleh INT NULL,
            tipe_reset ENUM('otomatis','manual') NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (direset_oleh) REFERENCES users(id) ON DELETE SET NULL
            )
            ");
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS penutupan_laci');
    }
};
