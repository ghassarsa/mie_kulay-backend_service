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
        Schema::create('pesanan__details', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('pesanan_id');
            $table->foreign('pesanan_id')->references('id')->on('pesanans')->onDelete('cascade');
            $table->foreignId('menu_id');
            $table->string('nama_hidangan');
            $table->integer('jumlah');
            $table->integer('harga_satuan');
            $table->integer('subtotal');
            $table->enum('status', ['sudah', 'belum'])->default('belum');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan__details');
    }
};
