<?php

namespace App\Models;

use Database\Factories\AnggotaTimFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnggotaTim extends Model
{
    /** @use HasFactory<AnggotaTimFactory> */
    use HasFactory;

    protected $table = 'anggota_tim';

    public $timestamps = false;

    protected $fillable = [
        'inisial',
        'nama',
        'jabatan',
        'foto',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
        ];
    }
}
