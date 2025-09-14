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
        Schema::create('favorite__menus', function (Blueprint $table) {
            $table->id();
            $table->string('nama_hidangan');
            $table->string('kategori_hidangan');
            $table->integer('jumlah');
            $table->foreignId('analisis_makanan_id')->constrained('analisis_makanans')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorite__menus');
    }
};
