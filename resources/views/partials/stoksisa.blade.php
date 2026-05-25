<div class="pending-detail-card">
    <h3 class="pending-title">📦 Pemeriksaan barang untuk produk <span>{{ $produk->nama_produk }} ({{$produk->kategori->nama_kategori}})</span></h3>

    <table class="pending-table">
        <thead>
            <tr>
                <th>Batch</th>
                <th>Periode</th>
                <th>Total</th>
                <th>Keluar</th>
                <th>Sisa</th>
                <th>Koreksi</th>
            </tr>
        </thead>
            <tbody>
                @forelse($produk->batchStoks as $batch)

                    <tr>
                        <td>{{ $batch->kode_batch }}</td>
                        <td>
                            {{ $batch->tanggal_awal->format('d M Y') }}
                            -
                            {{ $batch->tanggal_akhir->format('d M Y') }}
                        </td>
                        <td>{{ $batch->total_stok }}</td>
                        <td>{{ $batch->stok_keluar }}</td>
                        <td>{{ $batch->sisa }}</td>
                        <td>
                            <a href="#modalKoreksi-{{ $batch->id }}" class="btn-process">
                                Koreksi
                            </a>

                            <div id="modalKoreksi-{{ $batch->id }}" class="modal-pending">
                                <div class="modal-content">

                                    <h4>Koreksi Batch Stok</h4>

                                    <form method="POST"
                                        action="{{ url('/koreksi/proses/' . $batch->id) }}">

                                        @csrf
                                        @method('PATCH')

                                        <div>
                                            <label>Kode Batch</label>
                                            <input type="text"
                                                value="{{ $batch->kode_batch }}"
                                                readonly>
                                        </div>

                                        <div>
                                            <label>Total Batch</label>
                                            <input type="text"
                                                value="{{ $batch->total_stok }}"
                                                readonly>
                                        </div>

                                        <div>
                                            <label>Stok Keluar</label>
                                            <input type="text"
                                                value="{{ $batch->stok_keluar }}"
                                                readonly>
                                        </div>

                                        <div>
                                            <label>Sisa Sistem</label>
                                            <input type="text"
                                                value="{{ $batch->sisa }}"
                                                readonly>
                                        </div>

                                        <div>
                                            <label>Sisa Fisik Setelah Opname</label>

                                            <input
                                                type="number"
                                                step="0.01"
                                                name="stok_fisik"
                                                required
                                            >
                                        </div>

                                        <div>
                                            <label>Keterangan</label>

                                            <textarea
                                                name="keterangan"
                                                rows="3"
                                            ></textarea>
                                        </div>

                                        <div class="modal-actions">

                                            <button type="submit"
                                                class="btn-process">
                                                Simpan
                                            </button>

                                            <a href="#"
                                                class="btn-cancel">
                                                Batal
                                            </a>

                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="5" class="text-center">
                            Tidak ada stok tersisa.
                        </td>
                    </tr>

                @endforelse
            </tbody>
    </table>
</div>

