<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    /** @use HasFactory<\Database\Factories\KategoriFactory> */
    use HasFactory;
    protected $guarded=[];

    public function produk()
    {
        return $this->hasMany(Produk::class, 'kategori_id');
    }
    
}
