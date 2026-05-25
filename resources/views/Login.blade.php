<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sistem Informasi Rempah</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    :root {
      --rempah-coklat: #8B5E3C;
      --hijau-daun: #4CAF50;
      --kunyit: #F4A300;
      --krem: #F8F1EC;
      --putih: #FFFFFF;
      --teks: #2E2E2E;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: var(--krem);
      margin: 0;
    }

    /* Header Judul */
    .page-header {
      text-align: center;
      padding: 40px 20px 20px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
      margin-bottom: 30px;
    }

    .page-header h1 {
      color: var(--rempah-coklat);
      font-weight: 700;
      font-size: 2.2rem;
      margin-bottom: 10px;
    }

    .page-header p {
      color: #5e4b40;
      font-size: 1rem;
      max-width: 600px;
      margin: 0 auto;
      line-height: 1.5;
    }

    /* Kontainer Login */
    .login-container {
      max-width: 900px;
      background: var(--putih);
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    /* Bagian Kiri */
    .login-left {
      background: url('/images/loginback2.png') center center / cover no-repeat;
      min-height: 400px;
      position: relative;
    }

    .login-left::before {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(139, 94, 60, 0.5);
    }

    /* Bagian Kanan */
    .login-right {
      padding: 50px 40px;
      background-color: var(--putih);
      position: relative;
      z-index: 1;
    }

    .text-login {
      font-weight: 700;
      color: var(--rempah-coklat);
    }

    .button-login {
      background-color: var(--hijau-daun);
      color: var(--putih);
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      font-size: 1rem;
      width: 100%;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .button-login:hover {
      background-color: var(--kunyit);
      color: var(--putih);
    }

    .form-label {
      font-weight: 600;
      color: var(--rempah-coklat);
    }

    .form-control {
      border-radius: 8px;
      border: 1px solid #ddd;
      padding: 10px 15px;
      font-size: 0.95rem;
      transition: border-color 0.3s;
      box-sizing: border-box; /* memastikan ukuran tetap */
    }

    .form-control:focus {
      border-color: var(--hijau-daun);
      box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
      /* Tidak mengubah padding atau border-width */
    }

.password-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.toggle-password {
  position: absolute;
  right: 15px;
  top: 0;
  bottom: 0;
  height: 100%;
  display: flex;
  align-items: center;
  background: none;
  border: none;
  cursor: pointer;
  color: #888;
  font-size: 1.1rem;
  z-index: 2;
}

    @media (max-width: 768px) {
      .login-container {
        flex-direction: column;
      }

      .login-left, .login-right {
        padding: 30px 20px;
        text-align: center;
      }

      .login-left {
        min-height: 250px;
      }
    }
  </style>
</head>
<body>
  <!-- Judul Sistem di atas -->
  <header class="page-header">
    <h1>Sistem Informasi Rempah</h1>
    <p>Kelola Stok, Transaksi, & Pelacakan Real-Time Produk Rempah Anda dengan Mudah dan Cepat</p>
  </header>

  <!-- Kotak Login -->
  <main class="d-flex justify-content-center align-items-center">
    <div class="container">
      <div class="row login-container mx-auto">
        <!-- Gambar Kiri -->
        <div class="col-md-6 login-left"></div>

        <!-- Form Kanan -->
        <div class="col-md-6 login-right">
          <h3 class="text-login text-center mb-3">Selamat Datang</h3>
          <p class="text-center mb-4" style="font-size: 0.9rem;">Silakan login untuk melanjutkan ke dashboard inventaris Anda.</p>

                      @foreach (['success', 'warning', 'error'] as $type)
                          @if (session($type))
                              <div class="alert alert-{{ $type }} alert-dismissible fade show" role="alert">
                                  {{ session($type) }}
                                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                              </div>
                          @endif
                      @endforeach
          <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="email" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <div class="password-wrapper">
                <input type="password" class="form-control" name="password" id="password-field" required>
                <button type="button" class="toggle-password" tabindex="-1" onclick="togglePassword()">
                  <span id="toggle-icon">&#128065;</span>
                </button>
              </div>
            </div>
            <button type="submit" class="button-login">Masuk</button>
          </form>
        </div>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function togglePassword() {
      const pwd = document.getElementById('password-field');
      const icon = document.getElementById('toggle-icon');
      if (pwd.type === 'password') {
        pwd.type = 'text';
        icon.textContent = 'O_O';
      } else {
        pwd.type = 'password';
        icon.textContent = '-_-';
      }
    }
    // Set icon default
    document.addEventListener('DOMContentLoaded', function() {
      document.getElementById('toggle-icon').textContent = '-_-';
    });
  </script>
</body>
</html>
