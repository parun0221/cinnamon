<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanProdukKeluarExport implements FromCollection , WithHeadings, WithMapping
{
protected $rows;

    public function __construct(Collection $rows)
    {
        $this->rows = $rows;
    }

    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Kategori',
            'Produk',
            'JumlahKeluar (kg)',
            'Rata Harga Jual',
            'Total Penjualan',
            'Harga Pokok Terjual',
            'Total Modal',
            'Keuntungan',
        ];
    }

    public function map($row): array
    {
        return [
            $row->kategori,
            $row->produk,
            $row->jumlah_keluar,
            $row->harga_jual_rata2,
            $row->total_penjualan,
            $row->harga_modal_rata2,
            $row->total_modal,
            $row->keuntungan,
        ];
    }
}
