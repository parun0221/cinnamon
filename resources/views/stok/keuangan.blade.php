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
            <li><a href="/stok"><i class="fas fa-plus"></i> Kelola Stok</a></li>
            <li><a href="/stok/history"><i class="fas fa-history"></i> Mutasi Stok</a></li>
            <li><a href="/buku" class="active"><i class="fas fa-receipt"></i> Keuangan</a></li>
            <li><a href="/stok/laporan"><i class="fas fa-file-alt"></i> Laporan Stok</a></li>
            </ul>

        </aside>


    </div>

    {{-- Konten utama --}}
    <div class="dashboard-main">

      {{-- Bar Atas: Judul & Waktu --}}
      <div class="dashboard-topbar">
        <div class="topbar-left">
          <h2 class="dashboard-title">Laporan Buku Besar</h2>
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
    <form action="{{ route('buku.index') }}" method="GET" class="produk-filter-form">

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

        <button type="submit" class="btn-add">Tampilkan</button>
        <a href="{{ route('buku.index') }}" class="btn-reset" style="margin-left:8px">Reset</a>
    </form>

    @if(auth()->user()->role === 'admin')
    <div class="mb-3">
      <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#tambahUangModal">
          Tambah Uang
      </button>

      <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#kurangiUangModal">
          Kurangi Uang
      </button>
    </div>
    @endif

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
            <span class="label">Kategori</span>
            <span class="value">
                {{ $kategoriNama ?? 'Semua Kategori' }}
            </span>
        </div>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Debit (Rp)</th>
                <th>Kredit (Rp)</th>
                <th>Saldo (Rp)</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bukuBesar as $item)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                    <td>{{ number_format($item->debit, 0, ',', '.') }}</td>
                    <td>{{ number_format($item->kredit, 0, ',', '.') }}</td>
                    <td>{{ number_format($item->saldo, 0, ',', '.') }}</td>
                    <td>{{ $item->keterangan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">😔 Tidak ada data ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>




    </div> {{-- End dashboard-main --}}

  </div> {{-- End dashboard-container --}}
</div> {{-- End dashboard-wrapper --}}


<!-- Modal Tambah Uang -->
<div class="modal fade modal-buku" id="tambahUangModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm modal-buku-dialog">
    <form action="{{ route('buku_besar.tambah') }}"
          method="POST"
          class="modal-content modal-buku-content">
      @csrf

      <div class="modal-header modal-buku-header">
        <h5 class="modal-title text-success fw-bold">
          <i class="fas fa-plus-circle me-2"></i>Tambah Uang
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body modal-buku-body">
        <div class="modal-buku-group">
          <label class="modal-buku-label">Tanggal</label>
          <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}">
        </div>

        <div class="modal-buku-group">
          <label class="modal-buku-label">Jumlah (Rp)</label>
          <input type="number" name="jumlah" step="0.01" min="0" required>
        </div>

        <div class="modal-buku-group">
          <label class="modal-buku-label">Keterangan</label>
          <textarea name="keterangan" rows="2" placeholder="Opsional..."></textarea>
        </div>
      </div>

      <div class="modal-footer modal-buku-footer">
        <button type="submit" class="btn btn-success w-100 fw-bold">
          <i class="fas fa-check-circle me-1"></i> Simpan Debit
        </button>
      </div>
    </form>
  </div>
</div>



<!-- Modal Kurangi Uang -->
<div class="modal fade modal-buku" id="kurangiUangModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm modal-buku-dialog">
    <form action="{{ route('buku_besar.kurangi') }}"
          method="POST"
          class="modal-content modal-buku-content">
      @csrf

      <div class="modal-header modal-buku-header">
        <h5 class="modal-title text-danger fw-bold">
          <i class="fas fa-minus-circle me-2"></i>Kurangi Uang
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body modal-buku-body">
        <div class="modal-buku-group">
          <label class="modal-buku-label">Tanggal</label>
          <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}">
        </div>

        <div class="modal-buku-group">
          <label class="modal-buku-label">Jumlah (Rp)</label>
          <input type="number" name="jumlah" step="0.01" min="0" required>
        </div>

        <div class="modal-buku-group">
          <label class="modal-buku-label">Keterangan</label>
          <textarea name="keterangan" rows="2" placeholder="Opsional..."></textarea>
        </div>
      </div>

      <div class="modal-footer modal-buku-footer">
        <button type="submit" class="btn btn-danger w-100 fw-bold">
          <i class="fas fa-check-circle me-1"></i> Simpan Kredit
        </button>
      </div>
    </form>
  </div>
</div>


@endsection


