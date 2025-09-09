<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Analisis_Makanan extends Model
{
    protected $table = 'analisis_makanans';

    protected $fillable = [
        'daftar_laporan',
        'nama_hidangan',
        'total_jumlah',
        'average_hidangan',
        'periode_bulanan',
        'periode_tahunan',
    ];
}
