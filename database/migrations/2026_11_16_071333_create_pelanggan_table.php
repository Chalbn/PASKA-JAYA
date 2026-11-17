<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            CREATE TABLE pelanggan (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama VARCHAR(120) NOT NULL,
            no_hp VARCHAR(20),
            alamat TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
            ');
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS pelanggan');
    }
};
