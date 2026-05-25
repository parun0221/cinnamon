<div class="pending-detail-card">
    <h3 class="pending-title">📦 Pending untuk <span>{{ $produk->nama_produk }}</span></h3>

    <table class="pending-table">
        <thead>
            <tr>
                <th>Tanggal Masuk</th>
                <th>Qty Pending (Kg)</th>
                <th>Harga Satuan</th>
                <th>Supplier</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($produk->produkMasukDetails as $detail)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($detail->produkMasuk->tanggal_masuk)->format('d M Y') }}</td>
                    <td>{{ number_format($detail->berat_awal, 2) }}</td>
                    <td>Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                    <td>{{ $detail->produkMasuk->supplier->nama_supplier ?? '-' }}</td>
                    <td>
                        <a href="#modalProses-{{ $detail->id }}" class="btn-process">
                            Proses
                        </a>
                        <!-- Modal -->
                        <div id="modalProses-{{ $detail->id }}" class="modal-pending">
                            <div class="modal-content">
                                <h4>Proses Pending</h4>
                                <form method="POST" action="{{ url('/pending/proses/' . $detail->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <div>
                                        <label>Berat Awal (Kg)</label>
                                        <input type="text" value="{{ $detail->berat_awal }}" readonly>
                                    </div>
                                    <div>
                                        <label>Berat Final (Kg)</label>
                                        <input type="number" step="0.01" name="berat_final" required>
                                    </div>
                                    <button type="submit" class="btn-process">Simpan</button>
                                    <a href="#" class="btn-cancel">Batal</a>
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

