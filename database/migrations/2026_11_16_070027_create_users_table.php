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
            CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            role_id INT NOT NULL,
            nama VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            no_hp VARCHAR(20) UNIQUE,
            password VARCHAR(255) NOT NULL,
            status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
            alamat TEXT,
            foto_profil VARCHAR(255),
            email_verified_at TIMESTAMP NULL,
            remember_token VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            deleted_at TIMESTAMP NULL,
            FOREIGN KEY (role_id) REFERENCES roles(id)
            )
            ");
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS users');
    }
};
