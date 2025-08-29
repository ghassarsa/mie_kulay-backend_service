<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'category';

    protected $fillable = [
        'nama_hidangan'
    ];

    public function Menu()
    {
        return $this->belongsTo(Menu::class, 'id', 'kategori_id');
    }
}
