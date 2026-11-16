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
            CREATE TABLE kategori_barang (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama VARCHAR(80) NOT NULL UNIQUE,
            slug VARCHAR(100) UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
            ");
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS kategori_barang');
    }
};