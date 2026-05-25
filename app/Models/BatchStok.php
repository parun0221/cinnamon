<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchStok extends Model
{
    /** @use HasFactory<\Database\Factories\BatchStokFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'tanggal_awal' => 'date',
        'tanggal_akhir' => 'date',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function getSisaAttribute()
    {
        return $this->total_stok - $this->stok_keluar;
    }
}
