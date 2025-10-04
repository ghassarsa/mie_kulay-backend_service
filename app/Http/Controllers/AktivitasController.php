<?php

namespace App\Http\Controllers;

use App\Models\Aktivitas;
use Illuminate\Http\Request;

class AktivitasController extends Controller
{
    public function index(Request $request)
    {
        $query = Aktivitas::with('user')->latest();

        // Filter berdasarkan nama tabel
        if ($request->has('table') && $request->table !== 'All') {
            $query->where('table_name', $request->table);
        }

        return response()->json($query->get());
    }
}
