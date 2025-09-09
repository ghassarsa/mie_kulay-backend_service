<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus';

    protected $fillable = [
        'nama_hidangan',
        'gambar',
        'harga_pokok',
        'harga_jual',
        'stok',
        'kategori_id',
    ];

    public function pesanan_Detail()
    {
        return $this->hasOne(Pesanan_Detail::class, 'menu_id', 'id');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id', 'id');
    }
}
