<?php

namespace App\Http\Controllers;

use App\Models\Aktivitas;
use App\Models\bahan_mentah;
use Illuminate\Http\Request;

class BahanController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_bahan' => 'required|string|max:255|unique:bahan_mentahs,nama_bahan',
            'kategori_id' => 'required|exists:kategoris,id',
            'stok' => 'required|integer|min:0',
        ], [
            'nama_bahan.unique' => 'Nama bahan telah terbuat sebelumnya', // custom message
        ]);

        Aktivitas::create([
            'user_id' => auth()->user()->id,
            'action' => "Owner menambahkan Bahan Mentah {$validated['nama_bahan']}",
            'aktivitas' => null,
        ]);

        $bahan = bahan_mentah::create($validated);

        return response()->json($bahan->load(['menu', 'kategori']));
    }
}
