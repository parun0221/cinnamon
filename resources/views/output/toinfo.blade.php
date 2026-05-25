@extends('layouts.main')

@section('User', 'active')

@section('content')
<div class="dashboard-wrapper">
  <div class="dashboard-container">

    <div class="sidebar-container">
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>

        <aside class="dashboard-sidebar" id="sidebar">
        <div class="sidebar-logo">🍃 <strong>Cinnamon</strong></div>
            <ul>
            <li><a href="/output"><i class="fas fa-plus"></i> Catat Penjualan Baru</a></li>
            <li><a href="/output/history"><i class="fas fa-history"></i> Riwayat Penjualan</a></li>
            <li><a href="/suppto" class="active"><i class="fas fa-users"></i> Data Pelanggan / Supplier</a></li>
            <li><a href="/output/laporan"><i class="fas fa-file-alt"></i> Laporan Penjualan</a></li>
            </ul>

        </aside>


    </div>

    {{-- Konten utama --}}
    <div class="dashboard-main">

      {{-- Bar Atas: Judul & Waktu --}}
      <div class="dashboard-topbar">
        <div class="topbar-left">
          <h2 class="dashboard-title">Faktur Penjualan Produk</h2>
          <p class="dashboard-subtitle">Manage Produk, Make it Easy.</p>
        </div>
        <div class="topbar-right">
          <h5 id="greeting-text">Selamat malam 👋</h5>
          <p id="datetime-text">Selasa, 5 Agustus 2025, 02.29.07</p>
        </div>
      </div>

        @php
            $alerts = [
                'success' => 'success',
                'warning' => 'warning',
                'error'   => 'danger', // error diubah ke danger
            ];
        @endphp

        @foreach ($alerts as $sessionKey => $alertClass)
            @if (session($sessionKey))
                <div class="alert alert-{{ $alertClass }} alert-dismissible fade show" role="alert">
                    {{ session($sessionKey) }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        @endforeach

        {{-- Konten Utama --}}

        <div class="produk-header" id="produk-header">
            <form action="{{ route('suppto.show1', $supplier->id) }}" method="GET" class="produk-filter-form">

                <div class="filter-group">
                    <label for="start_date">Dari Tanggal</label>
                    <input type="date" id="start_date" name="start_date"
                        value="{{ request('start_date', date('Y-m-01')) }}" class="search-bar">
                </div>

                <div class="filter-group">
                    <label for="end_date">Sampai Tanggal</label>
                    <input type="date" id="end_date" name="end_date"
                        value="{{ request('end_date', date('Y-m-d')) }}" class="search-bar">
                </div>>

                <button type="submit" class="btn-add">Tampilkan</button>
                <a href="{{ route('suppto.show1', $supplier->id) }}" class="btn-reset" style="margin-left:8px">Reset</a>
            </form>
            
        </div>





        <div class="table-responsive">        

            <div class="report-info">

                @php
                    $tglAwal  = request('start_date', date('Y-m-01'));
                    $tglAkhir = request('end_date', date('Y-m-d'));

                    $kategoriNama = null;
                    if (request('kategori')) {
                        $kategoriNama = $kategori->firstWhere('id', request('kategori'))?->nama_kategori;
                    }
                @endphp

                <div class="report-item">
                    <span class="label">Periode</span>
                    <span class="value">
                        {{ \Carbon\Carbon::parse($tglAwal)->translatedFormat('d M Y') }}
                        —
                        {{ \Carbon\Carbon::parse($tglAkhir)->translatedFormat('d M Y') }}
                    </span>
                </div>

                <div class="report-item">
                    <span class="label">Pembelian Dari</span>
                    <span class="value">
                        {{ $supplier->nama_supplier ?? 'Semua Supplier' }}
                    </span>
                </div>
            </div>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Keluar</th>
                        <th>Produk</th>
                        <th class="text-center">Total Penjualan (Kg)</th>
                        <th class="text-center">Total Harga</th>
                        <th class="text-center">Aksi</th>

                    </tr>
                </thead>
                <tbody>
                    @forelse($details as $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $row->tanggal_keluar }}</td>
                            <td>
                                @php
                                    $produkList = $row->produkKeluarDetails->map(function ($detail) {
                                        $namaProduk = $detail->produk->nama_produk ?? '-';
                                        $kategori = $detail->produk->kategori->nama_kategori ?? '-';
                                        return "{$namaProduk} ({$kategori})";
                                    });
                                @endphp

                                {{ $produkList->implode(', ') }}
                            </td>
                            <td class="text-center">{{ number_format($row->total_berat, 0) }}</td>
                            <td class="text-center">{{ number_format($row->total_harga, 0) }}</td>
                            <td class="text-center">
                                <button
                                    class="btn btn-sm btn-outline-primary btn-nota"
                                    data-id="{{ $row->id }}"
                                    title="Lihat Faktur Penjualan">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>



    </div> {{-- End dashboard-main --}}

  </div> {{-- End dashboard-container --}}
</div> {{-- End dashboard-wrapper --}}

<div class="modal fade" id="notaModal" tabindex="-1">
    <link rel="stylesheet" href="{{ asset('css/nota.css') }}">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable nota-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Faktur Penjualan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="notaContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary"></div>
                </div>
            </div>

            <div class="modal-footer">
                <button id="btnPrintPdf" class="btn btn-success">
                    <i class="fas fa-print"></i> Print PDF
                </button>
                <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentNotaId = null;

document.querySelectorAll('.btn-nota').forEach(btn => {
    btn.addEventListener('click', function () {
        const id = this.dataset.id;
        currentNotaId = id;

        const modal = new bootstrap.Modal(
            document.getElementById('notaModal')
        );

        document.getElementById('notaContent').innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary"></div>
            </div>
        `;

        fetch(`/faktur/penjualan/${id}`)
            .then(res => res.text())
            .then(html => {
                document.getElementById('notaContent').innerHTML = html;
            });

        modal.show();
    });
});

document.getElementById('btnPrintPdf').addEventListener('click', function () {
    if (!currentNotaId) return;
    window.open(`/faktur/penjualan/${currentNotaId}/pdf`, '_blank');
});
</script>

@endsection

