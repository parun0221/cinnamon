<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\BukuBesar;
use App\Models\RiwayatStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreProdukRequest;
use App\Http\Requests\UpdateProdukRequest;
use App\Http\Requests\StoreKategoriRequest;
use App\Http\Requests\StoreBukuBesarRequest;
use App\Http\Requests\UpdateKategoriRequest;
use App\Http\Requests\UpdateBukuBesarRequest;

use function Symfony\Component\Clock\now;

class BukuBesarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Default start_date = awal bulan, end_date = hari ini
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate   = $request->end_date ?? Carbon::now()->format('Y-m-d');

        $query = BukuBesar::with(['produkMasuk', 'produkKeluar'])
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->orderBy('tanggal', 'asc');

        $bukuBesar = $query->get();

        return view('stok.keuangan', [
            'bukuBesar' => $bukuBesar,
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ]);
    }

        public function tambahUang(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jumlah' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            // Ambil saldo terakhir
            $saldoTerakhir = BukuBesar::orderBy('tanggal', 'desc')->latest()->first()?->saldo ?? 0;

            BukuBesar::create([
                'tanggal' => now(),
                'debit' => $request->jumlah,
                'kredit' => 0,
                'saldo' => $saldoTerakhir + $request->jumlah,
                'keterangan' => $request->keterangan,
            ]);
        });

        return redirect()->back()->with('success', 'Debit berhasil ditambahkan.');
    }

    public function kurangiUang(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jumlah' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            // Ambil saldo terakhir
            $saldoTerakhir = BukuBesar::orderBy('tanggal', 'desc')->latest()->first()?->saldo ?? 0;

            BukuBesar::create([
                'tanggal' => now(),
                'debit' => 0,
                'kredit' => $request->jumlah,
                'saldo' => $saldoTerakhir - $request->jumlah,
                'keterangan' => $request->keterangan,
            ]);
        });

        return redirect()->back()->with('success', 'Kredit berhasil ditambahkan.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBukuBesarRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(BukuBesar $bukuBesar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BukuBesar $bukuBesar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBukuBesarRequest $request, BukuBesar $bukuBesar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BukuBesar $bukuBesar)
    {
        //
    }
}
