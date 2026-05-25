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
            <li>
                <a href="/input" class="{{ request()->is('input') ? 'active' : '' }}">
                    <i class="fas fa-plus"></i> Catat Produk Masuk
                </a>
            </li>

            <li>
                <a href="/input/pending" class="{{ request()->is('input/pending') ? 'active' : '' }}">
                    <i class="fas fa-hourglass-half"></i> Produk Belum Siap
                </a>
            </li>

            <li>
                <a href="/input/history" class="{{ request()->is('input/history') ? 'active' : '' }}">
                    <i class="fas fa-history"></i> History Produk Masuk
                </a>
            </li>

            <li>
                <a href="/suppfrom" class="{{ request()->is('suppfrom*') ? 'active' : '' }}">
                    <i class="fas fa-truck"></i> Pemasok
                </a>
            </li>

            <li>
                <a href="/input/laporan" class="{{ request()->is('input/laporan') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i> Laporan Produk Masuk
                </a>
            </li>
        </ul>
    </aside>



    </div>

    {{-- Konten utama --}}
    <div class="dashboard-main">

      {{-- Bar Atas: Judul & Waktu --}}
      <div class="dashboard-topbar">
        <div class="topbar-left">
          <h2 class="dashboard-title">History Pemasok Produk</h2>
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
            <form action="{{ route('suppfrom') }}" method="GET" class="produk-filter-form">

                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama..." class="search-bar">

                <button type="submit" class="btn-add">Tampilkan</button>
                <a href="{{ route('suppfrom') }}" class="btn-reset" style="margin-left:8px">Reset</a>
            </form>
        </div>





        <div class="table-responsive">        
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Supplier</th>
                        <th class="text-center">Total Penjualan</th>
                        <th class="text-center">Cek</th>

                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $row->nama_supplier }}</td>
                            <td class="text-center">{{ number_format($row->produk_masuk_count, 0) }}</td>
                            <td class="text-center">
                                <a href="/suppfrom/{{ $row->id }}"
                                class="btn btn-sm"
                                style="background-color:#91F1DE; color:#736B6B; border-radius:50%;"
                                title="Lihat Detail">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>

                            {{-- <td>{{ number_format($row->cek, 0) }}</td> --}}
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



@endsection


