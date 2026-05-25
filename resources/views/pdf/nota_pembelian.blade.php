<div class="nota">
    {{-- HEADER TOKO --}}
    <div class="nota-header">
        <h2 class="nota-toko">Gudang Wahana Eka Sentral</h2>
        <p class="nota-alamat">
            Jl. Perdagangan No. 12, Jakarta<br>
            Telp: 0812-3456-7890
        </p>
        <h4 class="nota-judul">NOTA PEMBELIAN</h4>
    </div>

    {{-- INFO --}}
    <div class="nota-info">
        <div>
            <p><strong>No Nota</strong> : PM-{{ str_pad($produkMasuk->id, 6, '0', STR_PAD_LEFT) }}</p>
            <p><strong>Tanggal</strong> : {{ $produkMasuk->tanggal_masuk }}</p>
        </div>
        <div>
            <p><strong>Supplier</strong> : {{ $produkMasuk->supplier->nama_supplier ?? '-' }}</p>
            <p><strong>Petugas</strong> : {{ $produkMasuk->add_by ?? '-' }}</p>
        </div>
    </div>

    {{-- TABEL --}}
    <table class="nota-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Produk</th>
                <th>Kategori</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($produkMasuk->produkMasukDetails as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $row->produk->nama_produk ?? '-' }}</td>
                <td>{{ $row->produk->kategori->nama_kategori ?? '-' }}</td>
                <td class="center">{{ $row->berat_awal == 0 ? $row->berat_final : $row->berat_awal }}</td>
                <td class="right">{{ number_format($row->harga_satuan, 0) }}</td>
                <td class="right">{{ number_format($row->subtotal, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="right">TOTAL</th>
                <th class="right">{{ number_format($produkMasuk->total_harga, 0) }}</th>
            </tr>
        </tfoot>
    </table>

    {{-- FOOTER --}}
    <div class="nota-footer">
        <p>Barang yang sudah dibeli tidak dapat dikembalikan</p>
        <p class="nota-terima">Terima kasih 🙏</p>
    </div>
</div>
