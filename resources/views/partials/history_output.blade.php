<div class="dashboard-main">
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            @if(auth()->user()->role === 'admin')
            <div class="d-flex justify-content-end mb-3 gap-2">
                <a href="{{ route('output.history.excel', [
                    'produk' => $details->id,
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]) }}"
                class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
            </div>
            @endif

            <thead >
                <tr>
                    <th>Tanggal Keluar</th>
                    <th>Produk</th>
                    <th>Berat Keluar (Kg)</th>
                    <th>Harga Satuan</th>
                    <th>Subtotal</th>
                    <th>Hpp</th>
                    <th>Laba</th>
                    <th>Supplier</th>
                </tr>
            </thead>
            <tbody>
                @forelse($details->produkKeluarDetails as $d)
                    <tr>
                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($d->produkKeluar->tanggal_keluar)->format('d M Y') }}
                        </td>
                        <td>{{ $d->produk->nama_produk }}</td>
                        <td class="text-end">{{ number_format($d->jumlah, 2) }}</td>
                        <td class="text-end">Rp {{ number_format($d->harga_satuan, 0) }}</td>
                        <td class="text-end">Rp {{ number_format($d->subtotal, 0) }}</td>
                        <td class="text-end">Rp {{ number_format($d->hpp, 0) }}</td>
                        <td class="text-end">Rp {{ number_format($d->laba, 0) }}</td>
                        <td>{{ $d->produkKeluar->supplier->nama_supplier ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Tidak ada data produk keluar.</td>
                    </tr>
                @endforelse
            </tbody>
            @if($details->produkKeluarDetails->count() > 0)
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="2" class="text-end">Total</td>
                        <td class="text-end">
                            {{ number_format($details->produkKeluarDetails->sum('jumlah'), 2) }}
                        </td>
                        <td></td>
                        <td class="text-end">
                            Rp {{ number_format($details->produkKeluarDetails->sum('subtotal'), 0) }}
                        </td>
                        <td class="text-end">
                            Rp {{ number_format($details->produkKeluarDetails->sum('hpp'), 0) }}
                        </td>
                        <td class="text-end">
                            Rp {{ number_format($details->produkKeluarDetails->sum('laba'), 0) }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            @endif
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

        <canvas id="produkKeluarChart"
            data-labels='@json($labels)'
            data-values='@json($values)'>
        </canvas>
    </div>

    @endif


    
</div>




