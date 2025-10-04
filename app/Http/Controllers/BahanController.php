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
        $bahan = bahan_mentah::all();
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

        $name = auth()->user()->name;

        // Simpan aktivitas
        Aktivitas::create([
            'user_id' => auth()->user()->id,
            'action' => "{$name} menambahkan Bahan Mentah {$validated['nama_bahan']}",
            'table_name' => 'bahan',
            'aktivitas' => null,
        ]);

        return response()->json($bahan);
    }

    public function update(Request $request, $id)
    {
        $bahan = bahan_mentah::findOrFail($id);

        // simpan nilai lama dulu
        $old = $bahan->only(['nama_bahan', 'harga_beli', 'tipe']);

        $data = $request->only(['nama_bahan', 'harga_beli', 'tipe']);
        $name = auth()->user()->name;
        $bahan->update($data);

        $changes = [];
        foreach ($data as $key => $value) {
            if ($old[$key] != $value) {
                $changes[] = "{$key} dari '{$old[$key]}' menjadi '{$value}'";
            }
        }

        $changeText = implode(", ", $changes);

        Aktivitas::create([
            'user_id' => auth()->user()->id,
            'action' => "{$name} mengubah bahan {$bahan->nama_bahan}",
            'aktivitas' => $changeText,
            'table_name' => 'bahan',
        ]);

        return response()->json([
            'message' => 'Bahan berhasil diupdate',
            'data' => $bahan,
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
            'table_name' => 'bahan',
        ]);

        return response()->json([
            'message' => 'Menu berhasil dihapus'
        ]);
    }
}
