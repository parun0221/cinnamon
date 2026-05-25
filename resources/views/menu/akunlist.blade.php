@extends('layouts.main')

@section('User', 'active')

{{-- @section('sidebar')
  @include('Client_views.sidebar')
@endsection --}}

@section('content')
<div class="dashboard-wrapper">
  <div class="dashboard-container">

    {{-- <div class="sidebar-container">
      <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
      </button>

      <aside class="dashboard-sidebar" id="sidebar">
        <div class="sidebar-logo">🍃 <strong>Cinnamon</strong></div>
        <ul>
          <li><a href="/akun"><i class="fas fa-home"></i> Akun</a></li>
          <li><a href="/akunlist"><i class="fas fa-box"></i> Manage User</a></li>

        </ul>
      </aside>

    </div> --}}

    {{-- Konten utama --}}
    <div class="dashboard-main">

      {{-- Bar Atas: Judul & Waktu --}}
      <div class="dashboard-topbar">
        <div class="topbar-left">
          <h2 class="dashboard-title">Manage User</h2>
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

            <h3 class="page-title">Informasi Akun</h3>

                <div class="table-responsive">    
                    <button class="btn btn-primary fw-semibold"
        data-bs-toggle="modal"
        data-bs-target="#tambahUserModal">
    <i class="fas fa-user-plus me-1"></i> Tambah User
</button>
    
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pekerja</th>
                                <th>Phone Number</th>
                                <th>Email</th>
                                <th class="text-center">Aksi</th>

                            </tr>
                        </thead>
                        <tbody>
                            @forelse($akun as $row)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $row->name }}</td>
                                    <td>{{ ($row->phone) }}</td>
                                    <td>{{ $row->email }}</td>
                                    <td class="text-center">
                                        <a href="javascript:void(0)" class="btn btn-danger btn-sm" onclick="deleteUser({{ $row->id }})">
                                            Delete User
                                        </a>

                                        <script>
                                            function deleteUser(id) {
                                                if (confirm('Apakah Anda yakin untuk menghapus user ini?')) {
                                                    fetch(`/akun/${id}`, {
                                                        method: 'DELETE', // Gunakan metode DELETE sesuai dengan resource route
                                                        headers: {
                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}', // Pastikan CSRF token tersedia di halaman
                                                            'Content-Type': 'application/json'
                                                        }
                                                    })
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        if (data.success) {
                                                            alert('User berhasil dihapus.');
                                                            location.reload(); // Reload halaman setelah berhasil
                                                        } else {
                                                            alert('Terjadi kesalahan saat menghapus user.');
                                                        }
                                                    })
                                                    .catch(error => {
                                                        console.error('Error:', error);
                                                        alert('Gagal menghubungi server.');
                                                    });
                                                }
                                            }
                                        </script>

                                        <a href="javascript:void(0)" class="btn btn-danger btn-sm" onclick="resetpw({{ $row->id }})">
                                            Reset Password
                                        </a>

                                        <script>
                                        function resetpw(id) {
                                            if (!confirm('Reset Password User ini ke default (12345678)?')) return;

                                            fetch(`/akun-reset/${id}`, {
                                                method: 'POST',
                                                headers: {
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                    'Content-Type': 'application/json'
                                                }
                                            })
                                            .then(res => res.json())
                                            .then(data => {
                                                if (data.success) {
                                                    alert('Password berhasil direset ke 12345678.');
                                                    location.reload();
                                                } else {
                                                    alert(data.message || 'Terjadi kesalahan.');
                                                }
                                            })
                                            .catch(err => {
                                                console.error(err);
                                                alert('Gagal menghubungi server.');
                                            });
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

<!-- Modal Tambah User -->
<div class="modal fade modal-akun" id="tambahUserModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm modal-akun-dialog">
    <form action="{{ route('akun.store') }}"
          method="POST"
          class="modal-content modal-akun-content">
      @csrf

      <div class="modal-header modal-akun-header">
        <h5 class="modal-title fw-bold">
          <i class="fas fa-user-plus me-2"></i>Tambah User
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body modal-akun-body">
        <div class="modal-akun-group">
          <label class="modal-akun-label">Nama</label>
          <input type="text" name="name" required placeholder="Nama lengkap">
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

      <div class="modal-footer modal-akun-footer">
        <button type="submit" class="btn btn-primary w-100 fw-semibold">
          <i class="fas fa-save me-1"></i> Simpan User
        </button>
      </div>
    </form>
  </div>
</div>

@endsection
