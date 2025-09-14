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
            'nama_hidangan' => 'required|string|max:255|unique:menus,nama_hidangan',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif,webp',
            'harga_jual' => 'required|integer',
            'kategori_id' => 'required|exists:kategoris,id',
            'bahan_ids' => 'required|array',
            'bahan_ids.*.bahan_id' => 'required|exists:bahan_mentahs,id',
            'bahan_ids.*.jumlah' => 'required|integer|min:1',
        ], [
            'nama_hidangan.unique' => 'Nama hidangan sudah ada, silakan gunakan nama lain.',
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

        $bahanData = collect($validated['bahan_ids'])->mapWithKeys(function ($bahan) {
            // contoh hasil : $bahan = ['bahan_id' => 3, 'jumlah' => 2]
            return [$bahan['bahan_id'] => ['jumlah' => $bahan['jumlah']]];
        })->toArray();

        // memcegah duplikat bahan mentah untuk menu yang sama
        $menu->bahanMentahs()->sync($bahanData);

        return response()->json($menu->load(['kategori', 'bahanMentahs']));
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
