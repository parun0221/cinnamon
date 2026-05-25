<div class="dashboard-main">
    <div class="table-responsive">
        <table class="table table-striped">
            @if(auth()->user()->role === 'admin')
        <div class="d-flex justify-content-end mb-3 gap-2">
            <a href="{{ route('input.history.excel', [
                'produk' => $details->id,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]) }}"
            class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
        </div>
            @endif

            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Berat Awal</th>
                    <th>Berat Final</th>
                    <th>Berat Keluar</th>
                    <th>Harga Beli</th>
                    <th>Subtotal</th>
                    {{-- <th>Harga Jual</th>
                    <th>Income</th> --}}
                    <th>Supplier</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($details ->produkMasukDetails as $d)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($d->produkMasuk->tanggal_masuk)->format('d M Y') }}</td>
                        <td>{{ number_format($d->berat_awal, 2) }}</td>
                        <td>{{ number_format($d->berat_final, 2) }}</td>
                        <td>{{ number_format($d->berat_keluar, 2) }}</td>
                        <td>{{ number_format($d->harga_satuan, 0) }}</td>
                        <td>{{ number_format($d->subtotal, 0) }}</td>
                        {{-- <td>{{ number_format($d->produk->harga_jual, 0) }}</td>
                        <td>{{ number_format($d->produk->harga_jual*$d->berat_final - $d->subtotal, 0) }}</td> --}}

                        <td>{{ $d->produkMasuk->supplier->nama_supplier ?? '-' }}</td>
                        <td>{{ $d->status }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(count($values) > 0)
    <style>
    .dashboard-graph {
        position: relative;
        height: 320px;      /* 🔥 HARUS ADA */
        width: 100%;
        margin-bottom: 20px;
    }

    .dashboard-graph canvas {
        width: 100% !important;
        height: 100% !important;
    }
    </style>
    <div class="dashboard-graph">
        <h4>Grafik Transaksi</h4>

        <canvas id="produkMasukChart"
            data-labels='@json($labels)'
            data-values='@json($values)'>
        </canvas>
    </div>

    @endif
</div>