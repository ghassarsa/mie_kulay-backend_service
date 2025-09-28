<?php

namespace App\Http\Controllers;

use App\Models\Analisis_Keuangan;
use App\Models\Analisis_Makanan;
use App\Models\Favorite_Menu;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalisisController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');

        // Ambil data keuangan berdasarkan tahun
        $keuangan = Analisis_Keuangan::whereYear('periode_tahunan', $tahun)
            ->orderBy('periode_tahunan', 'desc')
            ->get();

        return response()->json([
            'keuangan' => $keuangan,
        ]);
    }

    public function tahunList()
    {
        $years = Analisis_Keuangan::query()
            ->selectRaw('YEAR(COALESCE(periode_tahunan, periode_bulanan)) as year')
            ->distinct()
            ->orderBy('year', 'asc')
            ->pluck('year');

        return response()->json(['years' => $years]);
    }

    public function monthlyIncome()
    {
        $keuangan = Analisis_Keuangan::select('periode_bulanan', 'hasil_pendapatan')
            ->orderBy('periode_bulanan', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => Carbon::parse($item->periode_bulanan)->format('M'),
                    'income' => $item->hasil_pendapatan
                ];
            });

        return response()->json($keuangan);
    }

    public function monthlyExpenses()
    {
        $keuangan = Analisis_Keuangan::select('periode_bulanan', 'total_pengeluaran')
            ->orderBy('periode_bulanan', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => Carbon::parse($item->periode_bulanan)->format('M'),
                    'pengeluaran' => $item->total_pengeluaran
                ];
            });

        return response()->json($keuangan);
    }

    public function monthlyOrders()
    {
        $orders = Analisis_Keuangan::select('periode_bulanan', 'order_average')
            ->orderBy('periode_bulanan', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => Carbon::parse($item->periode_bulanan)->format('M'),
                    'orders' => $item->order_average
                ];
            });

        return response()->json($orders);
    }

    public function favoriteMenu()
    {
        $favorites = Favorite_Menu::orderByDesc('jumlah')->get();

        return response()->json($favorites);
    }
}
