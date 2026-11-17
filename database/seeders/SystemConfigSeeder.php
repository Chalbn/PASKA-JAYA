<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemConfigSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('system_configs')->upsert([
            ['kunci' => 'nama_toko',         'nilai' => 'PASKA JAYA', 'deskripsi' => 'Nama toko'],
            ['kunci' => 'auto_backup',       'nilai' => '1',          'deskripsi' => 'Pencadangan otomatis'],
            ['kunci' => 'jam_backup',        'nilai' => '02:00',      'deskripsi' => 'Jam pencadangan'],
            ['kunci' => 'backup_terakhir',   'nilai' => '',           'deskripsi' => 'Pencadangan terakhir'],
            ['kunci' => 'jam_reset_laci',    'nilai' => '00:00',      'deskripsi' => 'Jam reset laci otomatis'],
            ['kunci' => 'sinkronisasi_data', 'nilai' => '1',          'deskripsi' => 'Sinkronisasi data'],
        ], ['kunci'], ['deskripsi']);
    }
}
