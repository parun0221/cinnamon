<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;


class HistoryProdukKeluarExport implements FromCollection, WithHeadings, WithMapping
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
            'Tanggal',
            'ID',
            'Produk',
            'Kategori',
            'Tujuan',
            'Qty',
            'Harga Satuan',
            'Subtotal',
        ];
    }

    public function map($row): array
    {
        return [
            $row->produkKeluar->tanggal_keluar ?? '-',
            'PK-' . str_pad($row->id, 6, '0', STR_PAD_LEFT),
            $row->produk->nama_produk ?? '-',
            $row->produk->kategori->nama_kategori ?? '-',
            $row->produkKeluar->supplier->nama_supplier ?? '-',
            $row->jumlah,
            $row->harga_satuan,
            $row->subtotal,
        ];
    }
}
