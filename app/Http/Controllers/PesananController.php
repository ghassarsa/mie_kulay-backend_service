<?php

namespace App\Http\Controllers;

use App\Models\Aktivitas;
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
        Aktivitas::create([
            'user_id' => auth()->user()->id,
            'aktivitas' => "Owner menghapus Pesanan {$pesanan->id}",
        ]);

        return response()->json([
            'message' => 'Pesanan berhasil dihapus'
        ]);
    }
}
