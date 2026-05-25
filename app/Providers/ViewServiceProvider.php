<?php

namespace App\Providers;

use App\Models\BukuBesar;
use App\Models\Produk;
use Illuminate\Support\Facades\View;

use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
            View::composer('layouts.header', function ($view) {
            $saldoKas = BukuBesar::orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc')
                ->value('saldo') ?? 0;

            $pendingProdukCount = Produk::whereHas('produkmasukdetails', function ($q) {
                $q->where('is_approved', false);
            })->count();

            $view->with('saldoKas', $saldoKas);
            $view->with('pendingProdukCount', $pendingProdukCount);
        });
    }
} 
