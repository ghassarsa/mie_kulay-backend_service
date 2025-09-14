<?php

namespace App\Http\Controllers;

use App\Models\Aktivitas;
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_hidangan' => 'required|string|max:255|unique:kategoris,jenis_hidangan',
        ]);

        $kategori = Kategori::create([
            'jenis_hidangan' => $validated['jenis_hidangan'],
        ]);

        return response()->json([
            'message' => 'Kategori berhasil ditambahkan',
            'data' => $kategori
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $kategori = Kategori::find($id);

        $validated = $request->validate([
            'jenis_hidangan' => 'required|string|max:255|unique:kategoris,jenis_hidangan,' . $id,
        ]);

        $kategori->update([
            'jenis_hidangan' => $validated['jenis_hidangan'],
        ]);

        return response()->json([
            'message' => 'Kategori berhasil diperbarui',
            'data' => $kategori
        ], 200);
    }


    public function destroy($id)
    {
        $category = Kategori::findOrFail($id);
        $category->delete();

        $name = auth()->user()->name;
        Aktivitas::create([
            'user_id' => auth()->user()->id,
            'action' => "{$name} menghapus Category {$category->jenis_hidangan}",
            'aktivitas' => null,
        ]);

        return response()->json([
            'message' => 'Menu berhasil dihapus'
        ]);
    }
}
