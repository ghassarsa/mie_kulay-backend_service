<?php

namespace App\Http\Controllers;

use App\Models\Aktivitas;
use App\Models\Laporan_Pemesanan;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\Pesanan_Detail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Pesanan_DetailController extends Controller
{
    public function index()
    {
        $details = Pesanan_Detail::with(['Pesanan', 'Menu'])->get();
        return response()->json($details);
    }

    public function store(Request $request)
    {
        $data = $request->json()->all();
        if (!isset($data['pesanan']) || !is_array($data['pesanan'])) {
            return response()->json(['message' => 'Data pesanan tidak valid'], 422);
        }
        $pesananItems = $data['pesanan'];
        $rules = [
            '*.menu_id' => 'required|exists:menus,id',
            '*.jumlah' => 'required|integer',
        ];
        $validatedData = validator($pesananItems, $rules)->validate();

        $pesanan = Pesanan::create([
            'id' => 'PSN' . mt_rand(1000000000, 9999999999),
            'total_pesanan' => 0,
            'pembayaran' => $data['pembayaran'] ?? 'Cash',
        ]);

        $total = 0;

        $menus = Menu::whereIn('id', collect($validatedData)->pluck('menu_id'))->get()->keyBy('id');

        foreach ($validatedData as $item) {
            $menu = $menus[$item['menu_id']];

            $harga_satuan = $menu->harga_jual;
            $subtotal = $item['jumlah'] * $harga_satuan;
            $total += $subtotal;

            Pesanan_Detail::create([
                'id' => 'DTL' . mt_rand(1000000000, 9999999999),
                'pesanan_id' => $pesanan->id,
                'menu_id' => $item['menu_id'],
                'nama_hidangan' => $menu->nama_hidangan,
                'jumlah' => $item['jumlah'],
                'harga_satuan' => $harga_satuan,
                'subtotal' => $subtotal,
            ]);
        }
        Laporan_Pemesanan::create([
            'pesanan_id' => $pesanan->id,
        ]);

        $pesanan->update(['total_pesanan' => $total]);
        $name = auth()->user()->name;
        Aktivitas::create([
            'user_id' => auth()->user()->id,
            'aktivitas' => "{$name} Membuat Pesanan {$pesanan->id}",
        ]);

        return response()->json([
            'pesanan_id' => $pesanan->id,
            'total_pesanan' => $total,
            'message' => 'Pesanan berhasil dibuat'
        ]);
    }

    public function show($id)
    {
        $detail = Pesanan_Detail::with(['Pesanan', 'Menu'])->findOrFail($id);
        return response()->json($detail);
    }


    public function update(Request $request, $id)
    {
        $detail = Pesanan_Detail::findOrFail($id);

        $validated = $request->validate([
            'jumlah' => 'nullable|integer',
            'harga_satuan' => 'nullable|integer',
        ]);
        $name = auth()->user()->name;
        Aktivitas::create([
            'user_id' => auth()->user()->id,
            'aktivitas' => "{$name} mengubah Pesanan Detail {$detail->id}",
        ]);

        $detail->update($validated);

        return response()->json($detail->load(['Pesanan', 'Menu']));
    }

    public function destroy($id)
    {
        $detail = Pesanan_Detail::findOrFail($id);
        $detail->delete();

        return response()->json([
            'message' => 'Pesanan Detail berhasil dihapus'
        ]);
    }
}
