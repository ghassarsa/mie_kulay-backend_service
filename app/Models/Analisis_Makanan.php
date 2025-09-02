<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Analisis_Makanan extends Model
{
    protected $table = 'analisis_makanans';

    protected $fillable = [
        'daftar_laporan',
        'menu_id',
        'kategori_id',
        'total_jumlah',
        'average_per_pesanan',
    ];
}
