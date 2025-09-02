<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Analisis_Keuangan extends Model
{
    protected $table = 'analisis__keuangans';
    protected $fillable = [
        'hasil_pendapatan',
        'hasil_keuntungan',
        'total_pengeluaran',
        'order_average',
        'daftar_laporan',
    ];
}
