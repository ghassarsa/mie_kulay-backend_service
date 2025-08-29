<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menu = Menu::with(['Category', 'Pesanan_Detail'])->get();
        return response()->json($menu);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_hidangan' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
        ]);

        $menu = Menu::create($validated);
        return response()->json($menu->load(['Category']));
    }

    public function show($id)
    {
        $menu = Menu::with(['Category', 'Pesanan_Detail'])->findOrFail($id);
        return response()->json($menu);
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $validated = $request->validate([
            'nama_hidangan' => 'nullable|string|max:255',
            'kategori_id' => 'nullable|exists:kategori,id',
        ]);

        $menu->update($validated);
        return response()->json($menu->load(['Category', 'Pesanan_Detail']));
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();
        return response()->json([
            'message' => 'Menu berhasil dihapus'
        ]);
    }
}
