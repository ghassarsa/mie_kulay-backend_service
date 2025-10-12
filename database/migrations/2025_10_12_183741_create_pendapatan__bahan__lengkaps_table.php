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
        Schema::create('pendapatan__bahan__lengkaps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('daftar_laporan');
            $table->integer('hasil_pendapatan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendapatan__bahan__lengkaps');
    }
};
