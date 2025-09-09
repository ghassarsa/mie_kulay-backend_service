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

        $old = $pesanan->only(['total_pesanan', 'pembayaran']);

        $pesanan->update($validated);

        $user = auth()->user()->name;
        $changes = [];
        if (array_key_exists('total_pesanan', $validated) && $old['total_pesanan'] != $pesanan->total_pesanan) {
            $changes[] = "total pesanan menjadi {$pesanan->total_pesanan}";
        }
        if (array_key_exists('pembayaran', $validated) && $old['pembayaran'] != $pesanan->pembayaran) {
            $changes[] = "pembayaran menjadi {$pesanan->pembayaran}";
        }

        if (!empty($changes)) {
            Aktivitas::create([
                'user_id' => auth()->user()->id,
                'action' => "{$user} mengubah " . implode(' dan ', $changes),
            ]);
        }

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
