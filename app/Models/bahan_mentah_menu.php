<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class bahan_mentah_menu extends Model
{
    protected $fillable = [
        'bahan_mentah_id',
        'menu_id',
        'jumlah',
    ];
}
