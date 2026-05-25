@extends('layouts.main')

@section('User', 'active')

{{-- @section('sidebar')
  @include('Client_views.sidebar')
@endsection --}}

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
            <li><a href="/produk"><i class="fas fa-list"></i> List Produk</a></li>
            <li><a href="/produk/create"><i class="fas fa-plus"></i> Tambah Produk</a></li>
            <li><a href="{{ route('produk.updateList') }}"><i class="fas fa-edit"></i> Update Produk</a></li>
            <li><a href="/kategori" class="active"><i class="fas fa-tags" ></i> Kategori Produk</a></li>
        </ul>
      </aside>

    </div>

    {{-- Konten utama --}}
    <div class="dashboard-main">

      {{-- Bar Atas: Judul & Waktu --}}
      <div class="dashboard-topbar">
        <div class="topbar-left">
          <h2 class="dashboard-title">Kategori Produk</h2>
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

      {{-- Konten Utama --}}
        <div class="dashboard-content akun-page">

            <h3 class="page-title">Manajemen Kategori Produk</h3>

                <div class="table-responsive">    
                <button class="btn btn-primary fw-semibold"
                        data-bs-toggle="modal"
                        data-bs-target="#tambahKategoriModal">
                    <i class="fas fa-user-plus me-1"></i> Tambah Kategori
                </button>
    
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kategori</th>
                                <th class="text-center">Jumlah Produk</th>
                                <th class="text-center">Aksi</th>

                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kategori as $row)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $row->nama_kategori }}</td>
                                    <td class="text-center">{{ $row->produk_count }}</td>
                                    <td class="text-center">

                                        <button class="btn btn-primary fw-semibold"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editKategoriModal{{ $row->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <div class="modal fade modal-akun"
                                            id="editKategoriModal{{ $row->id }}"
                                            tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered modal-sm modal-akun-dialog">
                                            <form action="{{ route('kategori.update', $row->id) }}"
                                                method="POST"
                                                class="modal-content modal-akun-content">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-header modal-akun-header">
                                                <h5 class="modal-title fw-bold">
                                                <i class="fas fa-edit me-2"></i>Edit Kategori
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body modal-akun-body">
                                                <div class="modal-akun-group">
                                                <label class="modal-akun-label">Nama</label>
                                                <input type="text"
                                                        name="name"
                                                        value="{{ $row->name }}"
                                                        required
                                                        placeholder="Nama kategori">
                                                </div>
                                            </div>

                                            <div class="modal-footer modal-akun-footer">
                                                <button type="submit" class="btn btn-primary w-100 fw-semibold">
                                                <i class="fas fa-save me-1"></i> Update Kategori
                                                </button>
                                            </div>
                                            </form>
                                        </div>
                                        </div>


                                        @if ($row->produk_count > 0)
                                            <button class="btn btn-danger btn-sm" disabled
                                                title="Kategori masih memiliki produk">
                                                Delete Kategori
                                            </button>
                                        @else
                                            <a href="javascript:void(0)"
                                            class="btn btn-danger btn-sm"
                                            onclick="deleteUser({{ $row->id }})">
                                                Delete Kategori
                                            </a>
                                        @endif

                                        <script>
                                            function deleteUser(id) {
                                                if (confirm('Apakah Anda yakin untuk menghapus kategori ini?')) {
                                                    fetch(`/kategori/${id}`, {
                                                        method: 'DELETE', // Gunakan metode DELETE sesuai dengan resource route
                                                        headers: {
                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}', // Pastikan CSRF token tersedia di halaman
                                                            'Content-Type': 'application/json'
                                                        }
                                                    })
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        if (data.success) {
                                                            alert('Kategori berhasil dihapus.');
                                                            location.reload(); // Reload halaman setelah berhasil
                                                        } else {
                                                            alert('Terjadi kesalahan saat menghapus kategori.');
                                                        }
                                                    })
                                                    .catch(error => {
                                                        console.error('Error:', error);
                                                        alert('Gagal menghubungi server.');
                                                    });
                                                }
                                            }
                                        </script>

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

        </div>

    </div> {{-- End dashboard-main --}}

  </div> {{-- End dashboard-container --}}
</div> {{-- End dashboard-wrapper --}}

<!-- Modal Tambah Kategori -->
<div class="modal fade modal-akun" id="tambahKategoriModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm modal-akun-dialog">
    <form action="{{ route('kategori.store') }}"
          method="POST"
          class="modal-content modal-akun-content">
      @csrf

      <div class="modal-header modal-akun-header">
        <h5 class="modal-title fw-bold">
          <i class="fas fa-user-plus me-2"></i>Tambah Kategori
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body modal-akun-body">
        <div class="modal-akun-group">
          <label class="modal-akun-label">Nama Kategori</label>
          <input type="text" name="name" required placeholder="Nama kategori">
        </div>
      <div class="modal-footer modal-akun-footer">
        <button type="submit" class="btn btn-primary w-100 fw-semibold">
          <i class="fas fa-save me-1"></i> Simpan Kategori
        </button>
      </div>
    </form>
  </div>
</div>




@endsection
