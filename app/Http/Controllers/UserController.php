<?php

namespace App\Http\Controllers;

use App\Models\BukuBesar;
use Carbon\Carbon;
use App\Models\Produk;
use App\Models\RiwayatStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
{

    $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
    $endDate   = $request->end_date ?? now()->toDateString();

    $produkId = $request->produk_id
        ?? Produk::orderBy('nama_produk')->value('id');

    $produkList = Produk::with('kategori')
     ->orderBy('nama_produk')
     ->get();

    $saldoKas = BukuBesar::orderBy('tanggal', 'desc')
    ->orderBy('id', 'desc')
    ->value('saldo') ?? 0;

    $pendingProdukCount = Produk::whereHas('produkmasukdetails', function ($q) {
        $q->where('is_approved', false);
    })->count();

    $produkMasukHariIni = DB::table('produk_masuk_details')
        ->whereDate('created_at', Carbon::today())
        ->count();
    $produkKeluarHariIni = DB::table('produk_keluar_details')
        ->whereDate('created_at', Carbon::today())
        ->count();
    $produkStokRendah = Produk::with('kategori')
        ->whereColumn('stok_siap', '<=', 'stok_minimum')
        ->get();
    
    
    // ambil riwayat stok
    $riwayat = DB::table('riwayat_stoks')
        ->where('produk_id', $produkId)
        ->whereBetween('tanggal', [$startDate, $endDate])
        ->orderBy('tanggal')
        ->get()
        ->groupBy('tanggal');

    // stok awal sebelum startDate
    $stokAwal = RiwayatStok::where('produk_id', $produkId)
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
    $masuk = [];
    $keluar = [];
    $penyesuaian = [];
    $stok = [];

    $stokBerjalan = $stokAwal;

    foreach ($riwayat as $tanggal => $items) {
        $labels[] = Carbon::parse($tanggal)->format('Y-m-d');
        $jMasuk = 0;
        $jKeluar = 0;
        $jPenyesuaian = 0;

        foreach ($items as $row) {
            if ($row->jenis === 'masuk') {
                $jMasuk += $row->jumlah;
                $stokBerjalan += $row->jumlah;
            }

            if ($row->jenis === 'keluar') {
                $jKeluar += $row->jumlah;
                $stokBerjalan -= $row->jumlah;
            }

            if ($row->jenis === 'penyesuaian') {
                $jPenyesuaian += $row->jumlah;

                if ($row->jumlah > 0) {
                    $stokBerjalan -= $row->jumlah;
                } else {
                    $stokBerjalan += abs($row->jumlah);
                }
            }
        }

        $masuk[] = $jMasuk;
        $keluar[] = $jKeluar;
        $penyesuaian[] = $jPenyesuaian;
        $stok[] = $stokBerjalan;
    }

    $stokRendah = Produk::with('kategori')
        ->orderBy('stok_siap', 'asc')
        ->limit(5)
        ->get();

    return view('menu.dashboard', compact(
        'produkList',
        'produkId',
        'labels',
        'masuk',
        'keluar',
        'penyesuaian',
        'stok',
        'stokRendah',
        'startDate',
        'endDate'
        ,'saldoKas',
        'pendingProdukCount',
        'produkMasukHariIni',
        'produkKeluarHariIni',
        'produkStokRendah'
    ));
}

    public function akun()
    {
        $akun = Auth::user();
        return view('menu.akun', compact('akun'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password sekarang tidak sesuai.'
            ]);
        }

        $user->update (['password' => Hash::make($request->new_password)]);
        

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    public function akunlist()
    {
        $akun = User::all()->where('role', 'karyawan');
        return view('menu.akunlist', compact('akun'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
        'name'  => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'phone' => 'nullable|string|max:20',
    ]);

    User::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'phone'    => $request->phone,
        'password' => Hash::make('12345678'), 
        'role'     => 'karyawan',
    ]);

    return back()->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);

            $user->delete();

            return response()->json(['success' => true, 'message' => 'User berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function resetPassword($id)
    {
        try {
            // Cari user berdasarkan ID
            $employee = User::findOrFail($id);

            // Reset password ke default (12345678)
            $employee->password = Hash::make('12345678');
            $employee->save();
            return response()->json(['success' => true, 'message' => 'Password berhasil direset.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // hitung setelah login berhasil
            $pendingProdukCount = Produk::whereHas('produkmasukdetails', function ($q) {
                $q->where('is_approved', false);
            })->count();

            if ($pendingProdukCount > 0) {
                session()->flash('pending_produk', $pendingProdukCount);
            }

            return redirect()
                ->intended('/dashboard')
                ->with('success', 'Login berhasil! Selamat datang.');
        }

        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->with('error', 'Login gagal! Periksa kembali email dan password Anda.')
            ->withInput();
    }

  
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return view('login');
    }
}
