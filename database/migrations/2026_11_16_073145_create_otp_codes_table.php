<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            CREATE TABLE otp_codes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            kode_hash VARCHAR(255) NOT NULL,
            kedaluwarsa_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            digunakan_pada TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
            ');
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS otp_codes');
    }
};
