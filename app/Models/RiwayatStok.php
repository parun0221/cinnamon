<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatStok extends Model
{
    /** @use HasFactory<\Database\Factories\RiwayatStokFactory> */
    use HasFactory;
    protected $guarded = [];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function produkMasukDetail()
    {
        return $this->belongsTo(ProdukMasukDetail::class, 'produk_masuk_detail_id');
    }

    public function produkKeluarDetail()
    {
        return $this->belongsTo(ProdukKeluarDetail::class, 'produk_keluar_detail_id');
    }

}
