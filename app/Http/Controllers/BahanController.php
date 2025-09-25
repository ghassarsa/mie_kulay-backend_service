<?php

namespace App\Http\Controllers;

use App\Models\Aktivitas;
use App\Models\bahan_mentah;
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
        Log::info('=== STORE METHOD STARTED ===');
        Log::info('User ID: ' . auth()->id());
        Log::info('Session ID: ' . session()->getId());

        $validated = $request->validate([
            'nama_bahan' => 'required|string|max:255|unique:bahan_mentahs,nama_bahan',
            'harga_beli' => 'required|integer',
            'kategori_id' => 'required|exists:kategoris,id',
            'stok' => 'required|integer|min:0',
        ], [
            'nama_bahan.unique' => 'Nama bahan telah terbuat sebelumnya',
        ]);

        // Log sebelum create
        Log::info('Creating bahan: ' . $validated['nama_bahan']);

        $name = auth()->user()->name;
        Aktivitas::create([
            'user_id' => auth()->user()->id,
            'action' => "{$name} menambahkan Bahan Mentah {$validated['nama_bahan']}",
            'aktivitas' => null,
        ]);

        $bahan = bahan_mentah::create($validated);

        // Log setelah create
        Log::info('Bahan created successfully');
        Log::info('User ID after: ' . auth()->id());
        Log::info('Session ID after: ' . session()->getId());

        return response()->json($bahan->load(['menu', 'kategori']));
    }

    public function update(Request $request, $id)
    {
        $bahan = bahan_mentah::findOrFail($id);

        $data = $request->only(['nama_bahan', 'harga_beli', 'stok', 'kategori_id']);

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
