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
        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_produk', 150);
            $table->foreignId('kategori_id')->constrained('kategoris')->onDelete('restrict');
            $table->string('satuan', 50);
            $table->decimal('stok_siap', 10, 2)->default(0);
            $table->decimal('stok_pending', 10, 2)->default(0);
            $table->decimal('stok_minimum', 10, 2)->default(0);
            $table->decimal('harga_jual', 15, 2)->nullable();
            $table->decimal('harga_beli', 15, 2)->nullable();
            $table->string('gambar')->nullable(); // Menyimpan path gambar
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};
