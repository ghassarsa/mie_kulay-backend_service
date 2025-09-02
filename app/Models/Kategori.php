<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategoris'; // ✅ kasih tahu nama tabel manual

    protected $fillable = [
        'jenis_hidangan'
    ];

    public function menus()
    {
        return $this->hasMany(Menu::class, 'kategori_id', 'id');
    }
}
