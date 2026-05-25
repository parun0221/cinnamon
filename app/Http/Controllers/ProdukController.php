<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreProdukRequest;
use App\Http\Requests\UpdateProdukRequest;
use App\Models\RiwayatStok;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\BatchStok;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Produk::with('kategori');

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

        return view('produk.produklist', compact('produk', 'kategori'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $query = Produk::with('kategori');
        $produk = $query->orderBy('nama_produk')->get();
        $kategori = Kategori::orderBy('nama_kategori')->get();

        return view('produk.produkcreate', compact('produk', 'kategori'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'produk.*.nama_produk'   => 'required|string|max:255',
        'produk.*.kategori_id'   => 'required|exists:kategoris,id',
        'produk.*.satuan'        => 'required|string|max:50',
        'produk.*.stok_siap'     => 'nullable|numeric|min:0',
        'produk.*.stok_pending'  => 'nullable|numeric|min:0',
        'produk.*.harga_jual'    => 'nullable|numeric|min:0',
        'produk.*.harga_beli'    => 'nullable|numeric|min:0',
        'produk.*.gambar'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $produkData = $request->input('produk', []);

    // Cek duplikat dalam request
    $combos = [];
    foreach ($produkData as $p) {
        $combo = strtolower($p['nama_produk']) . '|' . $p['kategori_id'];
        if (in_array($combo, $combos)) {
            return back()
                ->with('error', 'Produk "' . $p['nama_produk'] . '" dengan kategori yang sama tidak boleh duplikat dalam satu input.')
                ->withInput();
        }
        $combos[] = $combo;
    }

    // Simpan data jika tidak ada di DB
    foreach ($produkData as $index => $p) {
        $exists = Produk::whereRaw('LOWER(nama_produk) = ?', [strtolower($p['nama_produk'])])
            ->where('kategori_id', $p['kategori_id'])
            ->exists();

        if ($exists) {
            return back()
                ->with('error', 'Produk "' . $p['nama_produk'] . '" dengan kategori ini sudah ada di database.')
                ->withInput();
        }

        $gambarPath = null;
        if ($request->hasFile("produk.$index.gambar")) {
            $gambarPath = $request->file("produk.$index.gambar")->store('produk', 'public');
        }

        $produk = Produk::create([
            'nama_produk'  => $p['nama_produk'],
            'kategori_id'  => $p['kategori_id'],
            'satuan'       => $p['satuan'],
            'stok_siap'    => $p['stok_siap'] ?? 0,
            'stok_pending' => $p['stok_pending'] ?? 0,
            'harga_jual'   => $p['harga_jual'] ?? 0,
            'harga_beli'   => $p['harga_beli'] ?? 0,
            'gambar'       => $gambarPath,
        ]);

        // Simpan riwayat stok dengan produk_id dari produk yang baru dibuat
        RiwayatStok::create([
            'produk_id'    => $produk->id, 
            'tanggal'       => Carbon::now(),
            'jenis'         => 'masuk',
            'jumlah'        => $p['stok_siap'] ?? 0,
            'stok_siap'    => $p['stok_siap'] ?? 0,
            'stok_pending' => $p['stok_pending'] ?? 0,
        ]);

        // BATCH STOK AWAL
        // =====================================================

        $stokAwal = $p['stok_siap'] ?? 0;

        if ($stokAwal > 0) {

            $tanggal = Carbon::now();

            // batch 1 = tanggal 1-15
            if ($tanggal->day <= 15) {

                $tanggalAwal = $tanggal->copy()->startOfMonth();

                $tanggalAkhir = $tanggal->copy()
                    ->startOfMonth()
                    ->addDays(14);

                $kodeBatch =
                    strtoupper($tanggal->translatedFormat('M')) .
                    '-1-' .
                    $tanggal->year;

            } else {

                // batch 2 = tanggal 16-akhir bulan
                $tanggalAwal = $tanggal->copy()
                    ->startOfMonth()
                    ->addDays(15);

                $tanggalAkhir = $tanggal->copy()->endOfMonth();

                $kodeBatch =
                    strtoupper($tanggal->translatedFormat('M')) .
                    '-2-' .
                    $tanggal->year;
            }

            BatchStok::create([
                'produk_id'     => $produk->id,
                'kode_batch'    => $kodeBatch,
                'tanggal_awal'  => $tanggalAwal,
                'tanggal_akhir' => $tanggalAkhir,
                'total_stok'    => $stokAwal,
                'stok_keluar'   => 0,
                'catatan'       => 'Batch awal saat produk dibuat',
            ]);
        }
    }

    return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan.');
}


    /**
     * Display the specified resource.
     */
    public function show(Produk $produk)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Produk $produk)
    {
        //
    }

    public function updateList(Request $request)
    {
        $query = Produk::with('kategori');

        // search by nama_produk
        if ($request->filled('search')) {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

        // filter by kategori
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        $produk = $query->orderBy('nama_produk')->get();
        $kategori = Kategori::orderBy('nama_kategori')->get();

        return view('produk.produkupdate', compact('produk', 'kategori'));
    }


    public function quickUpdate(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        try {
            $validated = $request->validate(
                [
                    'nama_produk' => [
                        'required',
                        'string',
                        'max:100',
                        Rule::unique('produks')
                            ->where(function ($q) use ($request) {
                                return $q->where('kategori_id', $request->kategori_id);
                            })
                            ->ignore($produk->id),
                    ],
                    'kategori_id'   => 'required|exists:kategoris,id',
                    'satuan'        => 'required|string|max:50',
                    'stok_siap'     => 'nullable|numeric|min:0',
                    'stok_pending'  => 'nullable|numeric|min:0',
                    'harga_jual'    => 'nullable|numeric|min:0',
                    'harga_beli'    => 'nullable|numeric|min:0',
                    'gambar'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                ],
                [
                    'nama_produk.unique' => 'Produk dengan kategori ini sudah ada.',
                ]
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Gagal memperbarui produk.');
        }

        // Upload gambar
        if ($request->hasFile('gambar')) {
            if ($produk->gambar && Storage::disk('public')->exists($produk->gambar)) {
                Storage::disk('public')->delete($produk->gambar);
            }

            $validated['gambar'] = $request->file('gambar')->store('produk', 'public');
        }

        $produk->update($validated);

        return back()->with('success', 'Produk berhasil diperbarui.');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProdukRequest $request, Produk $produk)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produk $produk)
    {
        $produk = Produk::findOrFail($produk->id);

        // Hapus file gambar jika ada
        if ($produk->gambar && Storage::exists($produk->gambar)) {
            Storage::delete($produk->gambar);
        }

        // Hapus data produk
        $produk->delete();

        // Redirect dengan pesan sukses
        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus.');
   
    }
}
