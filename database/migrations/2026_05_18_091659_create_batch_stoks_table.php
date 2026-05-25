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
        Schema::create('batch_stoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
            $table->string('kode_batch');
            $table->date('tanggal_awal');
            $table->date('tanggal_akhir');
            $table->decimal('total_stok', 10, 2)->default(0);
            $table->decimal('stok_keluar', 10, 2)->default(0);
            $table->decimal('harga_modal', 15, 2)->default(0);
            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_stoks');
    }
};
