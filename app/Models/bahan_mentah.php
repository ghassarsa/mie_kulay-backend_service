<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class bahan_mentah extends Model
{
    protected $fillable = [
        'nama_bahan',
        'harga_beli',
        'kategori_id',
        'stok',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'bahan_mentah_menus')
            ->withPivot('jumlah')
            ->withTimestamps();
    }
}
