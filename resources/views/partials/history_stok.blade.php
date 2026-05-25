<div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            @if(auth()->user()->role === 'admin')
            <div class="d-flex justify-content-end mb-3 gap-2">
                <a href="{{ route('stok.history.excel', [
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
                    <th>Id</th>
                    <th>Jenis</th>
                    <th>Jumlah (Kg)</th>
                    <th>Stok</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $stok = $stokAwal;
                @endphp
                @forelse($details->riwayatStoks as $d)
                    @php
                        if ($d->jenis === 'masuk') {
                            $stok += $d->jumlah;
                        } elseif ($d->jenis === 'keluar') {
                            $stok -= $d->jumlah;
                        } elseif ($d->jenis === 'penyesuaian') {
                            $stok += $d->jumlah;
                        }
                    @endphp
                    <tr>
                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($d->tanggal)->format('d M Y') }}
                        </td>
                        <td class="text-center">{{ $d->id }}</td>
                        <td class="text-capitalize">{{ $d->jenis }}</td>
                        <td class="text-end">{{ number_format($d->jumlah, 2) }}</td>
                        
                        <td class="text-end">{{ number_format($stok, 2) }}</td>
                        <td>{{ $d->keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Tidak ada data riwayat stok.</td>
                    </tr>
                @endforelse
            </tbody>
            @if($details->riwayatStoks->count() > 0)
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="2" class="text-end">Total Masuk</td>
                        <td class="text-end">
                            {{ number_format(
                                $details->riwayatStoks
                                    ->where('jenis', 'masuk')
                                    ->sum('jumlah'),
                                2
                            ) }}
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-end">Total Keluar</td>
                        <td class="text-end">
                            {{ number_format(
                                $details->riwayatStoks
                                    ->where('jenis', 'keluar')
                                    ->sum('jumlah'),
                                2
                            ) }}
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-end">Total Penyesuaian</td>
                        <td class="text-end">
                            {{ number_format(
                                $details->riwayatStoks
                                    ->where('jenis', 'penyesuaian')
                                    ->sum('jumlah'),
                                2
                            ) }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>

            @endif
        </table>
    </div>

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
        <h4>Grafik Mutasi Stok</h4>

        <canvas id="stokChart"
            data-labels='@json($labels)'
            data-values='@json($values)'
            data-masuk='@json($masuk)'
            data-keluar='@json($keluar)'
            data-penyesuaian='@json($penyesuaian)'>
        </canvas>

    </div>
</div>


