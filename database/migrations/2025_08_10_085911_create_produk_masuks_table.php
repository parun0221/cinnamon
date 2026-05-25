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
        Schema::create('produk_masuks', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_masuk');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->decimal('total_berat', 10, 2)->default(0);
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->string('add_by', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_masuks');
    }
};
