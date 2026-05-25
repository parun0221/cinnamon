{{-- @extends('layouts.main')

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
                <li><a href="/input"><i class="fas fa-plus"></i> Catat Produk Masuk</a></li>
                <li><a href="/input/pending"><i class="fas fa-hourglass-half"></i> Produk Masuk Pending / Proses</a></li>
                <li><a href="/input/history"><i class="fas fa-history"></i> History Produk Masuk</a></li>
                <li><a href=""><i class="fas fa-truck"></i> Supplier</a></li>
                <li><a href="/input/laporan"><i class="fas fa-file-alt"></i> Laporan Produk Masuk</a></li>
            </ul>

        </aside>


    </div>

    {{-- Konten utama --}}
    <div class="dashboard-main">

      {{-- Bar Atas: Judul & Waktu --}}
      <div class="dashboard-topbar">
        <div class="topbar-left">
          <h2 class="dashboard-title">History Produk Masuk</h2>
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

        <div class="produk-header">
            <form action="{{ route('input.history') }}" method="GET" class="produk-filter-form">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..." class="search-bar">

                <select name="kategori" class="search-bar">
                    <option value="">Semua Kategori</option>
                    @foreach($kategori as $k)
                        <option value="{{ $k->id }}" {{ request('kategori') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kategori }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn-add">Cari</button>
                
            </form>
        </div>

        <div class="produk-list">
            @forelse($produk as $produks)
                <div class="produk-card" tabindex="0" role="button" aria-pressed="false">
                    <div class="produk-card-content">
                        <h5 class="produk-nama">{{ $produks->nama_produk }}</h5>
                        <p class="produk-kategori">{{ $produks->kategori->nama_kategori ?? '-' }}</p>
                    </div>
                    <div class="produk-status-count">
                        <span class="status-badge pending">Pending</span>
                        <span class="pending-count">
                            {{ $produks->produkMasukDetails->count() }} item
                        </span>
                    </div>
                </div>
            @empty
                <p class="no-data">Tidak ada produk pending.</p>
            @endforelse
        </div>




    </div> {{-- End dashboard-main --}}

  </div> {{-- End dashboard-container --}}
</div> {{-- End dashboard-wrapper --}}



@endsection
 --}}
