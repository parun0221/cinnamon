<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class HistoryStokProdukExport implements FromCollection, WithHeadings, WithMapping
{
protected $rows;
protected float $stok = 0;

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
            'Jenis',
            'Jumlah',
            'Stok',
            'Keterangan'
        ];
    }

    public function map($row): array
    {
        // 🔁 Hitung stok berjalan
        if ($row->jenis === 'masuk') {
            $this->stok += $row->jumlah;
        } elseif ($row->jenis === 'keluar') {
            $this->stok -= $row->jumlah;
        } elseif ($row->jenis === 'penyesuaian') {
            $this->stok += $row->jumlah;
        }

        // 🔖 Referensi transaksi
        if ($row->jenis === 'masuk') {
            $ref = 'PM-' . str_pad($row->produk_masuk_detail_id, 6, '0', STR_PAD_LEFT);
        } elseif ($row->jenis === 'keluar') {
            $ref = 'PK-' . str_pad($row->produk_keluar_detail_id, 6, '0', STR_PAD_LEFT);
        } else {
            $ref = 'RS-' . str_pad($row->id, 6, '0', STR_PAD_LEFT);
        }

        return [
            \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y H:i'),
            $ref,
            $row->produk->nama_produk ?? '-',
            $row->produk->kategori->nama_kategori ?? '-',
            ucfirst($row->jenis),
            $row->jumlah,
            $this->stok, // 🔥 STOK KUMULATIF
            $row->keterangan
        ];
    }

}
