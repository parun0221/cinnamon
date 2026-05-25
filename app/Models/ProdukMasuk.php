<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukMasuk extends Model
{
    /** @use HasFactory<\Database\Factories\ProdukMasukFactory> */
    use HasFactory;
    protected $guarded = [];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function produkMasukDetails()
    {
        return $this->hasMany(ProdukMasukDetail::class);
    }
}
