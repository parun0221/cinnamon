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
            <li><a href="/suppto" class="active"><i class="fas fa-users"></i> Data Pelanggan / Supplier</a></li>
            <li><a href="/output/laporan"><i class="fas fa-file-alt"></i> Laporan Penjualan</a></li>
            </ul>

        </aside>


    </div>

    {{-- Konten utama --}}
    <div class="dashboard-main">

      {{-- Bar Atas: Judul & Waktu --}}
      <div class="dashboard-topbar">
        <div class="topbar-left">
          <h2 class="dashboard-title">Data Pelanggan / Supplier</h2>
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
            <form action="{{ route('suppto') }}" method="GET" class="produk-filter-form">

                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama..." class="search-bar">

                <button type="submit" class="btn-add">Tampilkan</button>
                <a href="{{ route('suppto') }}" class="btn-reset" style="margin-left:8px">Reset</a>
            </form>
        </div>





        <div class="table-responsive">        
                <button class="btn btn-primary fw-semibold"
                        data-bs-toggle="modal"
                        data-bs-target="#tambahUserModal">
                    <i class="fas fa-user-plus me-1"></i> Tambah Data Klien
                </button>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Supplier</th>
                        <th>Telphone</th>
                        <th>Email</th>
                        <th>Alamat</th>
                        <th class="text-center">Total Penjualan</th>
                        <th class="text-center">Cek</th>

                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $row->nama_supplier }}</td>
                            <td>{{ $row->kontak }}</td>
                            <td>{{ $row->email }}</td>
                            <td>{{ $row->alamat }}</td>
                            <td class="text-center">{{ number_format($row->produk_keluar_count, 0) }}</td>
                            <td class="text-center">
                                <button class="btn btn-primary fw-semibold"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editSupplierModal{{ $row->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <div class="modal fade modal-akun"
                                    id="editSupplierModal{{ $row->id }}"
                                    tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered modal-sm modal-akun-dialog">
                                    <form action="{{ route('supplier.update', $row->id) }}"
                                        method="POST"
                                        class="modal-content modal-akun-content">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-header modal-akun-header">
                                        <h5 class="modal-title fw-bold">
                                        <i class="fas fa-edit me-2"></i>Edit Client
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body modal-akun-body">
                                        <div class="modal-akun-group">
                                        <label class="modal-akun-label">Nama</label>
                                        <input type="text"
                                                name="nama_supplier"
                                                required
                                                value="{{ $row->nama_supplier }}"
                                                placeholder="Nama lengkap">
                                        <input type="hidden" name="tipe" value="{{ $row->tipe }}">
                                        </div>

                                        <div class="modal-akun-group">
                                        <label class="modal-akun-label">Email</label>
                                        <input type="email"
                                                name="email"
                                                value="{{ $row->email }}"
                                                placeholder="email@domain.com">
                                        </div>

                                        <div class="modal-akun-group">
                                        <label class="modal-akun-label">Telepon</label>
                                        <input type="text"
                                                name="phone"
                                                value="{{ $row->kontak }}"
                                                placeholder="08xxxxxxxxxx">
                                        </div>

                                        <div class="modal-akun-group">
                                        <label class="modal-akun-label">Alamat</label>
                                        <input type="text"
                                                name="alamat"
                                                value="{{ $row->alamat }}"
                                                placeholder="Alamat">
                                        </div>
                                    </div>

                                    <div class="modal-footer modal-akun-footer">
                                        <button type="submit" class="btn btn-primary w-100 fw-semibold">
                                        <i class="fas fa-save me-1"></i> Update Klien
                                        </button>
                                    </div>

                                    </form>
                                </div>
                                </div>

                                <a href="/suppto/{{ $row->id }}"
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

<div class="modal fade modal-akun" id="tambahUserModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm modal-akun-dialog">
    <form action="{{ route('supplier.store') }}"
          method="POST"
          class="modal-content modal-akun-content">
      @csrf

      <div class="modal-header modal-akun-header">
        <h5 class="modal-title fw-bold">
          <i class="fas fa-user-plus me-2"></i>Tambah Client
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body modal-akun-body">
        <div class="modal-akun-group">
          <label class="modal-akun-label">Nama</label>
          <input type="text" name="nama_supplier" required placeholder="Nama lengkap">
          <input type="text" name="tipe" required hidden value="to">
        </div>

        <div class="modal-akun-group">
          <label class="modal-akun-label">Email</label>
          <input type="email" name="email" required placeholder="email@domain.com">
        </div>

        <div class="modal-akun-group">
          <label class="modal-akun-label">Telepon</label>
          <input type="text" name="phone" placeholder="08xxxxxxxxxx">
        </div>
      </div>
      <div class="modal-akun-group">
          <label class="modal-akun-label">Alamat</label>
          <input type="text" name="alamat" placeholder="Alamat">
        </div>
      <div class="modal-footer modal-akun-footer">
        <button type="submit" class="btn btn-primary w-100 fw-semibold">
          <i class="fas fa-save me-1"></i> Simpan Klien
        </button>
      </div>



    </form>
  </div>
</div>


@endsection


