<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';

    protected $fillable = [
        'id',
        'nama_hidangan',
        'kategori_id',
    ];

    public function Pesanan_Detail()
    {
        return $this->hasOne(Pesanan_Detail::class, 'menu_id', 'id');
    }

    public function Category()
    {
        return $this->hasOne(Category::class, 'id', 'kategori_id');
    }
}
