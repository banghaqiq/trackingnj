<?php

namespace App\Exports;

use App\Models\Paket;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PaketLaporanExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private readonly Builder $query)
    {
    }

    public function query(): Builder
    {
        return $this->query->with(['wilayah', 'asrama', 'diterimaOleh', 'diantarOleh']);
    }

    public function headings(): array
    {
        return [
            'Kode Resi',
            'Nama Penerima',
            'Wilayah',
            'Asrama',
            'Status',
            'Tanggal Diterima',
            'Tanggal Diambil',
            'Diterima Oleh',
            'Diantar Oleh',
        ];
    }

    /**
     * @param  Paket  $row
     */
    public function map($row): array
    {
        return [
            $row->kode_resi,
            $row->nama_penerima,
            $row->wilayah?->nama,
            $row->asrama?->nama,
            $row->status?->label() ?? (string) $row->status,
            $row->tanggal_diterima?->format('Y-m-d'),
            $row->tanggal_diambil?->format('Y-m-d'),
            $row->diterimaOleh?->name,
            $row->diantarOleh?->name,
        ];
    }
}
