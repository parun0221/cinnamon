@extends('layouts.main')

@section('User', 'active')

@section('content')
<div class="dashboard-wrapper">
  <div class="dashboard-container">

    {{-- Sidebar --}}
    <div class="sidebar-container">
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <aside class="dashboard-sidebar" id="sidebar">
            <div class="sidebar-logo">🍃 <strong>Cinnamon</strong></div>
        <ul>
            <li><a  href="/produk"><i class="fas fa-list"></i> List Produk</a></li>
            <li><a href="/produk/create"><i class="fas fa-plus"></i> Tambah Produk</a></li>
            <li><a href="{{ route('produk.updateList') }}" class="active"><i class="fas fa-edit"></i> Update Produk</a></li>
            <li><a href="/kategori"><i class="fas fa-tags"></i> Kategori Produk</a></li>
        </ul>
        </aside>
    </div>

    {{-- Main --}}
    <div class="dashboard-main">
      <div class="dashboard-topbar">
        <div class="topbar-left">
          <h2 class="dashboard-title">Update Produk</h2>
          <p class="dashboard-subtitle">Edit langsung di tempat — simpan per produk.</p>
        </div>
        <div class="topbar-right">
          <h5 id="greeting-text">Selamat malam 👋</h5>
          <p id="datetime-text">{{ now()->translatedFormat('l, j F Y, H:i:s') }}</p>
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
      {{-- filter / search --}}
      <div class="produk-header">
        <form action="{{ route('produk.updateList') }}" method="GET" class="produk-filter-form">
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
            <a href="{{ route('produk.updateList') }}" class="btn-reset" style="margin-left:8px">Reset</a>
        </form>

      </div>

      {{-- grid produk (editable inline) --}}
      <div class="produk-grid">
        @forelse($produk as $p)
          <form action="{{ route('produk.quickUpdate', $p->id) }}?{{ http_build_query(request()->query()) }}" method="POST" enctype="multipart/form-data" class="produk-card">
              @csrf
              @method('PUT')

                <div class="produk-img">
                    <img id="preview-{{ $p->id }}"
                        src="{{ $p->gambar ? asset('storage/'.$p->gambar) : asset('images/no-image.png') }}"
                        alt="{{ $p->nama_produk }}">

                    <label class="btn-ganti-gambar">
                        <i class="fas fa-camera"></i>
                        <input type="file"
                            name="gambar"
                            class="file-input"
                            accept="image/*"
                            onchange="previewImage(event, {{ $p->id }})">
                    </label>

                    <span class="produk-badge">
                        {{ $p->kategori->nama_kategori ?? '-' }}
                    </span>
                </div>

              <div class="produk-info">
                  <input type="text" name="nama_produk" value="{{ old('nama_produk', $p->nama_produk) }}" class="input-edit" required>

                  <select name="kategori_id" class="input-edit" required>
                      @foreach($kategori as $k)
                          <option value="{{ $k->id }}" {{ $p->kategori_id == $k->id ? 'selected' : '' }}>
                              {{ $k->nama_kategori }}
                          </option>
                      @endforeach
                  </select>

                  <input type="text" name="satuan" value="{{ old('satuan', $p->satuan) }}" class="input-edit" required readonly>

                  <label class="small">Stok Siap (Kg)</label>
                  <input type="number" step="0.1" name="stok_siap" value="{{ old('stok_siap', $p->stok_siap) }}" class="input-edit" readonly>

                  <label class="small">Stok Pending (Kg)</label>
                  <input type="number" step="0.1" name="stok_pending" value="{{ old('stok_pending', $p->stok_pending) }}" class="input-edit" readonly>

                  <label class="small">Harga Jual (Rp)</label>
                  <input type="number" name="harga_jual" value="{{ old('harga_jual', $p->harga_jual) }}" class="input-edit" placeholder="Harga Jual">
                    <label class="small">Harga Beli (Rp)</label>
                  <input type="number" name="harga_beli" value="{{ old('harga_beli', $p->harga_beli) }}" class="input-edit" placeholder="Harga Beli">
              </div>

              <div class="produk-actions">
                  <button type="submit" class="btn-save">
                      <i class="fas fa-save"></i> Simpan
                  </button>
              </div>
          </form>
        @empty
          <div class="no-data">
              <p>😔 Tidak ada produk untuk diupdate.</p>
          </div>
        @endforelse
      </div>

    </div>
  </div>
</div>
@endsection
