<?php

namespace App\Http\Controllers;

use App\Models\Analisis_Keuangan;
use App\Models\Analisis_Makanan;
use Illuminate\Http\Request;

class AnalisisController extends Controller
{
    public function index()
    {
        $keuangan = Analisis_Keuangan::orderBy('periode_bulanan', 'desc')
            ->orderBy('periode_tahunan', 'desc')
            ->get();

        $makanan = Analisis_Makanan::orderBy('periode_bulanan', 'desc')
            ->orderBy('periode_tahunan', 'desc')
            ->get();

        return response()->json([
            'keuangan' => $keuangan,
            'makanan'  => $makanan,
        ]);
    }
}
