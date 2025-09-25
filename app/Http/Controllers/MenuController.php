<?php

namespace App\Http\Controllers;

use App\Models\Aktivitas;
use App\Models\bahan_mentah;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index()
    {
        $menu = Menu::with(['kategori', 'pesanan_detail', 'bahanMentahs'])->get();
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

        // Validasi hanya jika field ada
        $validated = $request->validate([
            'nama_hidangan' => 'nullable|string|max:255',
            'harga_jual'    => 'nullable|integer',
            'kategori_id'   => 'nullable|exists:kategoris,id',
            'gambar'        => 'nullable|image',
        ]);

        // Handle gambar
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($menu->gambar && Storage::disk('public')->exists($menu->gambar)) {
                Storage::disk('public')->delete($menu->gambar);
            }

            $file = $request->file('gambar');
            $path = $file->store('menu', 'public');
            $validated['gambar'] = $path;
        }

        $oldName = $menu->nama_hidangan ?? 'Menu';
        $menu->update($validated);
        $newName = $menu->nama_hidangan ?? $oldName;

        // Handle bahan opsional
        if ($request->has('bahan')) {
            $bahan = $request->input('bahan');

            if (!isset($bahan['id'])) {
                $newBahan = bahan_mentah::create([
                    'nama_bahan'  => $bahan['nama'] ?? 'Bahan baru',
                    'harga_beli'  => $bahan['harga'] ?? 0,
                    'kategori_id' => $bahan['kategori_id'] ?? 1,
                ]);
                $bahanId = $newBahan->id;
            } else {
                $bahanId = $bahan['id'];
            }

            $menu->bahanMentahs()->syncWithoutDetaching([
                $bahanId => ['jumlah' => $bahan['jumlah'] ?? 1],
            ]);
        }

        // Catat aktivitas
        Aktivitas::create([
            'user_id'   => auth()->user()->id,
            'aktivitas' => "Owner mengubah Menu {$oldName} menjadi {$newName}",
            'action'    => 'update',
        ]);

        return response()->json($menu->load(['kategori', 'pesanan_detail', 'bahanMentahs']));
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
