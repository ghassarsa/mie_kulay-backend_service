<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = category::with('menu')->get();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_hidangan' => 'required|string|max:255',
        ]);

        $category = Category::create($validated);
        return response()->json($category);
    }

    public function show($id)
    {
        $category = Category::with('menu')->findOrFail($id);
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'jenis_hidangan' => 'nullable|string|max:255',
        ]);

        $category->update($validated);
        return response()->json($category->load('Menu'));
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json([
            'message' => 'Category berhasil dihapus'
        ]);
    }
}
