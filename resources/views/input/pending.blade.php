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
          <h2 class="dashboard-title">Produk Dalam Proses</h2>
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
                'error'   => 'danger', 
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
            {{-- <form action="{{ route('input.pending') }}" method="GET" class="produk-filter-form">
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
                <a href="{{ route('input.pending') }}" class="btn-reset" style="margin-left:8px">Reset</a>
            </form> --}}
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
                        <span class="status-badge pending">Pending</span>
                        <span class="pending-count">
                            {{ $produks->produkMasukDetails->where('status', 'pending')->count() }} item
                        </span>
                    </div>
                </div>
            @empty
                <p class="no-data">Tidak ada produk pending.</p>
            @endforelse
        </div>

        <div id="produk-detail" class="hidden">
            <button id="btn-back" class="btn-add" style="margin-bottom: 12px;">← Kembali</button>
            <div id="produk-detail-content">
                <!-- Data detail akan dimuat di sini -->
            </div>
        </div>




    </div> {{-- End dashboard-main --}}

  </div> {{-- End dashboard-container --}}
</div> {{-- End dashboard-wrapper --}}



@endsection
@push('scripts')
<script>
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

        // Hide header
        header.classList.add('hidden');

        // Hide semua card kecuali yang dipilih
        produkList.querySelectorAll('.produk-card').forEach(c => {
            if (c !== card) {
                c.classList.add('hidden');
            }
        });

        // Load detail
        fetch(`/input/pending/${produkId}`)
            .then(res => {
                if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                return res.text();
            })
            .then(html => {
                produkDetailContent.innerHTML = html;
                produkDetail.classList.remove('hidden');
            })
            .catch(err => {
                console.error(err);
                produkDetailContent.innerHTML = '<p style="color:red;">Gagal memuat data.</p>';
            });
    });

    btnBack.addEventListener('click', function () {
        // Tampilkan kembali semua card dan header
        header.classList.remove('hidden');
        produkList.querySelectorAll('.produk-card').forEach(c => c.classList.remove('hidden'));

        // Sembunyikan detail
        produkDetail.classList.add('hidden');
        produkDetailContent.innerHTML = '';
    });
});
</script>
@endpush
