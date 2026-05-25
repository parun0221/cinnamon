<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanProdukMasukExport implements FromCollection, WithHeadings, WithMapping
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
            'Total Berat (kg)',
            'Rata Harga Satuan',
            'Total Nilai',
            'Stok Pending',
            'Stok Siap',
        ];
    }

    public function map($row): array
    {
        return [
            $row->kategori,
            $row->produk,
            $row->total_berat_awal + $row->total_berat_final,
            $row->rata_harga_satuan,
            $row->total_nilai,
            $row->pending,
            $row->siap,
        ];
    }
}
