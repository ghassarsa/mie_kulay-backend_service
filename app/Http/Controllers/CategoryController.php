<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

use function Pest\Laravel\json;

class CategoryController extends Controller
{
    public function index()
    {
        $category = Kategori::all();
        return response()->json($category);
    }
}
