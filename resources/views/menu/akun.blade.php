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
          @if(auth()->user()->role === 'admin')
          <li><a href="/akunlist"><i class="fas fa-box"></i> Manage User</a></li>
          @endif

        </ul>
      </aside>

    </div> --}}

    {{-- Konten utama --}}
    <div class="dashboard-main">

      {{-- Bar Atas: Judul & Waktu --}}
      <div class="dashboard-topbar">
        <div class="topbar-left">
          <h2 class="dashboard-title">Data Akun</h2>
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

          {{-- INFO AKUN --}}
          <div class="akun-card">
              <div class="akun-row">
                  <span class="label">Nama</span>
                  <span class="value">{{ $akun->name }}</span>
              </div>
              <div class="akun-row">
                  <span class="label">Email</span>
                  <span class="value">{{ $akun->email }}</span>
              </div>
              <div class="akun-row">
                  <span class="label">No. Telepon</span>
                  <span class="value">{{ $akun->phone ?? '-' }}</span>
              </div>
          </div>

          <hr class="divider">

          {{-- GANTI PASSWORD --}}
          <h4 class="section-title">Ganti Password</h4>

          @if(session('success'))
              <div class="alert success">{{ session('success') }}</div>
          @endif

          @if($errors->any())
              <div class="alert error">
                  <ul>
                      @foreach($errors->all() as $e)
                          <li>{{ $e }}</li>
                      @endforeach
                  </ul>
              </div>
          @endif

          <form action="{{ route('akun.password.update') }}" method="POST" class="password-form">
              @csrf

              <div class="password-grid">
                  <div class="password-box">
                      <label>Password Sekarang</label>
                      <input type="password" name="current_password" required>
                  </div>

                  <div class="password-box">
                      <label>Password Baru</label>
                      <input type="password" name="new_password" required>
                  </div>

                  <div class="password-box">
                      <label>Konfirmasi Password</label>
                      <input type="password" name="new_password_confirmation" required>
                  </div>
              </div>

              <button type="submit" class="btn-save">
                  Simpan Perubahan
              </button>
          </form>

      </div>

    </div> {{-- End dashboard-main --}}

  </div> {{-- End dashboard-container --}}
</div> {{-- End dashboard-wrapper --}}

@endsection
