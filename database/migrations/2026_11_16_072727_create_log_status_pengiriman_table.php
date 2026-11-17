<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE TABLE log_status_pengiriman (
            id INT AUTO_INCREMENT PRIMARY KEY,
            bon_id INT NOT NULL,
            pengiriman_id INT NULL,
            status ENUM('belum_dikirim','dikirim_ke_karyawan','sedang_disiapkan','siap_berangkat','selesai') NOT NULL,
            user_id INT NULL,
            catatan VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (bon_id) REFERENCES bon_piutang(id) ON DELETE CASCADE,
            FOREIGN KEY (pengiriman_id) REFERENCES pengiriman_barang(id) ON DELETE SET NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            )
            ");
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS log_status_pengiriman');
    }
};
