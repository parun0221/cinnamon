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
            <li><a class="active" href="/produk"><i class="fas fa-list"></i> List Produk</a></li>
            @if(auth()->user()->role === 'admin')
            <li><a href="/produk/create"><i class="fas fa-plus"></i> Tambah Produk</a></li>
            <li><a href="{{ route('produk.updateList') }}"><i class="fas fa-edit"></i> Update Produk</a></li>
            <li><a href="/kategori"><i class="fas fa-tags"></i> Kategori Produk</a></li>
            @endif
        </ul>
        </aside>


    </div>

    {{-- Konten utama --}}
    <div class="dashboard-main">

      {{-- Bar Atas: Judul & Waktu --}}
      <div class="dashboard-topbar">
        <div class="topbar-left">
          <h2 class="dashboard-title">Produk List</h2>
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

      <div class="produk-header">
            <form action="{{ route('produk.index') }}" method="GET" class="produk-filter-form">
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
                 <a href="{{ route('produk.index') }}" class="btn-reset" style="margin-left:8px">Reset</a>
            </form>
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('produk.create') }}" class="btn-add">+ Tambah Produk</a>
            @endif
        </div>

        <div class="produk-grid">
            @forelse($produk as $p)
                <div class="produk-card">
                    <div class="produk-img">
                        <img src="{{ asset('storage/'.$p->gambar) }}" alt="{{ $p->nama_produk }}">
                        <span class="produk-badge">{{ $p->kategori->nama_kategori }}</span>
                    </div>
                    <div class="produk-info">
                        <h4>{{ $p->nama_produk }}</h4>
                        <p class="produk-satuan">{{ $p->satuan }}</p>

                        <div class="stok-info">
                            <p>Stok Siap: {{ number_format($p->stok_siap, 1) }} Kg</p>
                            <div class="progress">
                                <div class="progress-bar" style="width: {{ min(100, $p->stok_siap) }}%"></div>
                            </div>
                        </div>

                        <div class="stok-info">
                            <p>Stok Pending: {{ number_format($p->stok_pending, 1) }} Kg</p>
                            <div class="progress pending">
                                <div class="progress-bar" style="width: {{ min(100, $p->stok_pending) }}%"></div>
                            </div>
                        </div>

                        <p class="harga-jual">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</p>
                        <p class="harga-beli">Beli: Rp {{ number_format($p->harga_beli, 0, ',', '.') }}</p>
                    </div>
                    @if(auth()->user()->role === 'admin')
                    <div class="produk-actions">
                        <form action="{{ route('produk.destroy', $p->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="action-btn delete" onclick="return confirm('Hapus produk ini?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            @empty
                <div class="no-data">
                    <p>😔 Produk tidak ditemukan.</p>
                </div>
            @endforelse
        </div>




    </div> {{-- End dashboard-main --}}

  </div> {{-- End dashboard-container --}}
</div> {{-- End dashboard-wrapper --}}

@endsection
