<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE TABLE absensi_karyawan (
            id INT AUTO_INCREMENT PRIMARY KEY,
            karyawan_id INT NOT NULL,
            tanggal DATE NOT NULL,
            shift ENUM('pagi','siang','full') NULL,
            dibuat_oleh INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE (karyawan_id, tanggal),
            FOREIGN KEY (karyawan_id) REFERENCES users(id),
            FOREIGN KEY (dibuat_oleh) REFERENCES users(id)
            )
            ");
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS absensi_karyawan');
    }
};
