<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\BukuBesar;
use App\Models\ProdukMasuk;
use App\Models\RiwayatStok;
use App\Models\ProdukKeluar;
use Illuminate\Http\Request;
use App\Models\ProdukKeluarDetail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

use App\Exports\HistoryProdukKeluarExport;
use App\Exports\LaporanProdukKeluarExport;
use App\Http\Requests\StoreProdukKeluarRequest;
use App\Http\Requests\UpdateProdukKeluarRequest;
use App\Models\BatchStok;
use App\Models\ProdukMasukDetail;
use App\Models\Supplier;

class ProdukKeluarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('output.index');
    }

public function history(Request $request)
{
    
    $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
    $endDate   = $request->end_date ?? Carbon::now()->format('Y-m-d');

    $query = Produk::with(['kategori', 'produkKeluarDetails' => function ($q) use ($startDate, $endDate) {
    $q->with('produkKeluar');

    if ($startDate && $endDate) {
        $q->whereHas('produkKeluar', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('tanggal_keluar', [$startDate, $endDate]);
        });
    }
}]);


    // Filter search
    if ($request->filled('search')) {
        $query->where('nama_produk', 'like', '%' . $request->search . '%');
    }

    // Filter kategori
    if ($request->filled('kategori')) {
        $query->where('kategori_id', $request->kategori);
    }

    $produk   = $query->get();
    $kategori = Kategori::all();

    return view('output.history', compact('kategori', 'produk', 'startDate', 'endDate'));
}


public function historyDetail($id, Request $request)
{
    $startDate = $request->filled('start_date')
        ? $request->start_date
        : Carbon::now()->startOfMonth()->format('Y-m-d');

    $endDate = $request->filled('end_date')
        ? $request->end_date
        : Carbon::now()->format('Y-m-d');

    $details = Produk::with([
        'kategori',
        'produkKeluarDetails' => function ($q) use ($startDate, $endDate) {
            $q->with('produkKeluar');

            if ($startDate && $endDate) {
                $q->whereHas('produkKeluar', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('tanggal_keluar', [$startDate, $endDate]);
                });
            }
            
        }
    ])->findOrFail($id);
    
    $chartData = DB::table('produk_keluar_details as d')
        ->join('produk_keluars as k', 'k.id', '=', 'd.produk_keluar_id')
        ->selectRaw('DATE(k.tanggal_keluar) as tanggal, SUM(d.jumlah) as total')
        ->where('d.produk_id', $id)
        ->whereBetween('k.tanggal_keluar', [$startDate, $endDate])
        ->groupBy(DB::raw('DATE(k.tanggal_keluar)'))
        ->orderBy('tanggal')
        ->get();

    return view('partials.history_output', [
        'details' => $details,
        'labels'  => $chartData->pluck('tanggal')->toArray(),
        'values'  => $chartData->pluck('total')->toArray(),
        'startDate' => $startDate,
        'endDate' => $endDate,
    ]);
}

    public function historyExcel($id, Request $request)
    {
        $startDate = $request->filled('start_date')
            ? $request->start_date
            : Carbon::now()->startOfMonth()->format('Y-m-d');

        $endDate = $request->filled('end_date')
            ? $request->end_date
            : Carbon::now()->format('Y-m-d');

        $rows = ProdukKeluarDetail::with([
            'produk.kategori',
            'produkKeluar'
        ])
        ->where('produk_id', $id)
        ->whereHas('produkKeluar', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('tanggal_keluar', [$startDate, $endDate]);
        })
        ->orderByDesc('id')
        ->get();

        return Excel::download(
            new HistoryProdukKeluarExport($rows),
            "history_produk_keluar_{$startDate}_sampai_{$endDate}.xlsx"
        );
    }



public function laporan(Request $request)
{
$startDate = $request->start_date
    ?? Carbon::now()->startOfMonth()->format('Y-m-d');

$endDate = $request->end_date
    ?? Carbon::now()->format('Y-m-d');

$query = DB::table('produk_keluar_details as pkd')

    ->join('produk_keluars as pk', 'pk.id', '=', 'pkd.produk_keluar_id')

    ->join('produks as p', 'p.id', '=', 'pkd.produk_id')

    ->join('kategoris as k', 'k.id', '=', 'p.kategori_id')

    ->select(

        'k.nama_kategori as kategori',

        'p.nama_produk as produk',

        // TOTAL PENJUALAN
        DB::raw('ROUND(SUM(pkd.subtotal), 0) as penjualan'),

        // TOTAL HPP
        DB::raw('ROUND(SUM(pkd.hpp), 0) as hpp'),

        // TOTAL LABA
        DB::raw('ROUND(SUM(pkd.laba), 0) as laba')
    )

    // FILTER TANGGAL
    ->whereBetween('pk.tanggal_keluar', [$startDate, $endDate]);

// =====================================================
// FILTER KATEGORI
// =====================================================

if ($request->filled('kategori')) {

    $query->where('k.id', $request->kategori);
}

// =====================================================
// GROUPING
// =====================================================

$laporan = $query

    ->groupBy(
        'k.nama_kategori',
        'p.nama_produk'
    )

    ->orderBy('p.nama_produk', 'asc')

    ->get();

// =====================================================
// DATA KATEGORI
// =====================================================

$kategori = DB::table('kategoris')->get();

return view('output.laporan', compact(
    'laporan',
    'kategori'
));
}

    public function laporanExcel(Request $request)
    {
        $startDate = $request->filled('start_date')
            ? $request->start_date
            : Carbon::now()->startOfMonth()->format('Y-m-d');

        $endDate = $request->filled('end_date')
            ? $request->end_date
            : Carbon::now()->format('Y-m-d');

        $subModal = DB::table('produk_masuk_details as pmd')
            ->join('produk_masuks as pm', 'pm.id', '=', 'pmd.produk_masuk_id')
            ->select(
                'pmd.produk_id',
                DB::raw('SUM(COALESCE(pmd.berat_final, pmd.berat_awal) * pmd.harga_satuan) as total_modal_value'),
                DB::raw('SUM(COALESCE(pmd.berat_final, pmd.berat_awal)) as total_modal_qty')
            )
            // kalau mau batasi sumber modal berdasarkan tanggal masuk, aktifkan filter ini
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('pm.tanggal_masuk', [$startDate, $endDate]);
            })
            ->groupBy('pmd.produk_id');

            $rows = DB::table('produk_keluar_details as pkd')
                ->join('produk_keluars as pk', 'pk.id', '=', 'pkd.produk_keluar_id')
                ->join('produks as p', 'p.id', '=', 'pkd.produk_id')
                ->join('kategoris as k', 'k.id', '=', 'p.kategori_id')
                ->leftJoinSub($subModal, 'pmd_modal', function ($join) {
                    $join->on('pmd_modal.produk_id', '=', 'pkd.produk_id');
                })
                ->whereBetween('pk.tanggal_keluar', [$startDate, $endDate]) // ✅ PINDAH KE SINI
                ->select(
                    'k.nama_kategori as kategori',
                    'p.nama_produk as produk',
                    DB::raw('SUM(pkd.jumlah) as jumlah_keluar'),
                    DB::raw('ROUND(AVG(pkd.harga_satuan), 0) as harga_jual_rata2'),
                    DB::raw('SUM(pkd.subtotal) as total_penjualan'),

                    DB::raw('ROUND(
                        CASE
                            WHEN MAX(pmd_modal.total_modal_qty) IS NULL 
                                OR MAX(pmd_modal.total_modal_qty) = 0
                            THEN COALESCE(MAX(p.harga_beli), 0)
                            ELSE MAX(pmd_modal.total_modal_value) / MAX(pmd_modal.total_modal_qty)
                        END
                    , 0) as harga_modal_rata2'),

                    DB::raw('ROUND(
                        (CASE
                            WHEN MAX(pmd_modal.total_modal_qty) IS NULL 
                                OR MAX(pmd_modal.total_modal_qty) = 0
                            THEN COALESCE(MAX(p.harga_beli), 0)
                            ELSE MAX(pmd_modal.total_modal_value) / MAX(pmd_modal.total_modal_qty)
                        END) * SUM(pkd.jumlah)
                    , 0) as total_modal'),

                    DB::raw('ROUND(
                        SUM(pkd.subtotal) - (
                            (CASE
                                WHEN MAX(pmd_modal.total_modal_qty) IS NULL 
                                    OR MAX(pmd_modal.total_modal_qty) = 0
                                THEN COALESCE(MAX(p.harga_beli), 0)
                                ELSE MAX(pmd_modal.total_modal_value) / MAX(pmd_modal.total_modal_qty)
                            END) * SUM(pkd.jumlah)
                        )
                    , 0) as keuntungan')
                )
                ->groupBy('k.nama_kategori', 'p.nama_produk')
                ->orderBy('k.nama_kategori')
                ->get();




        return Excel::download(
            new LaporanProdukKeluarExport($rows),
            "laporan_produk_keluar_{$startDate}_sampai_{$endDate}.xlsx"
        );
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
public function store(Request $request)
{
    $request->validate([
        'tanggal_masuk' => 'required|date',
        'supplier_id' => 'required|exists:suppliers,id',
        'produk' => 'required|array|min:1',
        'produk.*.id' => 'required|exists:produks,id',
        'produk.*.berat' => 'required|numeric|min:0',
        'produk.*.harga_satuan' => 'required|numeric|min:0',
        'produk.*.subtotal' => 'nullable|numeric|min:0',
    ]);

    DB::beginTransaction();

    try {
        $produkKeluar = ProdukKeluar::create([
            'tanggal_keluar' => $request->tanggal_masuk,
            'supplier_id' => $request->supplier_id,
            'total_berat' => 0,
            'total_harga' => 0,
            'catatan' => "Produk keluar pada " . now()->format('Y-m-d H:i:s'),
        ]);

        $totalBerat = 0;
        $totalHarga = 0;


        foreach ($request->produk as $item) {

            $produk = Produk::findOrFail($item['id']);
            $berat = (float) $item['berat'];
            $hargaSatuan = (float) $item['harga_satuan'];
            $subtotal = $berat * $hargaSatuan;

            // =====================================================
            // FIFO BATCH STOK + HITUNG HPP
            // =====================================================

            $sisaKeluar = $berat;
            $totalHpp = 0;

            $batches = BatchStok::where('produk_id', $produk->id)
                ->whereRaw('total_stok > stok_keluar')
                ->orderBy('tanggal_awal', 'asc')
                ->get();

            foreach ($batches as $batch) {
                if ($sisaKeluar <= 0) {
                    break;
                }

                $sisaBatch = $batch->total_stok - $batch->stok_keluar;

                if ($sisaBatch >= $sisaKeluar) {

                    $qtyDipakai = $sisaKeluar;
                    $batch->stok_keluar += $qtyDipakai;

                    // =====================================================
                    // HITUNG HPP
                    // =====================================================

                    $hppBatch = $qtyDipakai * $batch->harga_modal;
                    $totalHpp += $hppBatch;
                    $sisaKeluar = 0;

                } else {

                    $qtyDipakai = $sisaBatch;
                    $batch->stok_keluar += $qtyDipakai;

                    $hppBatch = $qtyDipakai * $batch->harga_modal;
                    $totalHpp += $hppBatch;
                    $sisaKeluar -= $qtyDipakai;
                }

                $batch->save();
            }

            if ($sisaKeluar > 0) {

                DB::rollBack();

                return redirect()->back()->withErrors([
                    'error' => 'Stok produk ' . $produk->nama_produk . ' tidak mencukupi.'
                ]);
            }

            $laba = $subtotal - $totalHpp;

            $detail = $produkKeluar->produkKeluarDetails()->create([
                'produk_id'    => $produk->id,
                'jumlah'       => $berat,
                'harga_satuan' => $hargaSatuan,
                'subtotal'     => $subtotal,
                'hpp'          => $totalHpp,
                'laba'         => $laba,
            ]);

            $produk->stok_siap -= $berat;
            $produk->save();

            RiwayatStok::create([
                'produk_id' => $produk->id,
                'produk_keluar_detail_id' => $detail->id,
                'tanggal' => $request->tanggal_masuk,
                'jenis' => 'keluar',
                'jumlah' => $berat,
                'stok_siap' => $produk->stok_siap,
                'stok_pending' => $produk->stok_pending,
                'keterangan' => "Produk keluar dengan status Terkirim",
            ]);

            $totalBerat += $berat;

            $totalHarga += $subtotal;
        }

        $produkKeluar->update([
            'total_berat' => $totalBerat,
            'total_harga' => $totalHarga,
        ]);

        if ($totalHarga > 0) {
            $lastSaldo = BukuBesar::orderBy('tanggal', 'desc')->value('saldo') ?? 0;

            BukuBesar::create([
                'produk_keluar_id' => $produkKeluar->id,
                'tanggal' => now(),
                'debit' => $totalHarga,
                'kredit' => 0,
                'saldo' => $lastSaldo + $totalHarga,
                'keterangan' => 'Produk keluar: total ' . count($request->produk) . ' item',
            ]);
        }


        DB::commit();

        // return redirect()->back()->with('success', 'Produk keluar berhasil disimpan!');
        return redirect()
        ->route('output.index')
        ->with('show_nota', true)
        ->with('nota_id', $produkKeluar->id);
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->withErrors(['error' => $e->getMessage()]);
    }
}



    /**
     * Display the specified resource.
     */
    public function show(ProdukKeluar $produkKeluar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProdukKeluar $produkKeluar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProdukKeluarRequest $request, ProdukKeluar $produkKeluar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProdukKeluar $produkKeluar)
    {
        //
    }
    public function supplierSearch0(Request $request)
    {
        $search = $request->input('q');

        $query = Supplier::query()->where('tipe', 'to');

        if ($search) {
            $query->where('nama_supplier', 'like', "%{$search}%");
        }

        // Ambil maksimal 10 hasil
        $suppliers = $query->limit(10)->get();

        $results = $suppliers->map(function ($supplier) {
            return [
                'id' => $supplier->id,
                'text' => $supplier->nama_supplier,
            ];
        });

        return response()->json(['results' => $results]);
    }
}
