<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukMasukDetail extends Model
{
    /** @use HasFactory<\Database\Factories\ProdukMasukDetailFactory> */
    use HasFactory;
    protected $guarded = [];

    public function produkMasuk()
    {
        return $this->belongsTo(ProdukMasuk::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
    public function riwayatStok()
    {
        return $this->hasMany(RiwayatStok::class, 'produk_masuk_detail_id');
    }

}
