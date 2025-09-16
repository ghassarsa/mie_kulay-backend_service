<?php

namespace App\Http\Controllers;

use App\Models\Aktivitas;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;

class PengeluaranController extends Controller
{
    public function index()
    {
        $pengeluaran = Pengeluaran::all();
        return response()->json($pengeluaran);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'pengeluaran' => 'required|integer',
            'catatan'     => 'required|string',
            'tanggal'     => 'required|date',
        ]);

        $pengeluaran = Pengeluaran::create([
            'pengeluaran' => $validate['pengeluaran'],
            'catatan' => $validate['catatan'],
            'status' => 'belum',
            'created_at' => $validate['tanggal'],
        ]);

        if (auth()->check()) {
            $name = auth()->user()->name;
            Aktivitas::create([
                'user_id' => auth()->id(),
                'action' => "{$name} Menambahkan pengeluaran sebesar {$validate['pengeluaran']}",
                'aktivitas' => null,
            ]);
        }

        return response()->json([
            'message' => 'Pengeluaran telah ditambahkan',
            'data' => $pengeluaran
        ]);
    }

    public function update(Request $request, $id)
    {
        // Validasi, semua field opsional
        $validate = $request->validate([
            'pengeluaran' => 'nullable|integer',
            'catatan'     => 'nullable|string',
            'tanggal'     => 'nullable|date',
        ]);

        $pengeluaran = Pengeluaran::findOrFail($id);

        foreach ($validate as $key => $value) {
            if (!is_null($value)) {
                $pengeluaran->{$key === 'tanggal' ? 'created_at' : $key} = $value;
            }
        }

        $pengeluaran->save();

        if (auth()->check()) {
            $name = auth()->user()->name;
            Aktivitas::create([
                'user_id'   => auth()->id(),
                'action'    => isset($validate['pengeluaran'])
                    ? "{$name} Mengubah pengeluaran menjadi sebesar {$validate['pengeluaran']}"
                    : "{$name} Mengubah data pengeluaran",
                'aktivitas' => null,
            ]);
        }

        return response()->json([
            'message' => 'Pengeluaran telah diubah',
            'data'    => $pengeluaran
        ]);
    }

    public function destroy($id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        $pengeluaran->delete();

        $name = auth()->user()->name;
        Aktivitas::create([
            'user_id' => auth()->id(),
            'action' => "{$name} Menghapus pengeluaran sebesar {$pengeluaran->pengeluaran}",
            'aktivitas' => null,
        ]);

        return response()->json([
            'message' => 'Pengeluaran telah dihapus'
        ]);
    }
}
