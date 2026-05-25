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
        Schema::create('produk_masuk_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_masuk_id')->constrained('produk_masuks')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produks');
            $table->decimal('berat_awal', 10, 2)->default(0);
            $table->decimal('berat_final', 10, 2)->nullable();
            $table->decimal('berat_keluar', 10, 2)->nullable();
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->enum('status', ['pending', 'selesai'])->default('pending');
            $table->boolean('is_approved')->default(false);
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_masuk_details');
    }
};
