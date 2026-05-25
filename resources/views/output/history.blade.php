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
            <li><a href="/output/history" class="active"><i class="fas fa-history"></i> Riwayat Penjualan</a></li>
            <li><a href="/suppto"><i class="fas fa-users"></i> Data Pelanggan / Supplier</a></li>
            <li><a href="/output/laporan"><i class="fas fa-file-alt"></i> Laporan Penjualan</a></li>
            </ul>

        </aside>


    </div>

    {{-- Konten utama --}}
    <div class="dashboard-main">

      {{-- Bar Atas: Judul & Waktu --}}
      <div class="dashboard-topbar">
        <div class="topbar-left">
          <h2 class="dashboard-title">History Produk Keluar</h2>
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
            <form action="{{ route('output.history') }}" method="GET" class="produk-filter-form">
                {{-- <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..." class="search-bar"> --}}

                <div class="filter-group">
                    <label for="start_date">Dari Tanggal</label>
                    <input type="date" id="start_date" name="start_date"
                        value="{{ request('start_date', date('Y-m-01')) }}" class="search-bar">
                </div>

                <div class="filter-group">
                    <label for="end_date">Sampai Tanggal</label>
                    <input type="date" id="end_date" name="end_date"
                        value="{{ request('end_date', date('Y-m-d')) }}" class="search-bar">
                </div>


                <select name="kategori" class="search-bar">
                    <option value="">Semua Kategori</option>
                    @foreach($kategori as $k)
                        <option value="{{ $k->id }}" {{ request('kategori') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kategori }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn-add">Cari</button>
                <a href="{{ route('output.history') }}" class="btn-reset" style="margin-left:8px">Reset</a> 
            </form>
        </div>

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
                <span class="label">Kategori</span>
                <span class="value">
                    {{ $kategoriNama ?? 'Semua Kategori' }}
                </span>
            </div>
        </div>

        <div id="produk-list" class="produk-list">
            @forelse($produk as $produks)
                <div 
                    class="produk-card" 
                    tabindex="0" 
                    role="button" 
                    aria-pressed="false"
                    data-produk-id="{{ $produks->id }}"
                >
                    <div class="produk-card-content">
                        <h5 class="produk-nama">{{ $produks->nama_produk }}</h5>
                        <p class="produk-kategori">{{ $produks->kategori->nama_kategori ?? '-' }}</p>
                    </div>
                    <div class="produk-status-count">
                        <span class="status-badge selesai">Transaksi</span>
                        <span class="pending-count">
                            {{ $produks->produkKeluarDetails->count() }} item
                        </span>
                    </div>
                </div>
            @empty
                <p class="no-data">Tidak ada data history.</p>
            @endforelse
        </div>

        <div id="produk-detail" class="hidden">
          <button id="btn-back" class="btn-add" style="margin-bottom: 12px;">← Kembali</button>
          <div id="produk-detail-content"></div>
        </div>


    </div> {{-- End dashboard-main --}}

  </div> {{-- End dashboard-container --}}
</div> {{-- End dashboard-wrapper --}}



@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let produkChart = null; // 🔥 GLOBAL

function renderProdukKeluarChart() {
    const canvas = document.getElementById('produkKeluarChart');
    if (!canvas) return;

    const labels = JSON.parse(canvas.dataset.labels || '[]');
    const values = JSON.parse(canvas.dataset.values || '[]');

    if (produkChart) {
        produkChart.destroy();
        produkChart = null;
    }

    produkChart = new Chart(canvas, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Total Berat Keluar (Kg)',
                data: values,
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // sekarang AMAN
            animation: false, // 🔥 hilangkan lag
            scales: {
                x: {
                    ticks: {
                        callback: function(value, index) {
                            const label = this.getLabelForValue(value);
                            const d = new Date(label);
                            return d.getDate(); // 🔥 cuma tanggal (1–31)
                        }
                    }
                },
                y: { beginAtZero: true }
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const header = document.getElementById('produk-header');
    const produkList = document.getElementById('produk-list');
    const produkDetail = document.getElementById('produk-detail');
    const produkDetailContent = document.getElementById('produk-detail-content');
    const btnBack = document.getElementById('btn-back');

    produkList.addEventListener('click', function (e) {
        const card = e.target.closest('.produk-card');
        if (!card) return;

        const produkId = card.dataset.produkId;

        header.classList.add('hidden');
        produkList.querySelectorAll('.produk-card').forEach(c => {
            if (c !== card) c.classList.add('hidden');
        });

        const startDate = document.getElementById('start_date').value;
        const endDate   = document.getElementById('end_date').value;

        fetch(`/output/history/${produkId}?start_date=${startDate}&end_date=${endDate}`)
            .then(res => res.text())
            .then(html => {
                produkDetailContent.innerHTML = html;
                produkDetail.classList.remove('hidden');
                renderProdukKeluarChart(); // 🔥 SEKALI
            });
    });

    btnBack.addEventListener('click', function () {
        header.classList.remove('hidden');
        produkList.querySelectorAll('.produk-card').forEach(c => c.classList.remove('hidden'));
        produkDetail.classList.add('hidden');
        produkDetailContent.innerHTML = '';

        if (produkChart) {
            produkChart.destroy();
            produkChart = null;
        }
    });
});
</script>

@endpush