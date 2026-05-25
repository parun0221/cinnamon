<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class HistoryProdukMasukExport implements FromCollection, WithHeadings, WithMapping
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
            'Tanggal Masuk',
            'ID',
            'Produk',
            'Kategori',
            'Qty',
            'Harga Satuan',
            'Subtotal',
            'Status'
        ];
    }

    public function map($row): array
    {
        return [
            $row->produkMasuk->tanggal_masuk ?? '-',
            'PM-' . str_pad($row->id, 6, '0', STR_PAD_LEFT),
            $row->produk->nama_produk ?? '-',
            $row->produk->kategori->nama_kategori ?? '-',
            $row->status === 'pending'
                ? $row->berat_awal
                : $row->berat_final,
            $row->harga_satuan,
            $row->subtotal,
            $row->status
        ];
    }
}
