<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE TABLE anggota_tim (
            id INT AUTO_INCREMENT PRIMARY KEY,
            inisial VARCHAR(4) NOT NULL DEFAULT '',
            nama VARCHAR(100) NOT NULL,
            jabatan VARCHAR(100) NOT NULL,
            foto VARCHAR(255),
            urutan INT NOT NULL DEFAULT 0
            )
            ");
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS anggota_tim');
    }
};
