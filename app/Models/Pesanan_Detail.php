<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan_Detail extends Model
{
    protected $table = 'pesanan_detail';

    protected $fillable = [
        'id',
        'pesanan_id',
        'menu_id',
        'nama_hidangan',
        'jumlah',
        'harga_satuan',
        'subtotal',
    ];

    public function Pesanan()
    {
        return $this->hasMany(Pesanan::class);
    }
    public function Menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
