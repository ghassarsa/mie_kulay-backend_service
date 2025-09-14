<?php

namespace App\Http\Controllers;

use App\Models\Aktivitas;
use App\Models\bahan_mentah;
use Illuminate\Http\Request;

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
            'stok' => 'required|integer|min:0',
        ], [
            'nama_bahan.unique' => 'Nama bahan telah terbuat sebelumnya',
            // custom message
        ]);

        $name = auth()->user()->name;
        Aktivitas::create([
            'user_id' => auth()->user()->id,
            'action' => "{$name} menambahkan Bahan Mentah {$validated['nama_bahan']}",
            'aktivitas' => null,
        ]);

        $bahan = bahan_mentah::create($validated);

        return response()->json($bahan->load(['menu', 'kategori']));
    }

    public function update(Request $request, $id)
    {
        $bahan = bahan_mentah::findOrFail($id);

        $data = $request->only(['nama_bahan', 'harga_beli', 'stok', 'kategori_id']);
        $bahan->update($data);

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
