<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Supplier;
use App\Models\BukuBesar;
use App\Models\ProdukMasuk;
use App\Models\RiwayatStok;
use Illuminate\Http\Request;
use App\Models\ProdukMasukDetail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProdukMasukRequest;
use App\Http\Requests\UpdateProdukMasukRequest;
use function Symfony\Component\Clock\now;
use Illuminate\Support\Facades\Auth;

use App\Exports\HistoryProdukMasukExport;
use App\Exports\LaporanProdukMasukExport;
use App\Models\BatchStok;
use Maatwebsite\Excel\Facades\Excel;

class ProdukMasukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('input.index');
    }

    public function pending(Request $request)
    {
        $query = Produk::with('kategori', 'produkMasukDetails')
                ->whereHas('produkMasukDetails', function($query) {
                    $query->where('status', 'pending');
                })
                ->get();


        // Filter search
        if ($request->filled('search')) {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

        // Filter kategori
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        $produk = $query;
        $kategori = Kategori::all();

        return view('input.pending', compact('kategori', 'produk'));
    } 

    public function pendingDetails($id)
    {
        $produk = Produk::with([
            'kategori',
            'produkMasukDetails' => function ($q) {
                $q->where('status', 'pending')
                ->with('produkMasuk'); // jika ingin ambil info tanggal/supplier dari tabel induk
            }
        ])->findOrFail($id);

        return view('partials.pending_detail', compact('produk'));
    }

    public function history(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate   = $request->end_date ?? Carbon::now()->format('Y-m-d');

        $query = Produk::with(['kategori', 'produkMasukDetails' => function ($q) use ($startDate, $endDate) {
        $q->with('produkMasuk');

        if ($startDate && $endDate) {
            $q->whereHas('produkMasuk', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_masuk', [$startDate, $endDate]);
            });
        }
    }]);



        if ($request->filled('search')) {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

     
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        $produk   = $query->get();
        $kategori = Kategori::all();

        return view('input.history', compact('kategori', 'produk', 'startDate', 'endDate'));
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
            'produkMasukDetails' => function ($q) use ($startDate, $endDate) {

                $q->join('produk_masuks', 'produk_masuks.id', '=', 'produk_masuk_details.produk_masuk_id')
                ->with('produkMasuk');

                if ($startDate && $endDate) {
                    $q->whereBetween('produk_masuks.tanggal_masuk', [$startDate, $endDate]);
                }

                $q->orderBy('produk_masuks.tanggal_masuk', 'asc');
            }
        ])->findOrFail($id);


        $chartData = DB::table('produk_masuk_details as d')
            ->join('produk_masuks as k', 'k.id', '=', 'd.produk_masuk_id')
            ->selectRaw("
                DATE(k.tanggal_masuk) as tanggal,
                SUM(
                    CASE 
                        WHEN d.status = 'pending' THEN d.berat_awal
                        WHEN d.status = 'selesai' THEN d.berat_final
                        ELSE 0
                    END
                ) as total
            ")
            ->where('d.produk_id', $id)
            ->whereBetween('k.tanggal_masuk', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(k.tanggal_masuk)'))
            ->orderBy('tanggal')
            ->get();


        return view('partials.history_detail', [
            'details' => $details,
            'labels'  => $chartData->pluck('tanggal')->toArray(),
            'values'  => $chartData->pluck('total')->toArray(),
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        // return view('partials.history_detail', compact('details'));
    }

    public function historyExcel($id, Request $request)
    {
        $startDate = $request->filled('start_date')
            ? $request->start_date
            : Carbon::now()->startOfMonth()->format('Y-m-d');

        $endDate = $request->filled('end_date')
            ? $request->end_date
            : Carbon::now()->format('Y-m-d');

        $rows = ProdukMasukDetail::with([
            'produk.kategori',
            'produkMasuk'
        ])
        ->where('produk_id', $id)
        ->whereHas('produkMasuk', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('tanggal_masuk', [$startDate, $endDate]);
        })
        ->orderByDesc('id')
        ->get();

        return Excel::download(
            new HistoryProdukMasukExport($rows),
            "history_produk_masuk_{$startDate}_sampai_{$endDate}.xlsx"
        );
    }

    public function laporan(Request $request)
    {
    $kategoriList = Kategori::all();

    $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
    $endDate   = $request->end_date ?? Carbon::now()->format('Y-m-d');

    $query = DB::table('produk_masuk_details as pmd')
        ->join('produks as p', 'p.id', '=', 'pmd.produk_id')
        ->join('kategoris as k', 'k.id', '=', 'p.kategori_id')
        ->join('produk_masuks as pm', 'pm.id', '=', 'pmd.produk_masuk_id')
        ->select(
            'k.nama_kategori as kategori',
            'p.nama_produk as produk',
            DB::raw('SUM(pmd.berat_awal) as total_berat_awal'),
            DB::raw('SUM(CASE WHEN pmd.berat_awal = 0 THEN pmd.berat_final ELSE 0 END) as total_berat_final'),
            DB::raw('ROUND(AVG(pmd.harga_satuan), 0) as rata_harga_satuan'),
            DB::raw('SUM(pmd.subtotal) as total_nilai'),
            'p.harga_jual as harga_jual',

            // Pending: berat_awal hanya jika berat_final masih null
            DB::raw('SUM(CASE WHEN pmd.status = "pending" AND pmd.berat_final IS NULL THEN pmd.berat_awal ELSE 0 END) as pending'),

            // Siap: berat_final yang sudah ada
            DB::raw('SUM(CASE WHEN pmd.status = "selesai" AND pmd.berat_final IS NOT NULL THEN pmd.berat_final ELSE 0 END) as siap'),

            // Total Aset
            // DB::raw('(
            //     p.harga_jual * (
            //         SUM(CASE WHEN pmd.status = "pending" AND pmd.berat_final IS NULL THEN pmd.berat_awal ELSE 0 END)
            //         + SUM(CASE WHEN pmd.status = "selesai" AND pmd.berat_final IS NOT NULL THEN pmd.berat_final ELSE 0 END)
            //     )
            // ) as total_aset'),

            // // Keuntungan
            // DB::raw('(
            //     (p.harga_jual * (
            //         SUM(CASE WHEN pmd.status = "pending" AND pmd.berat_final IS NULL THEN pmd.berat_awal ELSE 0 END)
            //         + SUM(CASE WHEN pmd.status = "selesai" AND pmd.berat_final IS NOT NULL THEN pmd.berat_final ELSE 0 END)
            //     )) - SUM(pmd.subtotal)
            // ) as keuntungan')

        );

        // Filter tanggal

        $query->whereBetween('pm.tanggal_masuk', [$startDate, $endDate]);


        // Filter kategori
        if ($request->filled('kategori')) {
            $query->where('p.kategori_id', $request->kategori);
        }

        $laporan = $query
            ->groupBy('k.nama_kategori', 'p.nama_produk', 'p.harga_jual')
            ->get();

        return view('input.laporan', [
            'laporan' => $laporan,
            'kategori' => $kategoriList
        ]);
    }

    public function laporanExcel(Request $request)
    {
        $startDate = $request->filled('start_date')
            ? $request->start_date
            : Carbon::now()->startOfMonth()->format('Y-m-d');

        $endDate = $request->filled('end_date')
            ? $request->end_date
            : Carbon::now()->format('Y-m-d');

        $rows = DB::table('produk_masuk_details as pmd')
            ->join('produks as p', 'p.id', '=', 'pmd.produk_id')
            ->join('kategoris as k', 'k.id', '=', 'p.kategori_id')
            ->join('produk_masuks as pm', 'pm.id', '=', 'pmd.produk_masuk_id')
            ->whereBetween('pm.tanggal_masuk', [$startDate, $endDate])
            ->select(
                'k.nama_kategori as kategori',
                'p.nama_produk as produk',
                DB::raw('SUM(pmd.berat_awal) as total_berat_awal'),
                DB::raw('SUM(CASE WHEN pmd.berat_awal = 0 THEN pmd.berat_final ELSE 0 END) as total_berat_final'),
                DB::raw('ROUND(AVG(pmd.harga_satuan), 0) as rata_harga_satuan'),
                DB::raw('SUM(pmd.subtotal) as total_nilai'),
                DB::raw('SUM(CASE WHEN pmd.status = "pending" AND pmd.berat_final IS NULL THEN pmd.berat_awal ELSE 0 END) as pending'),
                DB::raw('SUM(CASE WHEN pmd.status = "selesai" AND pmd.berat_final IS NOT NULL THEN pmd.berat_final ELSE 0 END) as siap')
            )
            ->groupBy('k.nama_kategori', 'p.nama_produk')
            ->orderBy('k.nama_kategori')
            ->get();

        return Excel::download(
            new LaporanProdukMasukExport($rows),
            "laporan_produk_masuk_{$startDate}_sampai_{$endDate}.xlsx"
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
            'produk.*.status' => 'required|in:pending,selesai',
        ]);

        DB::beginTransaction();

        try {
            // Simpan data header produk masuk
            $produkMasuk = ProdukMasuk::create([
                'tanggal_masuk' => $request->tanggal_masuk,
                'supplier_id' => $request->supplier_id,
                'total_berat' => 0, // nanti diupdate
                'total_harga' =>0,
                'catatan' => $request->catatan ?? null,
                'add_by' => Auth::user()->name,
            ]);

            $totalBerat = 0;
            $totalHarga = 0;

            foreach ($request->produk as $item) {
                $produk = Produk::findOrFail($item['id']);
                $berat = (float) $item['berat'];
                $hargaSatuan = (float) $item['harga_satuan'];
                $subtotal = $berat * $hargaSatuan;
                $status = $item['status'];

                // Simpan detail produk masuk
                if ($status === 'pending') {
                    $detail = $produkMasuk->produkMasukDetails()->create([
                        'produk_id' => $produk->id,
                        'berat_awal' => $berat,
                        'harga_satuan' => $hargaSatuan,
                        'subtotal' => $subtotal,
                        'status' => $status,
                        'is_approved' => false,
                        'approved_at' => null,
                    ]);
                } else if ($status === 'selesai') {
                    $detail = $produkMasuk->produkMasukDetails()->create([
                        'produk_id' => $produk->id,
                        'berat_final' => $berat,
                        'harga_satuan' => $hargaSatuan,
                        'subtotal' => $subtotal,
                        'status' => $status,
                        'is_approved' => false,
                        'approved_at' => null,
                    ]);
                }

                $totalBerat += $berat;
                $totalHarga += $subtotal;
            }

            // Update total berat di produk_masuks
                $produkMasuk->update([
                'total_berat' => $totalBerat,
                'total_harga' => $totalHarga,
            ]);

                if ($totalHarga > 0) {
                $lastSaldo = BukuBesar::orderBy('tanggal', 'desc')->value('saldo') ?? 0;

                BukuBesar::create([
                    'produk_masuk_id' => $produkMasuk->id,
                    'tanggal' => now(),
                    'debit' => 0,
                    'kredit' => $totalHarga,
                    'saldo' => $lastSaldo - $totalHarga,
                    'keterangan' => 'Produk masuk: total ' . count($request->produk) . ' item',
                ]);
            }

            DB::commit();

            // return redirect()->back()->with('success', 'Produk masuk berhasil disimpan!');
            return redirect()
                ->route('input.index')
                ->with('show_nota', true)
                ->with('nota_id', $produkMasuk->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

public function prosesPending(Request $request, $id)
{
    $request->validate([
        'berat_final' => 'required|numeric|min:0'
    ]);

    DB::transaction(function () use ($request, $id) {
        $detail = ProdukMasukDetail::with('produk')->findOrFail($id);

        if ($detail->status === 'selesai') {
            throw new \Exception('Data sudah diproses.');
        }

        // Update status & berat_final di detail
        $detail->update([
            'berat_final' => $request->berat_final,
            'status'      => 'selesai'
        ]);

        if ($detail->is_approved == true) {
            $produk = $detail->produk;

            // Hitung stok baru
            $stok_pending_baru = max(0, $produk->stok_pending - $detail->berat_awal);
            $stok_siap_baru    = $produk->stok_siap + $request->berat_final;

            // Update stok di tabel produk
            $produk->update([
                'stok_pending' => $stok_pending_baru,
                'stok_siap'    => $stok_siap_baru
            ]);

            // Simpan riwayat stok
            RiwayatStok::create([
                'produk_id'    => $detail->produk_id,
                'jumlah'       => $request->berat_final - $detail->berat_awal,
                'tanggal'      => now(),
                'jenis'         => 'penyesuaian',
                'stok_siap'    => $stok_siap_baru,
                'stok_pending' => $stok_pending_baru,
                'keterangan'   => 'Proses pending dari pemasukan'
            ]);

            // =====================================================
            // UPDATE / CREATE BATCH STOK
            // =====================================================

            $tanggalMasuk = Carbon::parse($detail->produkMasuk->tanggal_masuk);

            if ($tanggalMasuk->day <= 15) {

                $tanggalAwal = $tanggalMasuk->copy()->startOfMonth();

                $tanggalAkhir = $tanggalMasuk->copy()
                    ->startOfMonth()
                    ->addDays(14);

                $kodeBatch =
                    strtoupper($tanggalMasuk->translatedFormat('M')) .
                    '-1-' .
                    $tanggalMasuk->year;

            } else {
                $tanggalAwal = $tanggalMasuk->copy()
                    ->startOfMonth()
                    ->addDays(15);

                $tanggalAkhir = $tanggalMasuk->copy()->endOfMonth();

                $kodeBatch =
                    strtoupper($tanggalMasuk->translatedFormat('M')) .
                    '-2-' .
                    $tanggalMasuk->year;
            }

            $beratMasuk = $request->berat_final;
            $hargaMasuk = $detail->harga_satuan;

            $batch = BatchStok::where('produk_id', $detail->produk_id)
                ->where('kode_batch', $kodeBatch)
                ->first();

            if (!$batch) {

                $batch = BatchStok::create([
                    'produk_id'     => $detail->produk_id,
                    'kode_batch'    => $kodeBatch,
                    'tanggal_awal'  => $tanggalAwal,
                    'tanggal_akhir' => $tanggalAkhir,
                    'total_stok'    => $request->berat_final,
                    'stok_keluar'   => 0,
                    'harga_modal'   => $hargaMasuk,
                    'catatan'       => 'Batch otomatis dari approve produk masuk',
                ]);

            } else {
                $stokLama = $batch->total_stok;
                $hargaLama = $batch->harga_modal;

                $totalStokBaru = $stokLama + $beratMasuk;

                $hargaModalBaru =
                    (
                        ($stokLama * $hargaLama)
                        +
                        ($beratMasuk * $hargaMasuk)
                    )
                    /
                    $totalStokBaru;

                $batch->total_stok = $totalStokBaru;
                $batch->harga_modal = $hargaModalBaru;

                $batch->save();
            }
        }

    });

    return back()->with('success', 'Pending berhasil diproses.');
}




    /**
     * Display the specified resource.
     */
    public function show(ProdukMasuk $produkMasuk)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProdukMasuk $produkMasuk)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProdukMasukRequest $request, ProdukMasuk $produkMasuk)
    {
        //
    }

      public function supplierSearch(Request $request)
    {
        $search = $request->input('q');

        $query = Supplier::query()->where('tipe', 'from');

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

    /**
     * Simpan supplier baru (via AJAX)
     */
    public function supplierStore(Request $request)
    {
        $request->validate([
            'nama_supplier' => 'required|string|max:255|unique:suppliers,nama_supplier',
        ], [
            'nama_supplier.unique' => 'Supplier sudah ada.',
            'nama_supplier.required' => 'Nama supplier wajib diisi.',
        ]);

        $supplier = Supplier::create([
            'nama_supplier' => $request->nama_supplier,
        ]);

        return response()->json([
            'id' => $supplier->id,
            'text' => $supplier->nama_supplier,
        ], 201);
    }

        public function supplierStore1(Request $request)
    {
        $request->validate([
            'nama_supplier' => 'required|string|max:255|unique:suppliers,nama_supplier',
        ], [
            'nama_supplier.unique' => 'Supplier sudah ada.',
            'nama_supplier.required' => 'Nama supplier wajib diisi.',
        ]);

        $supplier = Supplier::create([
            'nama_supplier' => $request->nama_supplier,
        ]);

        return response()->json([
            'id' => $supplier->id,
            'text' => $supplier->nama_supplier,
        ], 201);
    }

    // Search produk untuk select2
    public function produkSearch(Request $request)
    {
        $search = $request->input('q');
        $query = Produk::with('kategori'); // eager loading kategori

        if ($search) {
            $query->where('nama_produk', 'like', "%{$search}%");
        }

        $produks = $query->limit(10)->get();

        $result = [];
        foreach ($produks as $produk) {
            $result[] = [
                'id' => $produk->id,
                'text' => $produk->nama_produk,
                'nama_kategori' => $produk->kategori ? $produk->kategori->nama_kategori : null,
                'harga_beli' => $produk->harga_beli,
                'harga_jual' => $produk->harga_jual,
                'stok_siap' => $produk->stok_siap,
            ];
        }

        return response()->json(['results' => $result]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProdukMasuk $produkMasuk)
    {
        //
    }
}
