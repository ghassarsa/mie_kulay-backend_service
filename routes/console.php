<?php

use Illuminate\Support\Facades\Artisan;
use App\Models\Pesanan_Detail;
use App\Models\Analisis_Keuangan;
use App\Models\Analisis_Makanan;
use App\Models\Favorite_Menu;
use App\Models\Menu;
use App\Models\Pengeluaran;
use Illuminate\Support\Carbon;

Artisan::command('laporan:to-analisis-month', function () {
    $details = Pesanan_Detail::where('status', 'belum')->get();

    if ($details->isEmpty()) {
        $this->info("Tidak ada data pesanan yang belum dianalisis.");
        return;
    }

    $lastKeuangan = Analisis_Keuangan::max('daftar_laporan') ?? 0;
    $lastMakanan  = Analisis_Makanan::max('daftar_laporan') ?? 0;
    $daftar_laporan = max($lastKeuangan, $lastMakanan) + 1;

    $hasil_pendapatan = $details->sum('subtotal');
    $total_pengeluaran = Pengeluaran::where('status', 'belum')->sum('pengeluaran');

    $details = Pesanan_Detail::where('status', 'belum')
        ->with('menu.bahanMentahs')
        ->get();
    $total_biaya_bahan = $details->sum(function ($detail) {
        $menu = $detail->menu;
        if (!$menu) return 0;

        // Total harga bahan per menu × jumlah menu di pesanan
        return $menu->bahanMentahs->sum(function ($bahan) use ($detail) {
            return $bahan->harga_beli * $bahan->pivot->jumlah * $detail->jumlah;
        });
    });
    $hasil_keuntungan = $hasil_pendapatan - $total_biaya_bahan - $total_pengeluaran;

    $order_average = $details->sum('jumlah');

    Analisis_Keuangan::create([
        'hasil_pendapatan'  => $hasil_pendapatan,
        'hasil_keuntungan'  => $hasil_keuntungan,
        'total_pengeluaran' => $total_pengeluaran,
        'order_average'     => $order_average,
        'periode_bulanan'   => Carbon::now()->startOfMonth(),
        'daftar_laporan'    => $daftar_laporan,
    ]);

    $grouped = $details->groupBy('menu_id');
    foreach ($grouped as $menu_id => $items) {
        $menu = Menu::find($menu_id);
        if (!$menu) continue;

        $total_jumlah = $items->sum('jumlah');
        $average_hidangan = $items->count() > 0 ? $total_jumlah / $items->count() : 0;

        Analisis_Makanan::create([
            'daftar_laporan'   => $daftar_laporan,
            'nama_hidangan'    => $menu->nama_hidangan,
            'total_jumlah'     => $total_jumlah,
            'average_hidangan' => round($average_hidangan, 2),
            'periode_bulanan'   => Carbon::now()->startOfMonth(),
        ]);
    }

    $analisisMakanan = Analisis_Makanan::where('daftar_laporan', $daftar_laporan)
        ->orderByDesc('total_jumlah')
        ->take(5)
        ->get();

    foreach ($analisisMakanan as $makanan) {
        $menu = Menu::where('nama_hidangan', $makanan->nama_hidangan)->first();
        $kategori = $menu?->kategori?->jenis_hidangan ?? 'Tidak diketahui';

        Favorite_Menu::create([
            'nama_hidangan' => $makanan->nama_hidangan,
            'kategori_hidangan' => $kategori,
            'jumlah'        => $makanan->total_jumlah,
            'analisis_makanan_id' => $makanan->id,
        ]);
    }

    Pengeluaran::where('status', 'belum')->update(['status' => 'sudah']);
    $details->each(fn($item) => $item->update(['status' => 'sudah']));

    $this->info("Analisis bulanan berhasil disimpan dengan daftar_laporan = $daftar_laporan.");
});

Artisan::command('laporan:to-analisis-year', function () {
    $tahun_lalu = now()->subYear()->year;
    $periode_tahunan = Carbon::create($tahun_lalu)->startOfYear();
    $lastReport = max(Analisis_Keuangan::max('daftar_laporan'), Analisis_Makanan::max('daftar_laporan'), 0) + 1;

    $keuanganBulanan = Analisis_Keuangan::whereYear('periode_bulanan', $tahun_lalu)
        ->whereNull('periode_tahunan')
        ->get();

    if ($keuanganBulanan->isEmpty()) {
        $this->info("Tidak ada data bulanan tahun $tahun_lalu untuk Analisis Keuangan.");
    } else {
        $totalPendapatan = $keuanganBulanan->sum('hasil_pendapatan');
        $totalPengeluaran = $keuanganBulanan->sum('total_pengeluaran');
        $totalKeuntungan = $keuanganBulanan->sum('hasil_keuntungan');
        $orderAverage = $keuanganBulanan->avg('order_average');

        Analisis_Keuangan::create([
            'hasil_pendapatan'  => $totalPendapatan,
            'total_pengeluaran' => $totalPengeluaran,
            'hasil_keuntungan'  => $totalKeuntungan,
            'order_average'     => round($orderAverage),
            'daftar_laporan'    => $lastReport,
            'periode_bulanan'   => null,
            'periode_tahunan'   => $periode_tahunan,
        ]);

        foreach ($keuanganBulanan as $item) {
            $item->update(['periode_tahunan' => $periode_tahunan]);
        }

        $this->info("Analisis Keuangan tahunan untuk $tahun_lalu berhasil dibuat.");
    }

    $makananBulanan = Analisis_Makanan::whereYear('periode_bulanan', $tahun_lalu)
        ->whereNull('periode_tahunan')
        ->get();

    if ($makananBulanan->isEmpty()) {
        $this->info("Tidak ada data bulanan tahun $tahun_lalu untuk Analisis Makanan.");
    } else {
        $grouped = $makananBulanan->groupBy('nama_hidangan');

        foreach ($grouped as $namaHidangan => $items) {
            $totalJumlah = $items->sum('total_jumlah');
            $totalOrders = $items->sum(function ($item) {
                return $item->total_jumlah / $item->average_hidangan;
            });
            $averageHidangan = $totalOrders > 0 ? $totalJumlah / $totalOrders : 0;

            Analisis_Makanan::create([
                'daftar_laporan'   => $lastReport,
                'nama_hidangan'    => $namaHidangan,
                'total_jumlah'     => $totalJumlah,
                'average_hidangan' => round($averageHidangan, 2),
                'periode_bulanan'  => null,
                'periode_tahunan'  => $periode_tahunan,
            ]);
        }

        // Tandai baris bulanan sebagai sudah diproses ke tahunan
        foreach ($makananBulanan as $item) {
            $item->update(['periode_tahunan' => $periode_tahunan]);
        }

        $this->info("Analisis Makanan tahunan untuk $tahun_lalu berhasil dibuat.");
    }
});
