<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nota Pembelian</title>
    <link rel="stylesheet" href="{{ public_path('css/nota.css') }}">
</head>
<body>


<div class="faktur">

    {{-- HEADER --}}
    <table width="100%" class="faktur-header-table">
        <tr>
            <td width="70%" valign="top">
                <img src="{{ public_path('images/loginback2.png') }}" class="faktur-logo">
                <div class="faktur-company">
                    <strong>Gudang Wahana Eka Sentral</strong><br>
                    Jl. Bukittinggi- Payakumbuh, Kec.Baso, Kab.Agam<br>
                    Telp. (021) 111 2222<br>
                    Email: Cinnamon@store.com
                </div>
            </td>
            <td width="30%" align="right" valign="top">
                <h1 class="faktur-title">FAKTUR</h1>
            </td>
        </tr>
    </table>

    <hr class="divider">

    {{-- INFO --}}
    <table width="100%" class="faktur-info-table">
        <tr>
            <td width="60%" valign="top">
                <strong>Kepada:</strong><br>
                {{ $penjualan->supplier->nama_supplier ?? '-' }}<br>
                {{ $penjualan->supplier->alamat ?? '-' }}<br>
                {{ $penjualan->supplier->kontak ?? '-' }}<br>
                {{ $penjualan->supplier->email ?? '-' }}
            </td>
            <td width="40%" valign="top" align="right">
                <strong>Tanggal</strong> : {{ $penjualan->tanggal_keluar }}<br>
                <strong>No. Faktur</strong> :
                FP-{{ str_pad($penjualan->id, 6, '0', STR_PAD_LEFT) }}<br>
                <strong>Jatuh Tempo</strong> : {{ $penjualan->tanggal_keluar }}
            </td>
        </tr>
    </table>


    {{-- TABLE --}}
    <table class="faktur-table">
        <thead>
            <tr>
                <th>No</th>
                <th>ID</th>
                <th>Deskripsi</th>
                <th>Qty</th>
                <th>Harga Satuan</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penjualan->produkKeluarDetails as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $row->produk->id ?? '-' }}</td>
                <td>{{ $row->produk->nama_produk ?? '-' }}</td>
                <td class="center">{{ $row->jumlah }}</td>
                <td class="right">{{ number_format($row->harga_satuan, 0) }}</td>
                <td class="right">{{ number_format($row->subtotal, 0) }}</td>
            </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td colspan="5" class="right bold">Total</td>
                <td class="right bold">
                    {{ number_format($penjualan->total_harga, 0) }}
                </td>
            </tr>
        </tfoot>
    </table>

    {{-- KETERANGAN --}}
    <div class="faktur-note">
        <p><strong>Keterangan:</strong></p>
        <ul>
            <li>Pembayaran kredit jatuh tempo 15 hari setelah faktur diterbitkan.</li>
            <li>Pembayaran dilakukan sesuai nomor faktur.</li>
            <li>Untuk konfirmasi pembayaran silakan hubungi kami.</li>
        </ul>
    </div>

</div>



</body>
</html>
