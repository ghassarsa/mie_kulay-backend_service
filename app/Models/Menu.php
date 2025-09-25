<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus';

    protected $fillable = [
        'nama_hidangan',
        'gambar',
        'harga_jual',
        'kategori_id',
    ];

    public function pesanan_Detail()
    {
        return $this->hasMany(Pesanan_Detail::class, 'menu_id', 'id');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id', 'id');
    }

    public function bahanMentahs()
    {
        return $this->belongsToMany(bahan_mentah::class, 'bahan_mentah_menus', 'menu_id', 'bahan_mentah_id')
            ->withPivot('jumlah');
    }
}
