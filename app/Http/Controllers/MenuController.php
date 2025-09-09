<?php

namespace App\Http\Controllers;

use App\Models\Aktivitas;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menu = Menu::with(['kategori', 'pesanan_detail'])->get();
        return response()->json($menu);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_hidangan' => 'required|string|max:255',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif,webp',
            'harga_pokok' => 'required|integer',
            'harga_jual' => 'required|integer',
            'stok' => 'required|integer',
            'kategori_id' => 'required|exists:kategoris,id',
        ]);

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('menu', 'public');
            $validated['gambar'] = $path;
        }

        Aktivitas::create([
            'user_id' => auth()->user()->id,
            'action' => "Owner menambahkan Menu {$validated['nama_hidangan']}",
            'aktivitas' => null,
        ]);

        $menu = Menu::create($validated);
        return response()->json($menu->load(['kategori']));
    }

    public function show($id)
    {
        $menu = Menu::with(['kategori', 'pesanan_detail'])->findOrFail($id);
        return response()->json($menu);
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $validated = $request->validate([
            'nama_hidangan' => 'nullable|string|max:255',
            'harga_pokok' => 'required|integer',
            'harga_jual' => 'required|integer',
            'stok' => 'required|integer',
            'kategori_id' => 'nullable|exists:kategoris,id',
        ]);
        $oldName = $menu->nama_hidangan;
        $menu->update($validated);
        $newName = $menu->nama_hidangan;

        $name = $request->input('nama_hidangan');
        $validated['nama_hidangan'] = $name;
        Aktivitas::create([
            'user_id' => auth()->user()->id,
            'aktivitas' => "Owner mengubah Menu {$oldName} menjadi {$newName}",
        ]);

        return response()->json($menu->load(['kategori', 'pesanan_detail']));
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();
        Aktivitas::create([
            'user_id' => auth()->user()->id,
            'action' => "Owner menghapus Menu {$menu->nama_hidangan}",
            'aktivitas' => null,
        ]);

        return response()->json([
            'message' => 'Menu berhasil dihapus'
        ]);
    }
}
