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
            CREATE TABLE system_configs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            kunci VARCHAR(100) NOT NULL UNIQUE,
            nilai TEXT NOT NULL,
            deskripsi VARCHAR(255),
            updated_by INT NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
            )
            ");
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS system_configs');
    }
};
