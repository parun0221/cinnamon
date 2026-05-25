<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BukuBesar extends Model
{
    /** @use HasFactory<\Database\Factories\BukuBesarFactory> */
    use HasFactory;

    protected $guarded = [];

        public function produkMasuk()
    {
        return $this->belongsTo(ProdukMasuk::class, 'produk_masuk_id');
    }

    public function produkKeluar()
    {
        return $this->belongsTo(ProdukKeluar::class, 'produk_keluar_id');
    }
}

