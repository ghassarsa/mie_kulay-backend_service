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
            $table->string('nama_hidangan');
            $table->integer('total_jumlah');
            $table->decimal('average_hidangan', 8, 2);
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
        Schema::dropIfExists('analisis_makanans');
    }
};
