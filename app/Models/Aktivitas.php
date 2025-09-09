<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aktivitas extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'aktivitas',
        'pesanan_id',
    ];
}
