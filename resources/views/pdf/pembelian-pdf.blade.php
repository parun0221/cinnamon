<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nota Pembelian</title>
    <link rel="stylesheet" href="{{ public_path('css/nota.css') }}">
</head>
<body>

<div class="nota">
    {{-- HEADER TOKO --}}
    <div class="nota-header">
        <h2 class="nota-toko">Gudang Wahana Eka Sentral</h2>
        <p class="nota-alamat">
            Jl. Perdagangan No. 12, Jakarta<br>
            Telp: 0812-3456-7890
        </p>
        <div class="nota-divider"></div>
        <h4 class="nota-judul">NOTA PEMBELIAN</h4>
    </div>

    {{-- INFO --}}
    <div class="nota-info">
        <div class="nota-info-left">
            <p><strong>No Nota</strong> : PM-{{ str_pad($produkMasuk->id, 6, '0', STR_PAD_LEFT) }}</p>
            <p><strong>Tanggal</strong> : {{ \Carbon\Carbon::parse($produkMasuk->tanggal_masuk)->format('d-m-Y') }}</p>
        </div>
        <div class="nota-info-right">
            <p><strong>Supplier</strong> : {{ $produkMasuk->supplier->nama_supplier ?? '-' }}</p>
            <p><strong>Petugas</strong> : -</p>
        </div>
    </div>

    {{-- TABEL --}}
    <table class="nota-table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="30%">Produk</th>
                <th width="20%">Kategori</th>
                <th width="10%">Qty</th>
                <th width="15%">Harga</th>
                <th width="20%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($produkMasuk->produkMasukDetails as $i => $row)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td>{{ $row->produk->nama_produk ?? '-' }}</td>
                <td>{{ $row->produk->kategori->nama_kategori ?? '-' }}</td>
                <td class="center">
                    {{ $row->berat_awal == 0 ? $row->berat_final : $row->berat_awal }}
                </td>
                <td class="right">{{ number_format($row->harga_satuan, 0) }}</td>
                <td class="right">{{ number_format($row->subtotal, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="right">TOTAL</th>
                <th class="right total-harga">
                    {{ number_format($produkMasuk->total_harga, 0) }}
                </th>
            </tr>
        </tfoot>
    </table>

    {{-- FOOTER --}}
    <div class="nota-footer">
        <p>Barang yang sudah dibeli tidak dapat dikembalikan.</p>
        <p class="nota-terima">Terima kasih 🙏</p>
    </div>
</div>

</body>
</html>
