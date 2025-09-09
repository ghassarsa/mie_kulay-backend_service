<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('analisis__keuangans', function (Blueprint $table) {
            $table->id();
            $table->integer('hasil_pendapatan');
            $table->integer('hasil_keuntungan');
            $table->integer('total_pengeluaran');
            $table->integer('order_average');
            $table->unsignedBigInteger('daftar_laporan');
            $table->date('periode_bulanan')->nullable();
            $table->date('periode_tahunan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analisis__keuangans');
    }
};
