<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pendapatan_Bahan_Lengkap extends Model
{
    protected $table = 'pendapatan_bahan_lengkaps';

    protected $fillable = [
        'daftar_laporan',
        'hasil_pendapatan',
    ];
}
