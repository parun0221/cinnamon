<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukKeluar extends Model
{
    /** @use HasFactory<\Database\Factories\ProdukKeluarFactory> */
    use HasFactory;
    protected $guarded = [];

    public function produkKeluarDetails()
    {
        return $this->hasMany(ProdukKeluarDetail::class);
    }

        public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

}
