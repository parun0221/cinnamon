<header class="navbar">
        <button class="navbar-toggle" id="navbarToggle">
    <i class="fas fa-bars"></i>
    </button>
<div class="navbar-brand navbar-brand-stack">
    <span class="brand-title">G WES 🍃</span>
    <span class="saldo-navbar">
        Rp {{ number_format($saldoKas, 0, ',', '.') }}
    </span>
</div>



  <ul class="navbar-nav">


    <li class="nav-item {{ request()->is('produk*') ? 'active' : '' }}">
      <a href="/produk">
        <i class="fas fa-box"></i>
        <span>Produk</span> 
      </a>
    </li>
    <li class="nav-item {{ request()->is('input*', 'suppfrom*') ? 'active' : '' }}">
        <a href="/input">
            <i class="fas fa-arrow-down"></i>
            <span>Masuk</span>
        </a>
    </li>
    <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
      <a href="/dashboard">
        <i class="fas fa-home"></i>
        <span>Dashboard</span>
      </a>
    </li>
    @if(auth()->user()->role === 'admin')
    <li class="nav-item {{ request()->is('output*', 'suppto*') ? 'active' : '' }}">
      <a href="/output">
        <i class="fas fa-arrow-right"></i>
        <span>Keluar</span>
      </a>
    </li>
    @endif
    <li class="nav-item {{ request()->is('stok*', 'buku') ? 'active' : '' }}">
      <a href="/stok" class="menu-stok">
          <i class="fas fa-boxes-stacked"></i>
          <span>Stok</span>
          @if(auth()->user()->role === 'admin')
            @if($pendingProdukCount > 0)
                <span class="badge badge-danger">
                    {{ $pendingProdukCount }}
                </span>
            @endif
          @endif
      </a>
    </li>
@if(auth()->user()->role === 'admin')
    <li class="nav-item {{ request()->is('akunlist') ? 'active' : '' }}">
      <a href="/akunlist">
        <i class="fa-solid fa-users-gear"></i>
        <span>Pekerja</span>
      </a>
    </li>
@endif
  </ul>

  <div class="navbar-user dropdown">
      <a
          href="#"
          class="d-flex align-items-center text-decoration-none"
          id="userDropdown"
          data-bs-toggle="dropdown"
          aria-expanded="false"
      >
          <i class="fas fa-user-circle fa-lg"></i>
      </a>

      <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
          <li>
              <a class="dropdown-item" href="/akun">
                  <i class="fas fa-gear me-2"></i> Pengaturan
              </a>
          </li>

          <li><hr class="dropdown-divider"></li>

          <li>
              <form method="POST" action="/logout">
                  @csrf
                  <button type="submit" class="dropdown-item">
                      <i class="fas fa-right-from-bracket me-2"></i> Logout
                  </button>
              </form>
          </li>
      </ul>
  </div>


  <script>
  document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.getElementById("navbarToggle");
    const nav = document.querySelector(".navbar-nav");

    toggle.addEventListener("click", () => {
      nav.classList.toggle("show");
    });
  });
</script>

</header>
