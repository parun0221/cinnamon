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
            <li><a href="/stok" class="active"><i class="fas fa-plus"></i> Kelola Stok</a></li>
            <li><a href="/stok/history"><i class="fas fa-history"></i> Mutasi Stok</a></li>
            <li><a href="/buku"><i class="fas fa-receipt"></i> Keuangan</a></li>
            <li><a href="/stok/laporan"><i class="fas fa-file-alt"></i> Laporan Stok</a></li>
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
            <form action="{{ route('stok.index') }}" method="GET" class="produk-filter-form">
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
                <a href="{{ route('stok.index') }}" class="btn-reset" style="margin-left:8px">Reset</a>
            </form>

            

        </div>

        <div class="stok-list" id="produk-list">
            @forelse($produk as $p)
                <div class="stok-card">
                    <div class="stok-info-left">
                        <h4 class="stok-nama">{{ $p->nama_produk }}</h4>
                        <span class="stok-kategori">{{ $p->kategori->nama_kategori }}</span>
                        <p class="stok-satuan">Satuan: {{ $p->satuan }}</p>
                    </div>

                    <div class="stok-info-middle">
                        <div class="stok-row">
                            <span>Stok Siap:</span>
                            <strong>{{ number_format($p->stok_siap, 1) }} Kg</strong>
                            <div class="progress">
                                <div class="progress-bar" style="width: {{ $p->stok_minimum > 0 ? min(100, ($p->stok_siap / $p->stok_minimum) * 100) : 100 }}%"></div>
                            </div>
                        </div>

                        <div class="stok-row">
                            <span>Stok Pending:</span>
                            <strong>{{ number_format($p->stok_pending, 1) }} Kg</strong>
                            <div class="progress pending">
                                <div class="progress-bar" style="width: {{ min(100, $p->stok_pending) }}%"></div>
                            </div>
                        </div>

                        <div class="stok-row">
                            <span>Stok Minimum:</span>
                            <strong>{{ number_format($p->stok_minimum, 1) }} Kg</strong>
                        </div>
                    </div>

                    <div class="stok-info-right">
                        <p class="harga-jual">Jual: Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</p>
                        <p class="harga-beli">Beli: Rp {{ number_format($p->harga_beli, 0, ',', '.') }}</p>

                    

                            @if(auth()->user()->role === 'admin')
                            {{-- <button 
                                type="button"
                                class="btn-koreksi js-koreksi"
                                data-produk-id="{{ $p->id }}"
                                data-produk-nama="{{ $p->nama_produk }}"
                                data-stok="{{ $p->stok_siap }}"
                            >
                                Koreksi
                            </button> --}}
                            

                            <button 
                                type="button"
                                class="btn-periksa js-periksa"
                                data-produk-id="{{ $p->id }}"
                            >
                                Periksa
                                <span class="badge">
                                    {{ $p->produkMasukDetails->where('is_approved', false)->count() }}
                                </span>
                            </button>
                            @endif

                            <button 
                                type="button"
                                class="btn-koreksi js-stok"
                                data-produk-id="{{ $p->id }}"
                            >
                                Stok
                                {{-- <span class="badge">
                                    {{ $p->produkMasukDetails->where('is_approved', false)->count() }}
                                </span> --}}
                            </button>
                     

                    </div>
                </div>
            @empty
                <div class="no-data">
                    <p>😔 Tidak ada data stok ditemukan.</p>
                </div>
            @endforelse
        </div>

        <div id="stok-detail" class="hidden">
            <button id="btn-back-stok" class="btn-add" style="margin-bottom:12px;">
                ← Kembali
            </button>

            <div id="stok-detail-content">
                <!-- partial akan dimuat di sini -->
            </div>
        </div>

    </div> {{-- End dashboard-main --}}

  </div> {{-- End dashboard-container --}}
</div> {{-- End dashboard-wrapper --}}

{{-- MODAL KOREKSI --}}
<div class="modal fade" id="modalKoreksiStok" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <form id="form-koreksi-stok" class="modal-content shadow border-0" method="POST" action="{{ url('/stok-koreksi') }}">
      @csrf

      <div class="modal-header">
        <h5 class="modal-title fw-bold">
          Koreksi Stok Produk
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" name="produk_id" id="produk_id">

        <div class="mb-2">
          <label class="form-label">Produk</label>
          <input type="text" id="nama_produk" class="form-control" readonly>
        </div>

        <div class="mb-2">
          <label class="form-label">Stok Saat Ini</label>
          <input type="number" id="stok_lama" class="form-control" readonly>
        </div>

        <div class="mb-2">
          <label class="form-label">Stok Baru (Hasil Fisik)</label>
          <input
            type="number"
            step="0.01"
            name="stok_baru"
            id="stok_baru"
            class="form-control"
            required
          >
        </div>

        <div class="mb-2">
          <label class="form-label">Selisih</label>
          <input type="number" id="selisih" class="form-control" readonly>
        </div>

        <div class="mb-2">
          <label class="form-label">Keterangan</label>
          <textarea
            name="keterangan"
            class="form-control"
            rows="2"
            placeholder="Contoh: Koreksi stok fisik gudang"
          ></textarea>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-primary w-100" type="submit">
          Simpan Koreksi
        </button>
      </div>
    </form>
  </div>
</div>

<script>
document.querySelectorAll('.js-koreksi').forEach(btn => {
    btn.addEventListener('click', function () {

        const stokLama = parseFloat(this.dataset.stok);

        document.getElementById('produk_id').value = this.dataset.produkId;
        document.getElementById('nama_produk').value = this.dataset.produkNama;
        document.getElementById('stok_lama').value = stokLama;
        document.getElementById('stok_baru').value = '';
        document.getElementById('selisih').value = '';

        new bootstrap.Modal(
            document.getElementById('modalKoreksiStok')
        ).show();
    });
});

document.getElementById('stok_baru').addEventListener('input', function () {
    const lama = parseFloat(document.getElementById('stok_lama').value || 0);
    const baru = parseFloat(this.value || 0);
    document.getElementById('selisih').value = (baru - lama).toFixed(2);
});
</script>


<script>
document.addEventListener('change', function (e) {
    if (e.target.name === 'kondisi_barang') {
        const modal = e.target.closest('.modal-content');
        const beratWrapper = modal.querySelector('.berat-final-wrapper');

        if (e.target.value === 'belum_sesuai') {
            beratWrapper.classList.remove('hidden');
        } else {
            beratWrapper.classList.add('hidden');
        }
    }
});
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const stokList = document.getElementById('produk-list');
    const stokDetail = document.getElementById('stok-detail');
    const stokDetailContent = document.getElementById('stok-detail-content');
    const btnBack = document.getElementById('btn-back-stok');

    stokList.addEventListener('click', function (e) {
        const btn = e.target.closest('.js-periksa');
        if (!btn) return;

        const produkId = btn.dataset.produkId;

        // hide semua stok card
        stokList.querySelectorAll('.stok-card').forEach(card => {
            card.classList.add('hidden');
        });

        // load partial
        fetch(`/stok/${produkId}/periksa`)
            .then(res => {
                if (!res.ok) throw new Error('Gagal load data');
                return res.text();
            })
            .then(html => {
                stokDetailContent.innerHTML = html;
                stokDetail.classList.remove('hidden');
            })
            .catch(err => {
                console.error(err);
                stokDetailContent.innerHTML = '<p style="color:red;">Gagal memuat data.</p>';
            });
    });

    btnBack.addEventListener('click', function () {
        // tampilkan kembali list
        stokList.querySelectorAll('.stok-card').forEach(card => {
            card.classList.remove('hidden');
        });

        stokDetail.classList.add('hidden');
        stokDetailContent.innerHTML = '';
    });
});
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const stokList = document.getElementById('produk-list');
    const stokDetail = document.getElementById('stok-detail');
    const stokDetailContent = document.getElementById('stok-detail-content');
    const btnBack = document.getElementById('btn-back-stok');

    stokList.addEventListener('click', function (e) {
        const btn = e.target.closest('.js-stok');
        if (!btn) return;

        const produkId = btn.dataset.produkId;

        // hide semua stok card
        stokList.querySelectorAll('.stok-card').forEach(card => {
            card.classList.add('hidden');
        });

        // load partial
        fetch(`/stok/${produkId}/history`)
            .then(res => {
                if (!res.ok) throw new Error('Gagal load data');
                return res.text();
            })
            .then(html => {
                stokDetailContent.innerHTML = html;
                stokDetail.classList.remove('hidden');
            })
            .catch(err => {
                console.error(err);
                stokDetailContent.innerHTML = '<p style="color:red;">Gagal memuat data.</p>';
            });
    });

    btnBack.addEventListener('click', function () {
        // tampilkan kembali list
        stokList.querySelectorAll('.stok-card').forEach(card => {
            card.classList.remove('hidden');
        });

        stokDetail.classList.add('hidden');
        stokDetailContent.innerHTML = '';
    });
});
</script>

@endsection
