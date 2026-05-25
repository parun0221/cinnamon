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
            <li><a href="/output" class="active"><i class="fas fa-plus"></i> Catat Penjualan Baru</a></li>
            <li><a href="/output/history"><i class="fas fa-history"></i> Riwayat Penjualan</a></li>
            <li><a href="/suppto"><i class="fas fa-users"></i> Data Pelanggan / Supplier</a></li>
            <li><a href="/output/laporan"><i class="fas fa-file-alt"></i> Laporan Penjualan</a></li>
            </ul>


        </aside>


    </div>

    {{-- Konten utama --}}
    <div class="dashboard-main">

      {{-- Bar Atas: Judul & Waktu --}}
      <div class="dashboard-topbar">
        <div class="topbar-left">
          <h2 class="dashboard-title">Pencatatan Produk Keluar</h2>
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

        <div class="form-container">
            <form action="/output" method="POST" id="form-produk-masuk">
            @csrf

            <div class="form-group mb-4">
            <label for="tanggal_masuk">Tanggal Masuk</label>
            <input
                type="date"
                name="tanggal_masuk"
                id="tanggal_masuk"
                class="form-control"
                required
                value="{{ old('tanggal_masuk', date('Y-m-d')) }}"
            >
            </div>

            <div class="form-group mb-5">
            <label for="supplier_id">Supplier</label>
            <select
                name="supplier_id"
                id="supplier_id"
                class="form-control"
                required
                style="width: 100%"
            >
                {{-- select2 ajax nanti --}}
            </select>
            </div>

            <h4 style="color: #326b57; font-weight: 700; margin-bottom: 18px;">
            Detail Produk Keluar
            </h4>

            <table class="table" id="produk-masuk-table">
            <thead>
                <tr>
                <th style="width: 20%;">Produk</th>
                <th style="width: 15%;">Stok (kg)</th>
                <th style="width: 16%;">Berat (kg)</th>
                <th style="width: 14%;">Harga Satuan (Rp)</th>
                <th style="width: 19%;">Subtotal (Rp)</th>
                {{-- <th style="width: 12%;">Status</th> --}}
                <th style="width: 4%;">
                    <button type="button" id="add-row" title="Tambah Baris">
                    <i class="fas fa-plus"></i>
                    </button>
                </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                <td>
                    <select
                    name="produk[0][id]"
                    class="form-control produk-select"
                    required
                    style="width: 100%"
                    >
                    {{-- select2 ajax --}}
                    </select>
                </td>
                <td>
                    <input
                    type="number"
                    step="0.01"
                    name="produk[0][stok]"
                    class="form-control stok"
                    required
                    min="0"
                    placeholder="0"
                    readonly
                    >
                </td>
                <td>
                    <input
                    type="number"
                    step="0.01"
                    name="produk[0][berat]"
                    class="form-control berat-awal"
                    required
                    min="0"
                    placeholder="0"
                    >
                </td>
                <td>
                    <input
                    type="number"
                    step="0.01"
                    name="produk[0][harga_satuan]"
                    class="form-control harga-satuan"
                    required
                    min="0"
                    placeholder="0"
                    >
                </td>
                <td>
                    <input
                    type="number"
                    step="0.01"
                    name="produk[0][subtotal]"
                    class="form-control subtotal"
                    readonly
                    placeholder="0"
                    >
                </td>
                {{-- <td>
                    <select
                    name="produk[0][status]"
                    class="form-control"
                    required
                    >
                    <option value="pending" selected>Pending</option>
                    <option value="selesai">Selesai</option>
                    </select>
                </td> --}}
                <td>
                    <button type="button" class="remove-row" title="Hapus Baris">
                    <i class="fas fa-trash"></i>
                    </button>
                </td>
                </tr>
            </tbody>
            </table>

                <button type="button" id="btn-review" class="btn btn-success">
                    Review & Simpan
                </button>
                {{-- <button type="submit">Simpan Produk Masuk</button> --}}
            </form>
        </div>






    </div> {{-- End dashboard-main --}}

  </div> {{-- End dashboard-container --}}
</div> {{-- End dashboard-wrapper --}}

<div class="modal fade" id="notaModal" tabindex="-1">
    <link rel="stylesheet" href="{{ asset('css/nota.css') }}">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable nota-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Faktur Penjualan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="notaContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary"></div>
                </div>
            </div>

            <div class="modal-footer">
                <button id="btnPrintPdf" class="btn btn-success">
                    <i class="fas fa-print"></i> Print PDF
                </button>
                <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Verifikasi Produk Keluar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div id="review-content">
                    <!-- diisi via JS -->
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Periksa Ulang
                </button>
                <button type="button" id="btn-submit-final" class="btn btn-success">
                    Setujui & Simpan
                </button>
            </div>

        </div>
    </div>
</div>

 
<script>
    
document.addEventListener('DOMContentLoaded', function () {

    const btnReview = document.getElementById('btn-review');
    const form = document.getElementById('form-produk-masuk');

    btnReview.addEventListener('click', function () {

        const tanggal = document.getElementById('tanggal_masuk').value;
        const supplier = document.querySelector('#supplier_id option:checked')?.textContent ?? '-';
            const form = document.getElementById('form-produk-masuk');

    if (!form.checkValidity()) {
        form.reportValidity(); // munculkan pesan required
        return;
    }

        let rowsHtml = '';
        let grandTotal = 0;

        document.querySelectorAll('#produk-masuk-table tbody tr').forEach(row => {
const selectEl = row.querySelector('.produk-select');
let produk = '-';

if (selectEl) {
    const data = $(selectEl).select2('data');
    if (data && data.length) {
        const item = data[0];
        produk = item.text;
        if (item.nama_kategori) {
            produk += ` (${item.nama_kategori})`;
        }
    }
}

const berat    = row.querySelector('input[name*="[berat]"]')?.value ?? 0;
const harga    = row.querySelector('input[name*="[harga_satuan]"]')?.value ?? 0;
const subtotal = row.querySelector('input[name*="[subtotal]"]')?.value ?? 0;


            grandTotal += parseFloat(subtotal || 0);

            rowsHtml += `
                <tr>
                    <td>${produk}</td>
                    <td class="text-end">${berat}</td>
                    <td class="text-end">${Number(harga).toLocaleString()}</td>
                    <td class="text-end">${Number(subtotal).toLocaleString()}</td>
                </tr>
            `;
        });

        document.getElementById('review-content').innerHTML = `
            <p><strong>Tanggal Keluar:</strong> ${tanggal}</p>
            <p><strong>Supplier:</strong> ${supplier}</p>

            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Berat</th>
                        <th>Harga</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>${rowsHtml}</tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total</th>
                        <th class="text-end">${grandTotal.toLocaleString()}</th>
                    </tr>
                </tfoot>
            </table>
        `;

        new bootstrap.Modal(document.getElementById('reviewModal')).show();
    });

    document.getElementById('btn-submit-final-modal')
        .addEventListener('click', function () {
            form.submit();
        });
});
</script>


<script>
    
    document.getElementById('btn-submit-final').addEventListener('click', function () {
    document.getElementById('form-produk-masuk').submit();
    });
</script>


@if(session('show_nota'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    const notaId = "{{ session('nota_id') }}";
    currentNotaId = notaId; // 🔥 WAJIB

    const modalEl = document.getElementById('notaModal');
    if (!modalEl) {
        console.error('notaModal tidak ditemukan');
        return;
    }

    const modal = new bootstrap.Modal(modalEl);

    document.getElementById('notaContent').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary"></div>
        </div>
    `;

    fetch(`/faktur/penjualan/${notaId}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById('notaContent').innerHTML = html;
        })
        .catch(err => {
            document.getElementById('notaContent').innerHTML =
                '<p class="text-danger text-center">Gagal memuat nota</p>';
        });

    modal.show();
});
</script>
@endif
<script>
let currentNotaId = null;

document.addEventListener('click', function (e) {
    if (e.target.id === 'btnPrintPdf') {
        if (!currentNotaId) return;
        window.open(`/faktur/penjualan/${currentNotaId}/pdf`, '_blank');
    }
});
</script>



@endsection
@push('scripts')
<!-- Muat jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Muat Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Muat Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Format tampilan item di dropdown (digunakan untuk produk)
    function formatProdukResult(item) {
        if (!item.id) {
            return item.text || 'Pilih produk...';
        }
        if (item.nama_kategori) {
            return $('<span>' + item.text + ' <small style="color:#888;">(' + item.nama_kategori + ')</small></span>');
        }
        return $('<span>' + item.text + '</span>');
    }

    function formatProdukSelection(item) {
        if (item.nama_kategori) {
            return item.text + ' (' + item.nama_kategori + ')';
        }
        return item.text || 'Pilih produk';
    }

    // Inisialisasi Select2 untuk Supplier
    $('#supplier_id').select2({
        placeholder: 'Pilih supplier...',
        allowClear: true,
        minimumInputLength: 0,
        ajax: {
            url: '{{ route("supplier0.search") }}',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return { q: params.term || '' };
            },
            processResults: function(data, params) {
                let results = data.results || [];
                if (params.term && results.length === 0) {
                    results.push({
                        id: params.term,
                        text: 'Tambah supplier baru: "' + params.term + '"',
                        isNew: true
                    });
                }
                return { results: results };
            },
            cache: true
        },
        templateResult: function(data) {
            if (!data.id) return data.text || 'Pilih supplier...';
            if (data.isNew) {
                return $('<span style="color: #0d6efd; font-style: italic;"><i class="fas fa-plus-circle"></i> ' + data.text + '</span>');
            }
            return $('<span>' + data.text + '</span>');
        },
        templateSelection: function(data) {
            if (data.isNew) {
                return data.text.replace(/Tambah supplier baru: "|"/g, '');
            }
            return data.text || 'Pilih supplier';
        },
        escapeMarkup: function(markup) {
            return markup;
        },
        width: '100%'
    });

    // Simpan supplier baru saat dipilih
    $('#supplier_id').on('select2:select', function(e) {
        let data = e.params.data;
        if (data.isNew) {
            let namaBaru = data.text.replace(/Tambah supplier baru: "|"/g, '');
            if (confirm('Tambahkan supplier baru: "' + namaBaru + '"?')) {
                $.ajax({
                    url: '{{ route("supplier.store1") }}',
                    type: 'POST',
                    data: {
                        nama_supplier: namaBaru,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        let newOption = new Option(response.text, response.id, false, true);
                        $('#supplier_id').append(newOption).trigger('change');
                    },
                    error: function(xhr) {
                        let message = xhr.responseJSON?.message || 'Gagal menyimpan.';
                        alert('Error: ' + message);
                        $('#supplier_id').val(null).trigger('change');
                    }
                });
            } else {
                $('#supplier_id').val(null).trigger('change');
            }
        }
    });

    // Inisialisasi Select2 untuk Produk
    function initSelect2Produk($select) {
        $select.select2({
            placeholder: 'Pilih produk...',
            allowClear: true,
            minimumInputLength: 0,
            ajax: {
                url: '{{ route("produk.search") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { q: params.term || '' };
                },
                processResults: function(data) {
                    return {
                        results: data.results.map(item => ({
                            id: item.id,
                            text: item.text,
                            nama_kategori: item.nama_kategori,
                            harga_jual: item.harga_jual,
                            stok_siap: item.stok_siap 
                        }))
                    };
                },
                cache: true
            },
            templateResult: formatProdukResult,
            templateSelection: formatProdukSelection,
            escapeMarkup: function(markup) {
                return markup;
            },
            width: '100%'
        });
    }

    // Inisialisasi Select2 di baris pertama
    initSelect2Produk($('.produk-select'));

    // Tambah baris baru
    $('#add-row').on('click', function() {
        let lastIndex = $('#produk-masuk-table tbody tr').length;
        let newRow = `
            <tr>
                <td>
                    <select name="produk[${lastIndex}][id]" class="form-control produk-select" required style="width: 100%"></select>
                </td>
                <td>
                    <input type="number" step="0.01" name="produk[${lastIndex}][stok]" class="form-control stok" required min="0" placeholder="0" readonly>
                <td>
                    <input type="number" step="0.01" name="produk[${lastIndex}][berat]" class="form-control berat" required min="0" placeholder="0">
                </td>
                <td>
                    <input type="number" step="0.01" name="produk[${lastIndex}][harga_satuan]" class="form-control harga-satuan" required min="0" placeholder="0">
                </td>
                <td>
                    <input type="number" step="0.01" name="produk[${lastIndex}][subtotal]" class="form-control subtotal" readonly placeholder="0">
                </td>

                <td>
                    <button type="button" class="remove-row" title="Hapus Baris">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#produk-masuk-table tbody').append(newRow);
        initSelect2Produk($('#produk-masuk-table tbody tr').last().find('.produk-select'));
    });

    // Hapus baris
    $('#produk-masuk-table').on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        reindexRows();
    });

    // Reindex ulang semua baris setelah penghapusan
    function reindexRows() {
        $('#produk-masuk-table tbody tr').each(function(index) {
            $(this).find('.produk-select').attr('name', `produk[${index}][id]`);
            $(this).find('.stok').attr('name', `produk[${index}][stok]`);
            $(this).find('.berat').attr('name', `produk[${index}][berat]`);
            $(this).find('.harga-satuan').attr('name', `produk[${index}][harga_satuan]`);
            $(this).find('.subtotal').attr('name', `produk[${index}][subtotal]`);
            $(this).find('select').not('.produk-select').attr('name', `produk[${index}][status]`);
        });
    }

    // Hitung subtotal otomatis
    function hitungSubtotal(row) {
        let berat = parseFloat(row.find('.berat, .berat-awal').val()) || 0;
        let harga = parseFloat(row.find('.harga-satuan').val()) || 0;
        row.find('.subtotal').val((berat * harga).toFixed(0));
    }

    // Hitung saat input berat/harga berubah
    $('#produk-masuk-table').on('input', '.berat, .berat-awal, .harga-satuan', function() {
        let row = $(this).closest('tr');
        hitungSubtotal(row);
    });

    // Isi harga satuan dari data produk & hitung subtotal
    $('#produk-masuk-table').on('select2:select', '.produk-select', function(e) {
        let data = e.params.data;
        let row = $(this).closest('tr');

        if (data.harga_jual) {
            row.find('.harga-satuan').val(parseFloat(data.harga_jual).toFixed(0));
        }
        if (data.stok_siap) {
            row.find('.stok').val(parseFloat(data.stok_siap).toFixed(0));
        } else {
            row.find('.stok').val('0');
        }
        hitungSubtotal(row);
    });
$(document).on('input', '.berat-awal', function() {
    let row = $(this).closest('tr');
    let stok = parseFloat(row.find('.stok').val()) || 0;
    let berat = parseFloat($(this).val()) || 0;

    if (berat > stok) {
        $(this).val(stok); // batasi berat
        berat = stok; // update variabel berat ke stok juga
    }

    // panggil hitungSubtotal agar subtotal ikut berubah
    hitungSubtotal(row);
});

    
});



</script>
@endpush