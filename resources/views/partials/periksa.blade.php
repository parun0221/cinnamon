<div class="pending-detail-card">
    <h3 class="pending-title">📦 Pemeriksaan barang untuk produk <span>{{ $produk->nama_produk }} ({{$produk->kategori->nama_kategori}})</span></h3>

    <table class="pending-table">
        <thead>
            <tr>
                <th>Tanggal Masuk</th>
                <th>Qty (Kg)</th>
                <th>Harga Satuan</th>
                <th>Status</th>
                <th>Supplier</th>
                <th>Diterima</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($produk->produkMasukDetails as $detail)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($detail->produkMasuk->tanggal_masuk)->format('d M Y') }}</td>
                    @if ($detail->berat_final === null || $detail->berat_final == 0)
                        <td>{{ number_format($detail->berat_awal, 2) }}</td>
                    @else
                        <td>{{ number_format($detail->berat_final, 2) }}</td>
                    @endif

                    <td>Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                    <td>{{ $detail->status}}</td>
                    <td>{{ $detail->produkMasuk->supplier->nama_supplier ?? '-' }}</td>
                    <td>{{ $detail->produkMasuk->add_by ?? '-' }}</td>
                    <td>
                        <a href="#modalProses-{{ $detail->id }}" class="btn-process">
                            Proses
                        </a>
                        <!-- Modal -->
                        <div id="modalProses-{{ $detail->id }}" class="modal-pending">
                            <div class="modal-content">
                                <h4>Verifikasi Produk Masuk</h4>

                                <form method="POST" action="{{ url('/periksa/proses/' . $detail->id) }}">
                                    @csrf
                                    @method('PATCH')
 
                                    <div>
                                        <label>Berat Awal (Kg)</label>
                                        @if ($detail->berat_final === null || $detail->berat_final == 0)
                                            <input type="text" value="{{ $detail->berat_awal }}" readonly>
                                        @else
                                            <input type="text" value="{{ $detail->berat_final }}" readonly>
                                        @endif
 
                                    </div>

                                    <div>
                                        <label>Kondisi Barang</label>
                                        <div class="radio-group">
                                            <label>
                                                <input 
                                                    type="radio" 
                                                    name="kondisi_barang" 
                                                    value="sesuai" 
                                                    checked
                                                >
                                                Sesuai
                                            </label>

                                            <label>
                                                <input 
                                                    type="radio" 
                                                    name="kondisi_barang" 
                                                    value="belum_sesuai"
                                                >
                                                Tidak Sesuai
                                            </label>
                                        </div>
                                    </div>

                                    <div class="berat-final-wrapper hidden">
                                        <label>Berat Final (Kg)</label>
                                        <input 
                                            type="number" 
                                            step="0.01" 
                                            name="berat_final"
                                        >
                                    </div>

                                    <div class="modal-actions">
                                        <button type="submit" class="btn-process">Simpan</button>
                                        <a href="#" class="btn-cancel">Batal</a>
                                    </div>
                                </form>
                            </div>
                        </div>



                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data pending.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

