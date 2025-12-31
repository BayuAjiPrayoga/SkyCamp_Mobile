<?php

namespace App\Exports;

use App\Models\Peralatan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PeralatanExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Peralatan::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'Nama Alat',
            'Kategori',
            'Stok Total',
            'Harga Sewa/Malam',
            'Kondisi',
            'Deskripsi',
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        static $number = 0;
        $number++;

        return [
            $number,
            $row->nama,
            ucfirst($row->kategori),
            $row->stok_total,
            'Rp ' . number_format($row->harga_sewa, 0, ',', '.'),
            $this->formatKondisi($row->kondisi),
            $row->deskripsi ?? '-',
        ];
    }

    /**
     * Format kondisi label
     */
    private function formatKondisi(?string $kondisi): string
    {
        return match ($kondisi) {
            'baik' => 'Baik',
            'perlu_perbaikan' => 'Perlu Perbaikan',
            'rusak' => 'Rusak',
            default => '-',
        };
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
