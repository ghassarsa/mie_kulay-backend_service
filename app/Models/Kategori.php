<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategoris';

    protected $fillable = [
        'jenis_hidangan'
    ];

    public $timestamps = false;

    public function menus()
    {
        return $this->hasMany(Menu::class, 'kategori_id', 'id');
    }
}
