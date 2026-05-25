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
        Schema::create('riwayat_stoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
            $table->foreignId('produk_masuk_detail_id')->nullable()->constrained('produk_masuk_details')->onDelete('set null');
            $table->foreignId('produk_keluar_detail_id')->nullable()->constrained('produk_keluar_details')->onDelete('set null');
            $table->date('tanggal')->nullable();
            $table->enum('jenis', ['masuk', 'keluar', 'penyesuaian'])->nullable();
            $table->decimal('jumlah', 10, 2)->nullable();
            $table->decimal('stok_siap', 10, 2);
            $table->decimal('stok_pending', 10, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_stoks');
    }
};
