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
            <li><a href="/produk/create" class="active"><i class="fas fa-plus"></i> Tambah Produk</a></li>
            <li><a href="{{ route('produk.updateList') }}"><i class="fas fa-edit"></i> Update Produk</a></li>
            <li><a href="/kategori"><i class="fas fa-tags"></i> Kategori Produk</a></li>
        </ul>
      </aside>
    </div>

    {{-- Main --}}
    <div class="dashboard-main">
      <div class="dashboard-topbar">
        <div class="topbar-left">
          <h2 class="dashboard-title">Tambah Produk</h2>
          <p class="dashboard-subtitle">Buat beberapa produk sekaligus dengan cepat.</p>
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


      <form action="/produk" method="POST" enctype="multipart/form-data">
        @csrf
        
        @php
        $oldProduk = old('produk', [ [] ]); // kalau tidak ada, set array kosong sebagai index pertama
        @endphp

        <div id="produk-grid" class="produk-grid">
            @foreach ($oldProduk as $i => $produk)
                <div class="produk-card">
                    <div class="produk-img">
                        <img src="{{ asset('images/no-image.png') }}" alt="Preview">
                        <input type="file" name="produk[{{ $i }}][gambar]" class="file-input">
                    </div>

                    <div class="produk-info">
                        <input type="text" name="produk[{{ $i }}][nama_produk]" class="input-edit"
                            placeholder="Nama Produk" required
                            value="{{ old("produk.$i.nama_produk") }}">

                        <select name="produk[{{ $i }}][kategori_id]" class="input-edit" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($kategori as $k)
                                <option value="{{ $k->id }}" {{ old("produk.$i.kategori_id") == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kategori }}
                                </option>
                            @endforeach
                        </select>

                        <input type="text" name="produk[{{ $i }}][satuan]" class="input-edit"
                            placeholder="Satuan" required value="{{ old("produk.$i.satuan", 'kg') }}" readonly>
                        <input type="number" step="0.1" name="produk[{{ $i }}][stok_siap]" class="input-edit"
                            placeholder="Stok Siap (Kg)" value="{{ old("produk.$i.stok_siap") }}">
                        <input type="number" step="0.1" name="produk[{{ $i }}][stok_pending]" class="input-edit"
                            placeholder="Stok Pending (Kg)" value="{{ old("produk.$i.stok_pending") }}">
                        <input type="number" name="produk[{{ $i }}][harga_jual]" class="input-edit"
                            placeholder="Harga Jual" value="{{ old("produk.$i.harga_jual") }}">
                        <input type="number" name="produk[{{ $i }}][harga_beli]" class="input-edit"
                            placeholder="Harga Beli" value="{{ old("produk.$i.harga_beli") }}">
                    </div>
                </div>
            @endforeach

            {{-- Card tambah (+) --}}
            <div id="add-card" class="produk-card add-card">
                <div class="add-card-content">
                    <i class="fas fa-plus"></i>
                    <p>Tambah Produk</p>
                </div>
            </div>
        </div>


        <div class="mt-4">
          <button type="submit" class="btn-save">
            <i class="fas fa-save"></i> Simpan Semua
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  let index = {{ count($oldProduk) }};
  const grid = document.getElementById('produk-grid');
  const addCard = document.getElementById('add-card');

  addCard.addEventListener('click', () => {
    const newCard = document.createElement('div');
    newCard.classList.add('produk-card');

    newCard.innerHTML = `
      <button type="button" class="btn-remove-card" title="Hapus">
        <i class="fas fa-times"></i>
      </button>

      <div class="produk-img">
        <img src="{{ asset('images/no-image.png') }}" alt="Preview">
        <input type="file" name="produk[${index}][gambar]" class="file-input">
      </div>

      <div class="produk-info">
        <input type="text"
          name="produk[${index}][nama_produk]"
          class="input-edit"
          placeholder="Nama Produk"
          required>

        <select name="produk[${index}][kategori_id]" class="input-edit" required>
          <option value="">Pilih Kategori</option>
          @foreach($kategori as $k)
            <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
          @endforeach
        </select>

        <input type="text"
          name="produk[${index}][satuan]"
          class="input-edit"
          value="kg"
          readonly>

        <input type="number" step="0.1"
          name="produk[${index}][stok_siap]"
          class="input-edit"
          placeholder="Stok Siap (Kg)">

        <input type="number" step="0.1"
          name="produk[${index}][stok_pending]"
          class="input-edit"
          placeholder="Stok Pending (Kg)">

        <input type="number"
          name="produk[${index}][harga_jual]"
          class="input-edit"
          placeholder="Harga Jual">

        <input type="number"
          name="produk[${index}][harga_beli]"
          class="input-edit"
          placeholder="Harga Beli">
      </div>
    `;

    grid.insertBefore(newCard, addCard);
    index++;
  });

  // 🔥 Event delegation untuk hapus card
  grid.addEventListener('click', function (e) {
    if (e.target.closest('.btn-remove-card')) {
      e.target.closest('.produk-card').remove();
    }
  });
});
</script>


<style>
.add-card {
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px dashed #ccc;
  cursor: pointer;
  color: #888;
}
.add-card-content {
  text-align: center;
}
.add-card-content i {
  font-size: 2rem;
}
</style>
@endsection
