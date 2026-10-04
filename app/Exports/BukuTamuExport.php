<?php

namespace App\Exports;

use App\Models\BukuTamuModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class BukuTamuExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
            return BukuTamuModel::all();
    
    }

     public function headings(): array
    {
        return [
            'No',
            'Nama',
            'No Telepon',
            'NRP/NIK',
            'Instansi',
            'Keperluan',
            'Waktu Kedatangan',
        ];
    }

    public function map($tamu) : array 
    {
        return [
            $tamu->no,
            $tamu->nama,
            $tamu->no_telp,
            $tamu->nrp,
            $tamu->instansi,
            $tamu->keperluan,
            $tamu->created_at ? $tamu->created_at->format('d-m-Y H:i') : '-',

        ];
    }
}
