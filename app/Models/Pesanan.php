<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanans';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'total_pesanan',
        'pembayaran',
    ];

    public function pesananDetail()
    {
        return $this->hasMany(Pesanan_Detail::class, 'pesanan_id');
    }
}
