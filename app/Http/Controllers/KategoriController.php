<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use App\Models\RiwayatStok;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreProdukRequest;
use App\Http\Requests\UpdateProdukRequest;
use App\Http\Requests\StoreKategoriRequest;
use App\Http\Requests\UpdateKategoriRequest;
use Carbon\Carbon;

use App\Exports\HistoryStokProdukExport;
use App\Models\BatchStok;
use App\Models\ProdukMasukDetail;
use Maatwebsite\Excel\Facades\Excel;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = Produk::with('kategori')
                ->withCount([
                    'produkmasukdetails as unapproved_count' => function ($q) {
                        $q->where('is_approved', false);
                    }
                ])
                ->orderByDesc('unapproved_count');


        // Filter search
        if ($request->filled('search')) {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

        // Filter kategori
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        $produk = $query->latest()->get();
        $kategori = Kategori::all();

        return view('stok.stok', compact('produk', 'kategori')); 
    }

    public function periksa($id)
    {
        $produk = Produk::with([
            'kategori',
            'produkMasukDetails' => function ($q) {
                $q->where('is_approved', false)
                ->with('produkMasuk'); // jika ingin ambil info tanggal/supplier dari tabel induk
            }
        ])->findOrFail($id);

        return view('partials.periksa', compact('produk'));
    }

    public function prosesPeriksa(Request $request, $id)
    {


        DB::transaction(function () use ($request, $id) {
            $detail = ProdukMasukDetail::with('produk', 'produkMasuk')->findOrFail($id);

            // if ($detail->status === 'selesai') {
            //     throw new \Exception('Data sudah diproses.');
            // }
            if ($request->kondisi_barang === 'sesuai') {

                $detail->update([
                    'is_approved' => true,
                    'approved_at' => now(),
                ]);
                if ($detail->status === 'selesai') {
                    $produk = $detail->produk;
                    $stok_siap_baru    = $produk->stok_siap + $detail->berat_final;

                    $produk->update([
                        'stok_siap'    => $stok_siap_baru
                    ]);

                    RiwayatStok::create([
                        'produk_id'    => $detail->produk_id,
                        'produk_masuk_detail_id' => $detail->id,
                        'jumlah'       => $detail->berat_final,
                        'tanggal'      => $detail->produkMasuk->tanggal_masuk,
                        'jenis'         => 'masuk',
                        'stok_siap'    => $stok_siap_baru,
                        'stok_pending' => $detail->produk->stok_pending,
                        'keterangan'   => 'Produk masuk yang telah di setujui'
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

                    $beratMasuk = $detail->berat_final;
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
                            'total_stok'    => $detail->berat_final,
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

                }elseif ($detail->status === 'pending') {
                    $produk = $detail->produk;

                    // Hitung stok baru
                    $stok_pending_baru =  $produk->stok_pending + $detail->berat_awal;

                    // Update stok di tabel produk
                    $produk->update([
                        'stok_pending' => $stok_pending_baru,
                    ]);

                    // Simpan riwayat stok
                    RiwayatStok::create([
                        'produk_id'    => $detail->produk_id,
                        'produk_masuk_detail_id' => $detail->id,
                        'jumlah'       => $detail->berat_awal,
                        'tanggal'      => $detail->produkMasuk->tanggal_masuk,
                        'jenis'         => 'masuk',
                        'stok_siap' => $detail->produk->stok_siap,
                        'stok_pending' => $stok_pending_baru,
                        'keterangan'   => 'Produk masuk yang telah di setujui'
                    ]);
                }
            }

            elseif ($request->kondisi_barang === 'belum_sesuai') {
                $request->validate([
                    'berat_final' => 'required|numeric|min:0'
                ]);
                if ($detail->status === 'selesai') {
                    $detail->update([
                        'berat_final' => $request->berat_final,
                        'is_approved' => true,
                        'approved_at' => now(),
                    ]);

                    $produk = $detail->produk;
                    $stok_siap_baru    = $produk->stok_siap + $request->berat_final;

                    $produk->update([
                        'stok_siap'    => $stok_siap_baru
                    ]);

                    RiwayatStok::create([
                        'produk_id'    => $detail->produk_id,
                        'produk_masuk_detail_id' => $detail->id,
                        'jumlah'       => $request ->berat_final,
                        'tanggal'      => $detail->produkMasuk->tanggal_masuk,
                        'jenis'         => 'masuk',
                        'stok_siap'    => $stok_siap_baru,
                        'stok_pending' => $detail->produk->stok_pending,
                        'keterangan'   => 'Produk masuk dalam keadaan pending yang telah di setujui'
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

                }elseif ($detail->status === 'pending') {
                    $detail->update([
                        'berat_awal' => $request->berat_final,
                        'is_approved' => true,
                        'approved_at' => now(),
                    ]);
                    $produk = $detail->produk;
                    $stok_pending_baru =  $produk->stok_pending + $request->berat_final;

                    $produk->update([
                        'stok_pending' => $stok_pending_baru,
                    ]);

                    // Simpan riwayat stok
                    RiwayatStok::create([
                        'produk_id'    => $detail->produk_id,
                        'produk_masuk_detail_id' => $detail->id,
                        'jumlah'       => $request->berat_final,
                        'tanggal'      => $detail->produkMasuk->tanggal_masuk,
                        'jenis'         => 'masuk',
                        'stok_siap' => $detail->produk->stok_siap,
                        'stok_pending' => $stok_pending_baru,
                        'keterangan'   => 'Produk masuk dalam keadaan pending yang telah di setujui'
                    ]);
                }
            }
            
        });

        return back()->with('success', 'Pending berhasil diproses.');
    }

    public function koreksiStok(Request $request, $id)
    {
        $request->validate([
            'stok_fisik' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request, $id) {

            $batch = BatchStok::lockForUpdate()
                ->findOrFail($id);
            $produk = Produk::lockForUpdate()
                ->findOrFail($batch->produk_id);

            $sisaSistem = $batch->total_stok - $batch->stok_keluar;

            $stokFisik = $request->stok_fisik;

            $selisih = $stokFisik - $sisaSistem;

            $totalBatchBaru = $batch->stok_keluar + $stokFisik;

            $batch->update([
                'total_stok' => $totalBatchBaru
            ]);

            $produk->update([
                'stok_siap' => $produk->stok_siap + $selisih
            ]);

            RiwayatStok::create([
                'produk_id' => $produk->id,
                'tanggal' => now(),
                'jenis' => 'penyesuaian',
                'jumlah' => $selisih,
                'stok_siap' => $produk->stok_siap,
                'stok_pending' => $produk->stok_pending,
                'keterangan' => $request->keterangan ?? 'Koreksi batch stok',
            ]);
        });

        return back()->with(
            'success',
            'Koreksi batch berhasil dilakukan.'
        );
    }

public function stoksisa($id, Request $request)
{
    $produk = Produk::with([
        'kategori',
        'batchStoks' => function ($q) {

            // hanya batch yang masih memiliki sisa stok
            $q->whereRaw('total_stok > stok_keluar')

            // FIFO batch paling lama
            ->orderBy('tanggal_awal', 'asc');
        }

    ])->findOrFail($id);

    return view('partials.stoksisa', compact('produk'));
}


public function history(Request $request)
{
    $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
    $endDate   = $request->end_date ?? Carbon::now()->format('Y-m-d');

    $query = Produk::with(['kategori', 'riwayatStoks' => function ($q) use ($startDate, $endDate) {
        if ($startDate && $endDate) {
            $q->whereBetween('tanggal', [$startDate, $endDate]);
        }
        $q->orderBy('tanggal', 'desc');
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

    return view('stok.history', compact('kategori', 'produk', 'startDate', 'endDate'));
}



    public function historyDetail($id, Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate   = $request->end_date ?? Carbon::now()->format('Y-m-d');

        $details = Produk::with([
            'kategori',
            'riwayatStoks' => function ($q) use ($startDate, $endDate) {
                if ($startDate && $endDate) {
                    $q->whereBetween('tanggal', [$startDate, $endDate]);
                }
                $q->orderBy('tanggal', 'asc');
            }
        ])->findOrFail($id);

        $chartData = DB::table('riwayat_stoks')
        ->selectRaw("
            DATE(tanggal) as tanggal,
            SUM(CASE WHEN jenis = 'masuk' THEN jumlah ELSE 0 END) as masuk,
            SUM(CASE WHEN jenis = 'keluar' THEN jumlah ELSE 0 END) as keluar,
            SUM(CASE WHEN jenis = 'penyesuaian' THEN jumlah ELSE 0 END) as penyesuaian
        ")
        ->where('produk_id', $id)
        ->whereBetween('tanggal', [$startDate, $endDate])
        ->groupBy(DB::raw('DATE(tanggal)'))
        ->orderBy('tanggal')
        ->get();

        $stokAwal = RiwayatStok::where('produk_id', $id)
            ->where('tanggal', '<', $startDate)
            ->selectRaw("
                SUM(
                    CASE 
                        WHEN jenis = 'masuk' THEN jumlah
                        WHEN jenis = 'keluar' THEN -jumlah
                        WHEN jenis = 'penyesuaian' THEN jumlah
                        ELSE 0
                    END
                ) as stok
            ")
            ->value('stok') ?? 0;


        $labels = [];
        $values = [];
        $masuk = [];
        $keluar = [];
        $penyesuaian = [];

        $currentStok = $stokAwal;

        foreach ($chartData as $row) {

            $labels[] = $row->tanggal;

            $masuk[] = $row->masuk;
            $keluar[] = $row->keluar;
            $penyesuaian[] = $row->penyesuaian;

            $currentStok += $row->masuk;
            $currentStok -= $row->keluar;
            $currentStok += $row->penyesuaian; // 🔥 dibalik

            $values[] = $currentStok;
        }


        return view('partials.history_stok',compact('labels', 'values', 'masuk', 'keluar', 'penyesuaian', 'details', 'startDate', 'endDate','stokAwal')
    );
    }

    public function historyExcel($id, Request $request)
    {
        $startDate = $request->filled('start_date')
            ? $request->start_date
            : Carbon::now()->startOfMonth()->format('Y-m-d');

        $endDate = $request->filled('end_date')
            ? $request->end_date
            : Carbon::now()->format('Y-m-d');


        $rows = RiwayatStok::with([
            'produk.kategori',
        ])
        ->where('produk_id', $id)
        ->whereBetween('tanggal', [$startDate, $endDate])
        ->orderBy('tanggal', 'asc')
        ->get();

        return Excel::download(
            new HistoryStokProdukExport($rows),
            "history_stok_produk_$startDate-$endDate.xlsx"
        );
    }

public function laporan(Request $request)
{
    // Default tanggal
    $startDate = $request->input('start_date') ?? now()->startOfMonth()->toDateString();
    $endDate   = $request->input('end_date') ?? now()->toDateString();

    $query = DB::table('produks as p')
        ->join('kategoris as k', 'k.id', '=', 'p.kategori_id')
        ->select(
            'p.id as produk_id',
            'p.nama_produk as nama_p',
            'k.nama_kategori as nama_k',

            // stok_awal: stok_siap terakhir sebelum startDate
            DB::raw("COALESCE((
                SELECT rs.stok_siap
                FROM riwayat_stoks rs
                WHERE rs.produk_id = p.id
                  AND rs.tanggal < " . DB::getPdo()->quote($startDate) . "
                ORDER BY rs.tanggal DESC, rs.id DESC
                LIMIT 1
            ), 0) as stok_awal"),

            // masuk dalam periode
            DB::raw("COALESCE((
                SELECT SUM(rs.jumlah)
                FROM riwayat_stoks rs
                WHERE rs.produk_id = p.id
                  AND rs.jenis = 'masuk'
                  AND rs.tanggal BETWEEN " . DB::getPdo()->quote($startDate) . " AND " . DB::getPdo()->quote($endDate) . "
            ), 0) as masuk"),

            // keluar dalam periode
            DB::raw("COALESCE((
                SELECT SUM(rs.jumlah)
                FROM riwayat_stoks rs
                WHERE rs.produk_id = p.id
                  AND rs.jenis = 'keluar'
                  AND rs.tanggal BETWEEN " . DB::getPdo()->quote($startDate) . " AND " . DB::getPdo()->quote($endDate) . "
            ), 0) as keluar")
        );

    if ($request->filled('kategori')) {
        $query->where('p.kategori_id', $request->kategori);
    }

    $laporan = $query->get();

    // hitung stok_akhir di PHP (stok_awal + masuk - keluar)
    $laporan->transform(function ($item) {
        $item->stok_akhir = $item->stok_awal + $item->masuk - $item->keluar;
        return $item;
    });

    $kategori = DB::table('kategoris')->get();

    return view('stok.laporan', compact('laporan', 'kategori', 'startDate', 'endDate'));
}



    public function kategori()
    {
        $kategori = Kategori::withCount('produk')->get();
        return view('produk.kategori', compact('kategori'));
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
            'name'  => 'required|string|max:255',

        ]);

        Kategori::create([
            'nama_kategori'     => $request->name,
        ]);


        return back()->with('success', 'Kategori berhasil ditambahkan');
    }


    /**
     * Display the specified resource.
     */
    public function show(Kategori $kategori)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kategori $kategori)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:150'
        ]);

        $kategori = Kategori::findOrFail($id);

        $kategori->update([
            'name' => $request->name
        ]);

        return redirect()
            ->route('kategori.index')
            ->with('success', 'Kategori berhasil diperbarui');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $kategori = Kategori::findOrFail($id);

            $kategori->delete();

            return response()->json(['success' => true, 'message' => 'Kategori berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
