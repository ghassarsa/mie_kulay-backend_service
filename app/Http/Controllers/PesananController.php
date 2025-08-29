<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Pesanan_Detail;
use Illuminate\Http\Request;


class PesananController extends Controller
{
    public function index()
    {
        $pesanan = Pesanan::with('Pesanan_Detail')->get();
        return response()->json($pesanan);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'total_pesanan' => 'required|integer',
            'pembayaran' => 'required|string',
        ]);

        $pesanan = Pesanan::create([
            'total_pesanan' => $validated['total_pesanan'],
            'pembayaran' => $validated['pembayaran'],
        ]);

        $total = 0;

        foreach ($validated['details'] as $detail) {
            $pesanan->Pesanan_Detail()->create([
                'pesanan_id' => $detail['pesanan_id'],
                'menu_id' => $detail['menu_id'],
                'nama_hidangan' => $detail['nama_hidangan'],
                'jumlah' => $detail['jumlah'],
                'harga_satuan' => $detail['harga_satuan'],
                'subtotal' => $detail['subtotal'],
            ]);

            $total = $detail['jumlah'] * $detail['harga_satuan'];
        }

        $pesanan->update(['subtotal' => $total]);

        return response()->json($pesanan->load('Pesanan_Detail'));
    }

    public function show($id)
    {
        $pesanan = Pesanan::with('Pesanan_Detail')->findOrFail($id);
        return response()->json($pesanan);
    }

    public function update(Request $request, $id)
    {
        $pesanan = Pesanan::findOrFail($id);

        $validated = $request->validate([
            'total_pesanan' => 'nullable|integer',
            'pembayaran' => 'nullable|string',
        ]);

        $pesanan->update($validated);

        return response()->json(($pesanan));
    }

    public function destroy($id)
    {
        $pesanan = Pesanan::findOrFail($id);
        $pesanan->delete();

        return response()->json([
            'message' => 'Pesanan berhasil dihapus'
        ]);
    }
}
