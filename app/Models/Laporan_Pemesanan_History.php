<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan_Pemesanan_History extends Model
{
    protected $fillable = [
        'pesanan_id',
        'daftar_laporan',
    ];
}
