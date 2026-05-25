@extends('layouts.main')

@section('User', 'active')

{{-- @section('sidebar')
  @include('Client_views.sidebar')
@endsection --}}

@section('content')

@if(auth()->user()->role === 'admin')
{{-- Notifications --}}
@if(session('pending_produk'))
<div class="modal fade" id="pendingProdukModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    ⚠️ Produk Belum Disetujui
                </h5>
            </div>

            <div class="modal-body">
                <p>
                    Terdapat
                    <strong>{{ session('pending_produk') }}</strong>
                    produk yang belum di-approve.
                </p>
                <p>Silakan periksa stok untuk melanjutkan proses.</p>
            </div>

            <div class="modal-footer">
                <a href="/stok" class="btn btn-success">
                    Periksa Stok
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Nanti
                </button>
            </div>

        </div>
    </div>
</div>
@endif
@endif

@if(session('pending_produk'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    new bootstrap.Modal(
        document.getElementById('pendingProdukModal')
    ).show();
});
</script>
@endif


{{-- Notification End --}}

<div class="dashboard-wrapper">
  <div class="dashboard-container">

    {{-- <div class="sidebar-container">
      <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
      </button>

      <aside class="dashboard-sidebar" id="sidebar">
        <div class="sidebar-logo">🍃 <strong>Cinnamon</strong></div>
        <ul>
          <li><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
          <li><a href="#"><i class="fas fa-box"></i> Produk</a></li>
          <li><a href="#"><i class="fas fa-arrow-down"></i> Barang Masuk</a></li>
          <li><a href="#"><i class="fas fa-arrow-up"></i> Barang Keluar</a></li>
          <li><a href="#"><i class="fas fa-chart-line"></i> Laporan</a></li>
        </ul>
      </aside>

    </div> --}}

    {{-- Konten utama --}}
    <div class="dashboard-main">
      {{-- Bar Atas: Judul & Waktu --}}
      <div class="dashboard-topbar">
        <div class="topbar-left">
          <h2 class="dashboard-title">Dashboard Inventaris</h2>
          <p class="dashboard-subtitle">Selamat datang di sistem informasi rempah Cinnamon.</p>
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
      {{-- Ringkasan Kartu --}}
      <div class="dashboard-cards">
        <a href="/buku" class="dashboard-card">
          <h4>Keuangan</h4>
          <p>Rp. {{ number_format($saldoKas,0,',','.') }}</p>
        </a>

        <a href="{{ route('input.history', [
              'start_date' => now()->toDateString(),
              'end_date'   => now()->toDateString(),
              'kategori'   => null
          ]) }}" class="dashboard-card">
          <h4>Produk Masuk</h4>
          <p>{{ $produkMasukHariIni }}</p>

          @if($pendingProdukCount > 0)
            <span class="card-badge danger">
              {{ $pendingProdukCount }} Pending
            </span>
          @endif
        </a>

        @if(auth()->user()->role === 'admin')
        <a href="{{ route('output.history', [
              'start_date' => now()->toDateString(),
              'end_date'   => now()->toDateString(),
              'kategori'   => null
          ]) }}" class="dashboard-card">
          <h4>Produk Keluar</h4>
          <p>{{ $produkKeluarHariIni }}</p>
        </a>
        @endif
        <a href="/stok" class="dashboard-card warning">
          <h4>Stok Rendah</h4>
          <p>{{ $produkStokRendah->count() }} Produk</p>
        </a>
      </div>


      {{-- Tombol Aksi --}}
      <div class="dashboard-actions">
        <a href="/input" class="action-button masuk">
          <i class="fas fa-arrow-down"></i>
          <span>Catat Barang Masuk</span>
        </a>
        @if(auth()->user()->role === 'admin')
        <a href="/output" class="action-button keluar">
          <i class="fas fa-arrow-up"></i>
          <span>Catat Barang Keluar</span>
        </a>
        @endif
      </div>

      {{-- Grafik --}}
      <div class="dashboard-graph">
        <h4>Grafik Stok</h4>
        <form method="GET" action="{{ route('dashboard') }}" class="row g-2 align-items-end">
 
            {{-- Produk --}}
            <div class="col-md-4">
                <label class="form-label small">Produk</label>
                <select name="produk_id"
                        class="form-select form-select-sm"
                        onchange="this.form.submit()">
                    @foreach ($produkList as $produk)
                        <option value="{{ $produk->id }}"
                            {{ $produkId == $produk->id ? 'selected' : '' }}>
                            {{ $produk->nama_produk }}
                            ({{ $produk->kategori->nama_kategori ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Start Date --}}
            <div class="col-md-3">
                <label class="form-label small">Start Date</label>
                <input type="date"
                      name="start_date"
                      class="form-control form-control-sm"
                      value="{{ $startDate }}"
                      onchange="this.form.submit()">
            </div>

            {{-- End Date --}}
            <div class="col-md-3">
                <label class="form-label small">End Date</label>
                <input type="date"
                      name="end_date"
                      class="form-control form-control-sm"
                      value="{{ $endDate }}"
                      onchange="this.form.submit()">
            </div>

          </form>

        <canvas id="stokChart" height="100"></canvas>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
          const ctx = document.getElementById('stokChart');

          new Chart(ctx, {
              data: {
                  labels: @json($labels),
                  datasets: [
                      {
                          type: 'bar',
                          label: 'Barang Masuk',
                          data: @json($masuk),
                          backgroundColor: '#74b9ff'
                      },
                      {
                          type: 'bar',
                          label: 'Barang Keluar',
                          data: @json($keluar),
                          backgroundColor: '#ff7675'
                      },
                      {
                          type: 'bar',
                          label: 'Penyesuaian',
                          data: @json($penyesuaian),
                          backgroundColor: '#fdcb6e'
                      },
                      {
                          type: 'line',
                          label: 'Stok Tersisa',
                          data: @json($stok),
                          borderColor: '#e17055',
                          backgroundColor: 'transparent',
                          tension: 0.3,
                          yAxisID: 'y1'
                      }
                  ]
              },
              options: {
                  responsive: true,
                  interaction: {
                      mode: 'index',
                      intersect: false
                  },
                  plugins: {
                      tooltip: {
                          callbacks: {
                              title: function(context) {
                                  const date = context[0].label;
                                  const d = new Date(date);

                                  return d.toLocaleDateString('id-ID', {
                                      day: '2-digit',
                                      month: '2-digit',
                                      year: 'numeric'
                                  });
                              }
                          }
                      }
                  },
                  scales: {
                      x: {
                          ticks: {
                              callback: function(value) {
                                  const date = this.getLabelForValue(value);
                                  return date.substring(8, 10);
                              }
                          }
                      },
                      y: {
                          title: {
                              display: true,
                              text: 'Jumlah'
                          }
                      },
                      y1: {
                          position: 'right',
                          grid: { drawOnChartArea: false },
                          title: {
                              display: true,
                              text: 'Stok'
                          }
                      }
                  }
              }
          });
        </script>





      </div>

      {{-- Tabel --}}
      <div class="dashboard-table">
        <h4>Produk dengan Stok Rendah</h4>
        <table class="cinnamon-table">
          <thead>
            <tr>
              <th>Nama Produk</th>
              <th>Stok</th>
              <th>Stok Minimum</th>
              <th>Kategori</th>
              <th>Terakhir Masuk</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($produkStokRendah as $produk)
            <tr>
              <td>{{ $produk->nama_produk }}</td>
              <td class="low">{{ $produk->stok_siap }}</td>
              <td>{{ $produk->stok_minimum }}</td>
              <td>{{ $produk->kategori->nama_kategori }}</td>
              <td>{{ $produk->created_at->format('Y-m-d') }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

    </div> {{-- End dashboard-main --}}

  </div> {{-- End dashboard-container --}}
</div> {{-- End dashboard-wrapper --}}

@endsection

