<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            CREATE TABLE detail_pekerjaan_absensi (
            id INT AUTO_INCREMENT PRIMARY KEY,
            absensi_id INT NOT NULL,
            deskripsi VARCHAR(150) NOT NULL,
            urutan INT NOT NULL DEFAULT 1,
            FOREIGN KEY (absensi_id) REFERENCES absensi_karyawan(id) ON DELETE CASCADE
            )
            ');
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS detail_pekerjaan_absensi');
    }
};
