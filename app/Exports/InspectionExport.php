<?php

namespace App\Exports;

use DateTimeInterface;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InspectionExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private Collection $inspections,
        private array $columns,
    ) {
    }

    public function collection(): Collection
    {
        return $this->inspections;
    }

    public function headings(): array
    {
        return array_keys($this->columns);
    }

    public function map($inspection): array
    {
        return array_map(
            fn (string $field) => $this->value($inspection, $field),
            array_values($this->columns),
        );
    }

    private function value($inspection, string $field): mixed
    {
        if ($field === 'approval_status') {
            return $inspection->approved_at ? 'Approved' : 'Menunggu GL';
        }

        if ($field === 'team_members') {
            return collect($inspection->tim_pelaksana ?? [])->pluck('nama')->filter()->join(', ');
        }

        $value = data_get($inspection, $field);

        if ($value instanceof DateTimeInterface) {
            return $value->format('d-m-Y');
        }

        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        return $value;
    }
}
