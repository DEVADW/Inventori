<?php

namespace App\Exports;

use App\Models\InventarisModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InventarisExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
        return InventarisModel::all();
    }

    public function headings() : array
    {
        return [
            'No',
            'Nama',
            'NRP',
            'Nama Asset',
            'No Asset',
            'status peminjaman',
            'tanggal peminjaman',
            'tanggal pengembalian',
        ];
    }

    public function map($inventaris) : array
     {
        return [$inventaris->no, $inventaris->nama_asset, $inventaris->nrp, $inventaris->nama_asset, $inventaris->no_asset, $inventaris->status_peminjaman, $inventaris->tanggal_peminjaman, $inventaris->tanggal_pengembalian];
    }
}
