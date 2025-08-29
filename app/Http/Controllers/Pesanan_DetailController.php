<?php

namespace App\Http\Controllers;

use App\Models\Pesanan_Detail;
use Illuminate\Http\Request;

class Pesanan_DetailController extends Controller
{
    public function index()
    {
        $details = Pesanan_Detail::with(['Pesanan', 'Menu'])->get();
        return response()->json($details);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pesanan_id' => 'required|exists:pesanan,id',
            'menu_id' => 'required|exists:menu,id',
            'nama_hidangan' => 'required|string',
            'jumlah' => 'required|integer',
            'harga_satuan' => 'required|integer',
            'subtotal' => 'required|integer',
        ]);

        $detail = Pesanan_Detail::cretae($validated);

        return response()->json($detail->load(['Pesanan', 'Menu']));
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
