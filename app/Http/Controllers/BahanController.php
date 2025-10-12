<?php

namespace App\Http\Controllers;

use App\Models\Aktivitas;
use App\Models\bahan_mentah;
use App\Models\Menu;
use App\Models\Pendapatan_Bahan_Lengkap;
use App\Models\Pesanan_Detail;
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
            'menu_id'    => 'required|exists:menus,id',
            'bahan_id'   => 'nullable|exists:bahan_mentahs,id',
            'nama_bahan' => 'nullable|string|max:255|unique:bahan_mentahs,nama_bahan',
            'harga_beli' => 'nullable|integer',
            'tipe'       => 'nullable|string|in:bahan_mentah,bahan_baku,bahan_lengkap',
            'jumlah'     => 'required|integer|min:1',
        ]);

        $menu = Menu::findOrFail($validated['menu_id']);

        if (!empty($validated['bahan_id'])) {
            // Attach bahan lama
            $menu->bahanMentahs()->attach($validated['bahan_id'], [
                'jumlah' => $validated['jumlah']
            ]);
            $bahan = bahan_mentah::find($validated['bahan_id']);
        } else {
            // Buat bahan baru & attach
            $bahan = bahan_mentah::create([
                'nama_bahan' => $validated['nama_bahan'],
                'harga_beli' => $validated['harga_beli'],
                'tipe'       => $validated['tipe'],
            ]);
            $menu->bahanMentahs()->attach($bahan->id, [
                'jumlah' => $validated['jumlah']
            ]);
        }

        return response()->json([
            'id' => $bahan->id,
            'nama_bahan' => $bahan->nama_bahan,
            'harga_beli' => $bahan->harga_beli,
            'tipe' => $bahan->tipe,
            'jumlah' => $validated['jumlah'],
        ]);
    }


    // public function attachBahan(Request $request)
    // {
    //     $validated = $request->validate([
    //         'menu_id' => 'required|exists:menus,id',
    //         'bahan_id' => 'required|exists:bahan_mentahs,id',
    //         'jumlah' => 'required|integer|min:1',
    //     ]);

    //     $menu = Menu::findOrFail($validated['menu_id']);
    //     $menu->bahanMentahs()->attach($validated['bahan_id'], [
    //         'jumlah' => $validated['jumlah'],
    //     ]);

    //     return response()->json(['success' => true]);
    // }

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


    // laporan pendapatan bahan lengkap
    public function laporanPendapatanBahanLengkap()
    {
        // Total subtotal dari semua pesanan bertipe bahan_lengkap
        $pesananDetails = Pesanan_Detail::whereHas('menu.bahanMentahs', function ($query) {
            $query->where('tipe', 'bahan_lengkap');
        })
            ->with('menu.bahanMentahs')
            ->get();

        // Total subtotal
        $hasilPendapatan = $pesananDetails->sum('subtotal');

        // Total biaya bahan
        $biayaBahan = $pesananDetails
            ->flatMap(fn($detail) => $detail->menu->bahanMentahs->where('tipe', 'bahan_lengkap'))
            ->sum('harga_beli');

        $totalPendapatan = $hasilPendapatan - $biayaBahan;

        // Ambil nomor laporan terakhir
        $lastNumber = Pendapatan_Bahan_Lengkap::max('daftar_laporan');
        $nextNumber = $lastNumber ? $lastNumber + 1 : 1;

        // Simpan laporan baru
        $laporan = Pendapatan_Bahan_Lengkap::create([
            'daftar_laporan' => $nextNumber,
            'hasil_pendapatan' => $totalPendapatan,
        ]);

        $pesananDetails->each(fn($detail) => $detail->update(['status' => 'sudah']));

        // Balikin datanya
        return response()->json([
            'laporan' => $laporan,
            'total_pendapatan' => $totalPendapatan,
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

    public function deletePivot(Request $request)
    {
        $validated = $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'bahan_id' => 'required|exists:bahan_mentahs,id',
        ]);

        $menu = Menu::findOrFail($validated['menu_id']);
        $menu->bahanMentahs()->detach($validated['bahan_id']);

        return response()->json([
            'message' => 'Bahan berhasil dihapus'
        ]);
    }
}
