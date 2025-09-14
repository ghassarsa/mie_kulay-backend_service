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
        ]);

        Pengeluaran::create($validate);

        $name = auth()->user()->name;
        Aktivitas::create([
            'user_id' => auth()->user(),
            'action' => "{$name} Menambahkan pengeluaran sebesar {$validate['pengeluaran']}",
            'aktivitas' => null,
        ]);

        return response()->json([
            'message' => 'Pengeluaran telah ditambahkan'
        ]);
    }
}
