<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produk extends Model
{
    /** @use HasFactory<\Database\Factories\ProdukFactory> */
    use HasFactory;
    use SoftDeletes;
    protected $guarded=[];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }
    public function produkMasukDetails()
    {
        return $this->hasMany(ProdukMasukDetail::class);
    }
    public function produkKeluarDetails()
    {
        return $this->hasMany(ProdukKeluarDetail::class);
    }
    public function riwayatStoks()
    {
        return $this->hasMany(RiwayatStok::class);
    }
    
    public function batchStoks()
    {
        return $this->hasMany(BatchStok::class);
    }
}
