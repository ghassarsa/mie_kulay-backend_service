<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite_Menu extends Model
{
    protected $fillable = [
        'nama_hidangan',
        'kategori_hidangan',
        'jumlah',
        'analisis_makanan_id',
    ];
}
