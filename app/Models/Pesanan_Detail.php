<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan_Detail extends Model
{
    protected $table = 'pesanan__details';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'pesanan_id',
        'menu_id',
        'nama_hidangan',
        'jumlah',
        'harga_satuan',
        'subtotal',
        'status',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id', 'id');
    }
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }
}
