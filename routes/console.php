<?php

use App\Models\Analisis_Keuangan;
use App\Models\Analisis_Makanan;
use App\Models\Laporan_Pemesanan;
use App\Models\Laporan_Pemesanan_History;
use App\Models\Pesanan_Detail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

Artisan::command('laporan:move-to-history', function () {

    // Ambil nomor batch terakhir, jika belum ada maka mulai dari 1
    $batchId = Laporan_Pemesanan_History::max('daftar_laporan');
    $batchId = $batchId ? $batchId + 1 : 1;

    // Pindahkan laporan pemesanan ke history
    Laporan_Pemesanan::chunk(100, function ($laporans) use ($batchId) {
        foreach ($laporans as $item) {
            Laporan_Pemesanan_History::create([
                'pesanan_id'     => $item->pesanan_id,
                'daftar_laporan' => $batchId,
                'status'         => 'belum',
            ]);
            $item->delete();
        }
    });

    // Ambil semua pesanan_id dari batch ini
    $pesananIds = Laporan_Pemesanan_History::where('daftar_laporan', $batchId)
        ->pluck('pesanan_id')
        ->toArray();

    if (count($pesananIds) === 0) {
        $this->info('Tidak ada pesanan pada batch ini untuk dianalisis.');
        return;
    }

    Log::info('Pesanan IDs for batch ' . $batchId, $pesananIds);

    // Hitung total pendapatan & order average
    $totalPendapatan = Pesanan_Detail::whereIn('pesanan_id', $pesananIds)
        ->sum('subtotal');

    $totalOrder = count($pesananIds);
    $orderAverage = $totalOrder > 0 ? $totalPendapatan / $totalOrder : 0;

    // Hitung pengeluaran & keuntungan
    $pengeluaran = Pesanan_Detail::whereIn('pesanan_id', $pesananIds)
        ->join('menus', 'pesanan__details.menu_id', '=', 'menus.id')
        ->selectRaw('SUM(menus.harga_pokok * pesanan__details.jumlah) as total')
        ->value('total') ?? 0;

    $keuntungan = $totalPendapatan - $pengeluaran;

    // Simpan ke Analisis_Keuangan
    Analisis_Keuangan::create([
        'hasil_pendapatan'  => $totalPendapatan,
        'hasil_keuntungan'  => $keuntungan,
        'total_pengeluaran' => $pengeluaran,
        'order_average'     => $orderAverage,
        'daftar_laporan'    => $batchId
    ]);

    Pesanan_Detail::whereIn('pesanan_id', $pesananIds)->where('status', 'belum')->get()
      ->each(function ($detail) use ($batchId, $totalOrder) {

        $analisis = Analisis_Makanan::firstOrNew([
            'daftar_laporan' => $batchId,
            'menu_id'        => $detail->menu_id,
            'kategori_id'    => $detail->menu->kategori_id,
        ]);

        $analisis->total_jumlah = ($analisis->total_jumlah ?? 0) + $detail->jumlah;
        $analisis->average_per_pesanan = $analisis->total_jumlah / $totalOrder;
        $analisis->save();

        $detail->update(['status' => 'sudah']);
    });

    Laporan_Pemesanan_History::where('daftar_laporan', $batchId)
        ->update(['status' => 'sudah']);

    $this->info('Semua laporan berhasil dipindahkan ke history dengan batch ID: ' . $batchId);
});

Schedule::command('laporan:move-to-history')->everyMinute();
