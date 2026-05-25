<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukKeluarDetail extends Model
{
    /** @use HasFactory<\Database\Factories\ProdukKeluarDetailFactory> */
    use HasFactory;
    protected $guarded = [];

        public function produkKeluar()
    {
        return $this->belongsTo(ProdukKeluar::class, 'produk_keluar_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

        public function riwayatStok()
    {
        return $this->hasMany(RiwayatStok::class, 'produk_keluar_detail_id');
    }
}
