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
            <li><a href="/suppto"><i class="fas fa-users"></i> Data Pelanggan / Supplier</a></li>
            <li><a href="/output/laporan" class="active"><i class="fas fa-file-alt"></i> Laporan Penjualan</a></li>
            </ul>

        </aside>


    </div>

    {{-- Konten utama --}}
    <div class="dashboard-main">

      {{-- Bar Atas: Judul & Waktu --}}
      <div class="dashboard-topbar">
        <div class="topbar-left">
          <h2 class="dashboard-title">Laporan Produk Keluar</h2>
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
    <form action="{{ route('output.laporan') }}" method="GET" class="produk-filter-form">

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

        {{-- <div class="filter-group">
            <label for="kategori">Kategori Produk</label>
            <select id="kategori" name="kategori" class="search-bar">
                <option value="">Semua Kategori</option>
                @foreach($kategori as $k)
                    <option value="{{ $k->id }}" {{ request('kategori') == $k->id ? 'selected' : '' }}>
                        {{ $k->nama_kategori }}
                    </option>
                @endforeach
            </select>
        </div> --}}

        <button type="submit" class="btn-add">Tampilkan</button>
        <a href="{{ route('output.laporan') }}" class="btn-reset" style="margin-left:8px">Reset</a>
    </form>
</div>

<div class="table-responsive">

                @php
                    $tglAwal  = request('start_date', date('Y-m-01'));
                    $tglAkhir = request('end_date', date('Y-m-d'));

                    $kategoriNama = null;
                    if (request('kategori')) {
                        $kategoriNama = $kategori->firstWhere('id', request('kategori'))?->nama_kategori;
                    }
                @endphp
                
    <div class="report-info">
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

    <table class="table table-striped">
        <div class="d-flex justify-content-end mb-3 gap-2">
            <a href="{{ route('output.laporan.excel', [
                'start_date' => request('start_date'),
                'end_date' => request('end_date')
            ]) }}"
            class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
        </div>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Produk</th>
                <th>Penjualan</th>
                <th>HPP</th>
                <th>Laba</th>

            </tr>
        </thead>
        <tbody>
            @forelse($laporan as $row)
                <tr>
                    <td>{{ $row->kategori }}</td>
                    <td>{{ $row->produk }}</td>
                    <td>Rp {{ number_format($row->penjualan, 0) }}</td>
                    <td>Rp {{ number_format($row->hpp, 0) }}</td>
                    <td>Rp {{ number_format($row->laba, 0) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>




    </div> {{-- End dashboard-main --}}

  </div> {{-- End dashboard-container --}}
</div> {{-- End dashboard-wrapper --}}



@endsection


