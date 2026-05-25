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
        Schema::create('produk_keluar_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_keluar_id')->constrained('produk_keluars')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produks');
            $table->decimal('jumlah', 10, 2)->default(0);
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('hpp', 15, 2)->default(0);
            $table->decimal('laba', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_keluar_details');
    }
};
