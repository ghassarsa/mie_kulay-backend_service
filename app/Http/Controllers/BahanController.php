<?php

namespace App\Http\Controllers;

use App\Models\Aktivitas;
use App\Models\bahan_mentah;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BahanController extends Controller
{
    public function index()
    {
        $bahan = bahan_mentah::with('kategori')->get();
        return response()->json($bahan);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_bahan' => 'required|string|max:255|unique:bahan_mentahs,nama_bahan',
            'harga_beli' => 'required|integer',
            'kategori_id' => 'required|exists:kategoris,id',
            'menu_id' => 'required|exists:menus,id', // Hanya untuk pivot
            'jumlah' => 'nullable|integer|min:1',     // Optional jumlah pivot
        ], [
            'nama_bahan.unique' => 'Nama bahan telah terbuat sebelumnya',
        ]);

        $name = auth()->user()->name;

        // Simpan aktivitas
        Aktivitas::create([
            'user_id' => auth()->user()->id,
            'action' => "{$name} menambahkan Bahan Mentah {$validated['nama_bahan']}",
            'aktivitas' => null,
        ]);

        // Buat bahan baru
        $bahan = bahan_mentah::create([
            'nama_bahan' => $validated['nama_bahan'],
            'harga_beli' => $validated['harga_beli'],
            'kategori_id' => $validated['kategori_id'],
        ]);

        // Attach ke menu lewat pivot
        $menu = Menu::findOrFail($validated['menu_id']);
        $menu->bahanMentahs()->attach($bahan->id, [
            'jumlah' => $validated['jumlah'] ?? 1,
        ]);

        return response()->json($bahan->load(['menus', 'kategori']));
    }

    public function storeBahanMentah(Request $request)
    {
        $validated = $request->validate([
            'nama_bahan' => 'required|string|max:255|unique:bahan_mentahs,nama_bahan',
            'harga_beli' => 'required|integer',
            'tipe'       => 'required|string|in:bahan_mentah,bahan_baku,bahan_lengkap',
        ], [
            'nama_bahan.unique' => 'Nama bahan telah terbuat sebelumnya',
        ]);

        $bahan = bahan_mentah::create($validated);

        return response()->json($bahan);
    }

    public function update(Request $request, $id)
    {
        $bahan = bahan_mentah::findOrFail($id);

        $data = $request->only(['nama_bahan', 'harga_beli', 'kategori_id']);

        $name = auth()->user()->name;
        $bahan->update($data);
        Aktivitas::create([
            'user_id' => auth()->user()->id,
            'action' => "{$name} mengubah Bahan Mentah {$bahan->nama_bahan}",
            'aktivitas' => null,
        ]);

        return response()->json([
            'message' => 'Bahan berhasil diupdate',
            'data' => $bahan->load('kategori')
        ]);
    }

    public function destroy($id)
    {
        $bahan = bahan_mentah::findOrFail($id);
        $bahan->delete();

        $name = auth()->user()->name;
        Aktivitas::create([
            'user_id' => auth()->user()->id,
            'action' => "{$name} menghapus Bahan {$bahan->nama_bahan}",
            'aktivitas' => null,
        ]);

        return response()->json([
            'message' => 'Menu berhasil dihapus'
        ]);
    }
}
