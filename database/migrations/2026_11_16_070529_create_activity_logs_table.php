<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            CREATE TABLE activity_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            aksi VARCHAR(50)  NOT NULL,
            modul VARCHAR(50)  NOT NULL,
            target_id INT NULL,
            deskripsi VARCHAR(255) NOT NULL,
            data_lama JSON NULL,
            data_baru JSON NULL,
            ip_address VARCHAR(45),
            user_agent VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            )
            ');
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS activity_logs');
    }
};
