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
        Schema::create('analisis_makanans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('daftar_laporan');
            $table->unsignedBigInteger('menu_id');
            $table->unsignedBigInteger('kategori_id');
            $table->integer('total_jumlah');
            $table->float('average_per_pesanan');

            $table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');
            $table->foreign('kategori_id')->references('id')->on('kategoris')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analisis_makanans');
    }
};
