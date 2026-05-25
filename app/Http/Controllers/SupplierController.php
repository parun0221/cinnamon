<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Supplier;
use App\Models\ProdukMasuk;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\ProdukKeluar;
use Barryvdh\DomPDF\Facade\Pdf;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Supplier::withCount('produkMasuk')
        ->where('tipe', 'from');

        // Filter search
        if ($request->filled('search')) {
            $query->where('nama_supplier', 'like', '%' . $request->search . '%');
        }

        $suppliers = $query->get();

        return view('input.suppfrom', compact('suppliers'));
    }

    public function show(Request $request, $id)
    {

        $startDate = $request->filled('start_date')
            ? $request->start_date
            : Carbon::now()->startOfMonth()->format('Y-m-d');

        $endDate = $request->filled('end_date')
            ? $request->end_date
            : Carbon::now()->format('Y-m-d');

        $details = ProdukMasuk::with('produkMasukDetails.produk.kategori')
        ->where('supplier_id', $id)
        ->whereBetween('tanggal_masuk', [$startDate, $endDate])
        ->get();

        $supplier = Supplier::find($id);

        return view('input.frominfo', compact('details', 'supplier', 'startDate', 'endDate'));
    }

    public function pembelian(ProdukMasuk $produkMasuk)
    {
        $produkMasuk->load([
            'supplier',
            'produkMasukDetails.produk.kategori'
        ]);

        return view('pdf.nota_pembelian', compact('produkMasuk'));
    }



    public function pembelianPdf(ProdukMasuk $produkMasuk)
    {
        $produkMasuk->load([
            'supplier',
            'produkMasukDetails.produk.kategori'
        ]);

        $pdf = Pdf::loadView('pdf.pembelian-pdf', [
            'produkMasuk' => $produkMasuk
        ])->setPaper('a4', 'portrait');

        return $pdf->stream(
            'Nota-Pembelian-' . $produkMasuk->id . '.pdf'
        );
    }

        public function index1(Request $request)
    {
        $query = Supplier::withCount('produkKeluar')
        ->where('tipe', 'to');

        // Filter search
        if ($request->filled('search')) {
            $query->where('nama_supplier', 'like', '%' . $request->search . '%');
        }

        $suppliers = $query->get();

        return view('output.suppto', compact('suppliers'));
    }

    public function show1(Request $request, $id)
    {

        $startDate = $request->filled('start_date')
            ? $request->start_date
            : Carbon::now()->startOfMonth()->format('Y-m-d');

        $endDate = $request->filled('end_date')
            ? $request->end_date
            : Carbon::now()->format('Y-m-d');

        $details = ProdukKeluar::with('produkKeluarDetails.produk.kategori')
        ->where('supplier_id', $id)
        ->whereBetween('tanggal_keluar', [$startDate, $endDate])
        ->get();

        $supplier = Supplier::find($id);

        return view('output.toinfo', compact('details', 'supplier', 'startDate', 'endDate'));
    }

    public function penjualan(ProdukKeluar $produkKeluar)
    {
        $produkKeluar->load([
            'supplier',
            'produkKeluarDetails.produk.kategori'
        ]);

        $penjualan = $produkKeluar;

        return view('pdf.faktur_penjualan', compact('penjualan'));
    }



    public function penjualanPdf(ProdukKeluar $produkKeluar)
    {
        $produkKeluar->load([
            'supplier',
            'produkKeluarDetails.produk.kategori'
        ]);

        $penjualan = $produkKeluar;

        $pdf = Pdf::loadView('pdf.penjualan-pdf', [
            'penjualan' => $penjualan
        ])->setPaper('a4', 'portrait');

        return $pdf->stream(
            'Faktur-Penjualan-' . $produkKeluar->id . '.pdf'
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
            'nama_supplier' => 'required|string|max:255|unique:suppliers,nama_supplier',
        ], [
            'nama_supplier.unique' => 'Supplier sudah ada.',
            'nama_supplier.required' => 'Nama supplier wajib diisi.',
        ]);

        $supplier = Supplier::create([
            'nama_supplier' => $request->nama_supplier,
            'tipe' => $request->tipe ?? 'from',
            'alamat' => $request->alamat ?? null,
            'kontak' => $request->phone ?? null,
            'email' => $request->email ?? null,

        ]);

        return back()->with('success', 'Supplier berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, Supplier $supplier)
{
    $request->validate([
        'nama_supplier' => 'required|string|max:255|unique:suppliers,nama_supplier,' . $supplier->id,
        'email' => 'nullable|email',
        'phone' => 'nullable|string|max:20',
        'alamat' => 'nullable|string|max:255',
    ]);

    $supplier->update($request->only([
        'nama_supplier',
        'email',
        'kontak',
        'alamat',
        'tipe',
    ]));

    return redirect()
        ->back()
        ->with('success', 'Data klien berhasil diperbarui');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        //
    }
}
