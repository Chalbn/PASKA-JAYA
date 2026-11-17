<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('
            CREATE TABLE roles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama VARCHAR(30) NOT NULL UNIQUE,      -- admin, owner, kasir, karyawan
            label VARCHAR(50) NOT NULL,
            deskripsi VARCHAR(255)
            );
        ');
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS roles');
    }
};
